package main

import (
	"fmt"
	"log"
	"net"
	"os"
	"strconv"
	"strings"

	// "os/exec"
	// "runtime"
	"database/sql"
	"encoding/hex"

	_ "github.com/go-sql-driver/mysql"
	"github.com/google/gopacket"
	"github.com/google/gopacket/layers"
	"github.com/google/gopacket/pcap"
	"github.com/google/gopacket/pcapgo"

	"flag"
	"time"
)

var (
	device       string = "en0"
	snapshot_len int32  = 1024
	promiscuous  bool   = false
	err          error
	timeout      time.Duration = 30 * time.Second
	handle       *pcap.Handle
	packetCount  int    = 0
	pcapFile     string = "test.pcap"
)

func main() {
	if os.Geteuid() != 0 {
		fmt.Println("Sneak must run as root")
		log.Fatal("Error")
	}
	/*
		Let set out default structure to be used with in the software
	*/

	//localAddresses()
	//findDevices()
	//connection := SqlAddNIC()
	//fmt.Println(connection)
	//liveCapture()
	wordPtr := flag.String("sneaky", "", "Pass any argument to Sneaky: USAGE -sneaky=0")
	writePacs := flag.String("wp", "", "Argument p stands for write live packets to pcap file: USAGE:  -sneaky=0 -wp=en0 where en0 is network interface/device")
	liveCapture_ := flag.String("lc", "", "capture live packets from network card USAGE: -sneaky=0 -lc=en0 where en0 is network interface/device")
	readPackets := flag.String("rp", "", "Open provided network file for reading. USAGE:  -sneaky=0 -rp=en0 where en0 is network interface/device")
	filterPackets := flag.String("fp", "", "Filter packers for given interface. USAGE :  -sneaky=0 -fp=en0 where en0 is network interface/device")
	decodePackets := flag.String("dp", "", "Filter packers for given interface. USAGE:  -sneaky=0 -dp=en0")
	discoverDevices := flag.String("nic", "", "Discover Devices USAGE:  -sneaky=0 -nic where en0 is network interface/device")
	promise_ := flag.Bool("pm", false, "To turn on promiscious mode, turn it true. USAGE: -sneaky=0 pm=true")
	// readFilePacs := flag.String("sneakyread", "11", "")
	flag.Parse()
	if len(*wordPtr) > 0 {
		var execute bool = false //Evaluates to false if no arguments are supplied
		/*			Discover new devices on the network */
		if len(*discoverDevices) > 0 {
			fmt.Println(SqlAddNIC())
			execute = true
		}

		if *promise_ {
			/*
				Turning on Promiscious mode
			*/
			*&promiscuous = *promise_
			execute = true
		}

		/*			lc = live capture		*/
		if len(*liveCapture_) > 0 {
			*&device = *liveCapture_
			liveCapture()
			execute = true
		}
		if len(*writePacs) > 0 {
			/*				wp	write packets to pcap file		*/
			*&device = *writePacs
			writePcapFile()
			execute = true
		}
		if len(*readPackets) > 0 {
			/*				rp		read packets from pcap file	*/
			*&pcapFile = *readPackets + ".pcap"
			readPacketsFromFile()
			execute = true
		}

		if len(*filterPackets) > 0 {
			/*	fp Filter packets */
			*&pcapFile = *filterPackets + ".pcap"
			*&device = *filterPackets
			_filterPackets()
			execute = true
		}

		if len(*decodePackets) > 0 {
			/*	fp Filter packets */
			*&device = *decodePackets
			handle, err = pcap.OpenLive(device, snapshot_len, promiscuous, timeout)
			if err != nil {
				log.Fatal(err)
			}
			defer handle.Close()

			packetSource := gopacket.NewPacketSource(handle, handle.LinkType())
			for packet := range packetSource.Packets() {
				printPacketInfo(packet)
			}
			execute = true
		}

		if !execute {
			fmt.Println("No Arguments passed")
		}
	} else {
		fmt.Println("Welcome to Sneaky commandline")
	}
}

func tryChanging() {
	fmt.Println(device)
}

