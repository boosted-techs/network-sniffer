package main

import (
	// "bytes"
	"fmt"
	//"io"
	//"net"
	"golang.org/x/sys/unix"
	"runtime"
)

func main() {
	fmt.Println("Hello Ashan")
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
	var uname unix.Utsname
	if err := unix.Uname(&uname); err == nil {
		// extract members:
		// type Utsname struct {
		//  Sysname    [65]int8
		//  Nodename   [65]int8
		//  Release    [65]int8
		//  Version    [65]int8
		//  Machine    [65]int8
		//  Domainname [65]int8
		// }

		fmt.Println(int8ToStr(uname.Sysname[:]),
			int8ToStr(uname.Release[:]),
			int8ToStr(uname.Version[:]))
	}
}

func int8ToStr(arr []int8) string {
	b := make([]byte, 0, len(arr))
	for _, v := range arr {
		if v == 0x00 {
			break
		}
		b = append(b, byte(v))
	}
	return string(b)
}
