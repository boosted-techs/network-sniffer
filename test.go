package main

import (
	//"bytes"
	"fmt"
	//"io"
	"log"
	"net"
	"runtime"
	"strings"
)

func main() {
	var operatingSystem string
	switch runtime.GOOS {
	case "windows":
		operatingSystem = "Windows operating system"
	case "darwin":
		operatingSystem = "Machitosh OS"
	case "linux":
		operatingSystem = "Linux"

	default:
		operatingSystem = "UNKOWN OS"

	}
	fmt.Println("OS: " + operatingSystem)
	// conn, err := net.Dial("tcp", "google.com:80")
	// if err != nil {
	// 	fmt.Println("dial error:", err)
	// 	return
	// }
	// defer conn.Close()
	// fmt.Fprintf(conn, "GET / HTTP/1.0\r\n\r\n")
	// var buf bytes.Buffer
	// io.Copy(&buf, conn)
	// fmt.Println("total size:", buf.Len())
	networkInterfaceDetails()

}

func networkInterfaceDetails() {
	var count int

	ifaces, err := net.Interfaces()
	if err != nil {
		log.Print(fmt.Errorf("localAddresses: %v\n", err.Error()))
		return
	}

	for _, i := range ifaces {
		addrs, err := i.Addrs()
		if err != nil {
			log.Print(fmt.Errorf("localAddresses: %v\n", err.Error()))
			continue
		}

		for _, a := range addrs {
			log.Printf("Index:%d Name:%v addr:%v, mac:%v\n", i.Index, i.Name, a, i.HardwareAddr)
			if strings.Contains(i.Flags.String(), "up") {
				fmt.Println("Status : UP")
			} else {
				fmt.Println("Status : DOWN")
			}
			if i.Index == 4 {
				getInteraceDetails(i.Name)
			}
		}
		count++
	}
	fmt.Println("Total interfaces : ", count)
}

func getInteraceDetails(interfaceName string) {
	ief, err := net.InterfaceByName(interfaceName)
	if err != nil {
		log.Fatal(err)
	}
	addrs, err := ief.Addrs()
	if err != nil {
		log.Fatal(err)
	} else {
		fmt.Println("Network Interface:", interfaceName, "IPV6:", addrs[0], "IPV4:", addrs[1])
		//fmt.Println("IPV4", addrs[1])
	}
}
