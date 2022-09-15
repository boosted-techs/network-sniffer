package main

import (
	"fmt"
	"log"
	"net"

	// "os/exec"
	// "runtime"
	// "strings"

	"database/sql"
	_ "github.com/go-sql-driver/mysql"
	"github.com/google/gopacket"
	"github.com/google/gopacket/pcap"

	//"strings"
	"time"
)

var (
	device       string = "en0"
	snapshot_len int32  = 1024
	promiscuous  bool   = false
	err          error
	timeout      time.Duration = 30 * time.Second
	handle       *pcap.Handle
)

func main() {
	currentTime := time.Now()
	fmt.Printf("\t \t Sneaky Network Monitor. [ %d-%d-%d] [%d:%d:%d]\n", currentTime.Year(), currentTime.Month(), currentTime.Day(), currentTime.Hour(),
		currentTime.Hour(),
		currentTime.Second())
	//localAddresses()
	//findDevices()
	SqlAddNIC()
	//liveCapture()
}

func dbConnect() *sql.DB {
	db, err := sql.Open("mysql", "root:root@tcp(127.0.0.1:3306)/sneaky")
	if err != nil {
		panic(err.Error())
	}
	//defer db.Close()
	return db
}

func SqlAddNIC() {
	db := dbConnect()
	nic := findDevices()
	//fmt.Println(nic)
	//fmt.Println(connectResource)
	for _, row := range nic {
		if _, ok := row["ip"]; !ok {
			row["ip"] = "IP Not set"
		}
		if _, ok := row["mask"]; !ok {
			row["mask"] = "MASK Not set"
		}
		// for keys, value := range row {
		// 	fmt.Printf("%s %s \n", keys, value)
		// }
		//fmt.Println(row)
		// _, err := db.Query("INSERT INTO interfaces (interface, ipv4, ipv6, subnet, description) value('" + row["deviceName"] + "', '" + row["ip"] + "', '...', '" + row["mask"] + "', '" + row["description"] + "')")
		// if err == nil {
		// 	panic(err.Error())
		// }
		db.Close()

		//fmt.Println(err)

	}
	//defer insert.Close()
}

func findDevices() map[int]map[string]string {
	// Find all devices
	devices, err := pcap.FindAllDevs()
	if err != nil {
		log.Fatal(err)
	}

	// Print device information
	//fmt.Println("Devices found:")
	//var arr = [...]string{}
	var _devices = make(map[int]map[string]string)
	i := 0
	for _, device := range devices {
		//fmt.Println("\nName: ", device.Name)
		if _devices[i] == nil {
			_devices[i] = make(map[string]string)
		}
		_devices[i]["deviceName"] = device.Name
		_devices[i]["description"] = device.Description
		//fmt.Println("\nName: ", device.)
		//fmt.Println("Description: ", device.Description)
		//fmt.Println("Devices addresses: ", device.Description)
		for _, address := range device.Addresses {
			fmt.Println(address)
			//fmt.Println("- IP address: ", address.IP)
			//fmt.Println("- Subnet mask: ", address.Netmask)
			_devices[i]["ip"] = address.IP.String()
			_devices[i]["mask"] = address.Netmask.String()
			_devices[i]["defaultMask"] = address.IP.DefaultMask().String()
		}
		i++
		//arr[i] = _devices
	}
	//fmt.Println(_devices)
	return _devices
}

func liveCapture() {
	// Open device
	handle, err = pcap.OpenLive(device, snapshot_len, promiscuous, timeout)
	if err != nil {
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
