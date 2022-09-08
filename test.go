package main

import (
	"bytes"
	"fmt"
	"io"
	"net"

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
	conn, err := net.Dial("tcp", "google.com:80")
	if err != nil {
		fmt.Println("dial error:", err)
		return
	}
	defer conn.Close()
	fmt.Fprintf(conn, "GET / HTTP/1.0\r\n\r\n")
	var buf bytes.Buffer
	io.Copy(&buf, conn)
	fmt.Println("total size:", buf.Len())

}