func getDate() string {
	currentTime := time.Now()
	return fmt.Sprintf("%d-%d-%d]", currentTime.Year(), currentTime.Month(), currentTime.Day())
}
func dbConnect() *sql.DB {
	db, err := sql.Open("mysql", "root:root@tcp(127.0.0.1:3306)/sneaky")
	if err != nil {
		panic(err.Error())
	}
	//defer db.Close()
	return db
}

func SqlAddNIC() string {
	db := dbConnect()
	nic := findDevices() //nic refers to network interface card. This variable contains the available network interfaces
	for _, row := range nic {
		if _, ok := row["ip"]; !ok {
			row["ip"] = ""
		}
		if _, ok := row["mask"]; !ok {
			row["mask"] = ""
			row["defaultMask"] = ""
		}

		db.Query("INSERT INTO interfaces (interface, ipv4, ipv6, subnet, description, defaultMask, date_added) value('" + row["deviceName"] + "', '" + row["ip"] + "', '...', '" + row["mask"] + "', '" + row["description"] + "', '" + row["defaultMask"] + "', '" + getDate() + "')")
		//fmt.Println(err)

	}
	return "Activity ran"
	// defer insert.Close()
}

func findDevices() map[int]map[string]string {
	// Find all devices
	devices, err := pcap.FindAllDevs()
	if err != nil {
		log.Fatal(err)
	}
	var _devices = make(map[int]map[string]string)
	i := 0
	for _, device := range devices {

		if _devices[i] == nil {
			_devices[i] = make(map[string]string)
		}
		_devices[i]["deviceName"] = device.Name
		_devices[i]["description"] = device.Description

		for _, address := range device.Addresses {

			_devices[i]["ip"] = address.IP.String()
			if address.Netmask != nil {
				_devices[i]["mask"] = address.Netmask.String()
				a, _ := hex.DecodeString(_devices[i]["mask"])
				_string := make([]string, 0, len(a)/2)
				for g, subNet := range a {
					if g <= 3 {
						_string = append(_string, strconv.Itoa(int(subNet)))
					}
				}
				_devices[i]["defaultMask"] = strings.Join(_string, ".")
			}
		}
		i++
	}
	//fmt.Println(_devices)
	return _devices
}

func liveCapture() {
	// Open device
	handle, err = pcap.OpenLive(device, snapshot_len, promiscuous, timeout)
	if err != nil {
		fmt.Println(err)
		log.Fatal(err)
	}
	defer handle.Close()

	// Use the handle as a packet source to process all packets
	packetSource := gopacket.NewPacketSource(handle, handle.LinkType())
	for packet := range packetSource.Packets() {
		// Process packet here
		fmt.Println(packet)
	}
}

func writePcapFile() {
	// Open output pcap file and write header
	f, _ := os.Create(device + ".pcap")
	w := pcapgo.NewWriter(f)
	w.WriteFileHeader(uint32(snapshot_len), layers.LinkTypeEthernet)
	defer f.Close()

	// Open the device for capturing
	handle, err = pcap.OpenLive(device, snapshot_len, promiscuous, timeout)
	if err != nil {
		fmt.Printf("Error opening device %s: %v", device, err)
		os.Exit(1)
	}
	defer handle.Close()

	// Start processing packets
	packetSource := gopacket.NewPacketSource(handle, handle.LinkType())
	for packet := range packetSource.Packets() {
		// Process packet here
		fmt.Println(packet)
		w.WritePacket(packet.Metadata().CaptureInfo, packet.Data())
		packetCount++
		// Only capture 100 and then stop
		// if packetCount > 100 {
		// 	break
		// }
	}
}

func readPacketsFromFile() {
	// Open file instead of device
	handle, err = pcap.OpenOffline(pcapFile)
	if err != nil {
		log.Fatal(err)
	}
	defer handle.Close()

	// Loop through packets in file
	packetSource := gopacket.NewPacketSource(handle, handle.LinkType())
	for packet := range packetSource.Packets() {
		fmt.Println(packet)
	}
}

