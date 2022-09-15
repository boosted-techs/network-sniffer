package main

import (
	"context"
	"flag"
	"fmt"
	"github.com/sirupsen/logrus"
	"github.com/timest/gomanuf"
	"net"
	"os"
	"sort"
	"strings"
	"sync"
	"time"
)

var log = logrus.New()

// ipNet stores IP address and subnet mask
var ipNet *net.IPNet

// The mac address of the machine, which needs to be used to send Ethernet packets
var localHaddr net.HardwareAddr
var iface string

// Store the final data, key[string] stores the IP address
var data map[string]Info

// Timer, when no new data is written to data for a period of time, exit the program, otherwise reset the timer
var t *time.Ticker
var do chan string

const (
	// 3 second timer
	START = "start"
	END   = "end"
)

type Info struct {
	// IP address
	Mac net.HardwareAddr
	// CPU name
	Hostname string
	// manufacturer information
	Manuf string
}

// formatted output
// xxx.xxx.xxx.xxx  xx:xx:xx:xx:xx:xx  hostname  manuf
// xxx.xxx.xxx.xxx  xx:xx:xx:xx:xx:xx  hostname  manuf
func PrintData() {
	var keys IPSlice
	for k := range data {
		keys = append(keys, ParseIPString(k))
	}
	sort.Sort(keys)
	for _, k := range keys {
		d := data[k.String()]
		mac := ""
		if d.Mac != nil {
			mac = d.Mac.String()
		}
		fmt.Printf("IPV4:%-15s MacAddress:%-17s ComputerName:%-30s Manufacturer:%-10s\n", k.String(), mac, d.Hostname, d.Manuf)
	}
}

// Add the captured dataset to data and reset the timer at the same time
func pushData(ip string, mac net.HardwareAddr, hostname, manuf string) {
	// stop timer
	do <- START
	var mu sync.RWMutex
	mu.RLock()
	defer func() {
		// reset timer
		do <- END
		mu.RUnlock()
	}()
	if _, ok := data[ip]; !ok {
		data[ip] = Info{Mac: mac, Hostname: hostname, Manuf: manuf}
		return
	}
	info := data[ip]
	if len(hostname) > 0 && len(info.Hostname) == 0 {
		info.Hostname = hostname
	}
	if len(manuf) > 0 && len(info.Manuf) == 0 {
		info.Manuf = manuf
	}
	if mac != nil {
		info.Mac = mac
	}
	data[ip] = info
}

func setupNetInfo(f string) {
	var ifs []net.Interface
	var err error
	if f == "" {
		ifs, err = net.Interfaces()
	} else {
		// iface has been selected
		var it *net.Interface
		it, err = net.InterfaceByName(f)
		if err == nil {
			ifs = append(ifs, *it)
		}
	}
	if err != nil {
		log.Fatal("Unable to get local network information:", err)
	}
	for _, it := range ifs {
		addr, _ := it.Addrs()
		for _, a := range addr {
			if ip, ok := a.(*net.IPNet); ok && !ip.IP.IsLoopback() {
				if ip.IP.To4() != nil {
					ipNet = ip
					localHaddr = it.HardwareAddr
					iface = it.Name
					goto END
				}
			}
		}
	}
END:
	if ipNet == nil || len(localHaddr) == 0 {
		log.Fatal("Unable to get local network information")
	}
}

func localHost() {
	host, _ := os.Hostname()
	data[ipNet.IP.String()] = Info{Mac: localHaddr, Hostname: strings.TrimSuffix(host, ".local"), Manuf: manuf.Search(localHaddr.String())}
}

func sendARP() {
	// ips is a collection of intranet IP addresses
	ips := Table(ipNet)
	for _, ip := range ips {
		go sendArpPackage(ip)
	}
}

func main() {
	// allow non root user to execute by compare with euid
	if os.Geteuid() != 0 {
		log.Fatal("Sneaky must run as root.")
	}
	fmt.Println("Welcome to Sneaky Network Management tool CLI")
	flag.StringVar(&iface, "I", "", "Network interface name")
	flag.Parse()
	// initialize data
	data = make(map[string]Info)
	do = make(chan string)
	// Initialize network information
	setupNetInfo(iface)

	ctx, cancel := context.WithCancel(context.Background())
	go listenARP(ctx)
	go listenMDNS(ctx)
	go listenNBNS(ctx)
	go sendARP()
	go localHost()

	t = time.NewTicker(4 * time.Second)
	for {
		select {
		case <-t.C:
			PrintData()
			cancel()
			goto END
		case d := <-do:
			switch d {
			case START:
				t.Stop()
			case END:
				// New data received, 2 seconds counter reset
				t = time.NewTicker(2 * time.Second)
			}
		}
	}
END:
}