func _filterPackets() {
	// Open device
	handle, err = pcap.OpenLive(device, snapshot_len, promiscuous, timeout)
	//handle, err = pcap.OpenOffline(pcapFile)
	if err != nil {
		log.Fatal(err)
	}
	defer handle.Close()

	// Set filter
	var filter string = "tcp and port 80"
	err = handle.SetBPFFilter(filter)
	if err != nil {
		log.Fatal(err)
	}
	//fmt.Println("Only capturing TCP port 80 packets.")

	packetSource := gopacket.NewPacketSource(handle, handle.LinkType())
	for packet := range packetSource.Packets() {
		// Do something with a packet here.
		fmt.Println(packet)
	}
}
func localAddresses() {
	ifaces, err := net.Interfaces()
	if err != nil {
		fmt.Print(fmt.Errorf("localAddresses: %+v\n", err.Error()))
		return
	}
	for _, i := range ifaces {

		if i.Flags&net.FlagUp > 0 {
			fmt.Printf("%s is up\n", i.Name)

		}

		addrs, err := i.Addrs()
		if err != nil {
			fmt.Print(fmt.Errorf("localAddresses: %+v\n", err.Error()))
			continue
		}
		for _, a := range addrs {
			switch v := a.(type) {
			case *net.IPAddr:
				fmt.Printf("HardwareName: %v : %s (%s) MacAddress: %v\n", i.Name, v, v.IP.DefaultMask(), i.HardwareAddr)

			case *net.IPNet:
				fmt.Printf("HardwareName: %v : %s [%v] MASK:%v MacAddress: %v\n", i.Name, v, v.IP, v.Mask, i.HardwareAddr)
			}

		}
	}
}

func printPacketInfo(packet gopacket.Packet) {
	// Let's see if the packet is an ethernet packet
	ethernetLayer := packet.Layer(layers.LayerTypeEthernet)
	if ethernetLayer != nil {
		fmt.Println("Ethernet layer detected.")
		ethernetPacket, _ := ethernetLayer.(*layers.Ethernet)
		fmt.Println("Source MAC: ", ethernetPacket.SrcMAC)
		fmt.Println("Destination MAC: ", ethernetPacket.DstMAC)
		// Ethernet type is typically IPv4 but could be ARP or other
		fmt.Println("Ethernet type: ", ethernetPacket.EthernetType)
		fmt.Println()
	}

	// Let's see if the packet is IP (even though the ether type told us)
	ipLayer := packet.Layer(layers.LayerTypeIPv4)
	if ipLayer != nil {
		fmt.Println("IPv4 layer detected.")
		ip, _ := ipLayer.(*layers.IPv4)

		// IP layer variables:
		// Version (Either 4 or 6)
		// IHL (IP Header Length in 32-bit words)
		// TOS, Length, Id, Flags, FragOffset, TTL, Protocol (TCP?),
		// Checksum, SrcIP, DstIP
		fmt.Printf("From %s to %s\n", ip.SrcIP, ip.DstIP)
		fmt.Println("Protocol: ", ip.Protocol)
		fmt.Println()
	}

	// Let's see if the packet is TCP
	tcpLayer := packet.Layer(layers.LayerTypeTCP)
	if tcpLayer != nil {
		fmt.Println("TCP layer detected.")
		tcp, _ := tcpLayer.(*layers.TCP)

		// TCP layer variables:
		// SrcPort, DstPort, Seq, Ack, DataOffset, Window, Checksum, Urgent
		// Bool flags: FIN, SYN, RST, PSH, ACK, URG, ECE, CWR, NS
		fmt.Printf("From port %d to %d\n", tcp.SrcPort, tcp.DstPort)
		fmt.Println("Sequence number: ", tcp.Seq)
		fmt.Println()
	}

	// Iterate over all layers, printing out each layer type
	fmt.Println("All packet layers:")
	for _, layer := range packet.Layers() {
		fmt.Println("- ", layer.LayerType())
	}

	// When iterating through packet.Layers() above,
	// if it lists Payload layer then that is the same as
	// this applicationLayer. applicationLayer contains the payload
	applicationLayer := packet.ApplicationLayer()
	if applicationLayer != nil {
		fmt.Println("Application layer/Payload found.")
		fmt.Printf("%s\n", applicationLayer.Payload())

		// Search for a string inside the payload
		if strings.Contains(string(applicationLayer.Payload()), "HTTP") {
			fmt.Println("HTTP found!")
		}
	}

	// Check for errors
	if err := packet.ErrorLayer(); err != nil {
		fmt.Println("Error decoding some part of the packet:", err)
	}
}
