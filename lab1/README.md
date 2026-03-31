## Lab 1 – Web Server with Vagrant

### Description
This lab sets up a virtual machine using Vagrant with Ubuntu as the operating system. The VM is configured to run a web server (Apache2) with port forwarding.

### Objective
- Create and configure a virtual machine using Vagrant
- Set up port forwarding
- Automatically install and run a web server

### Tools Required
- Vagrant
- VirtualBox
- A code editor (e.g., VS Code)

### Configuration
- OS: Ubuntu (jammy64)
- VM Name: webserver
- RAM: 512 MB
- CPU: 1 core
- Port forwarding: Host 8080 → Guest 80

### How to run

1. Open PowerShell

2. Navigate to the lab folder:
cd lab1

3. Start the virtual machine:
vagrant up

4. (Optional) Connect to the VM:
   vagrant ssh

5. Open your browser and go to:
http://localhost:8080

### Files
Vagrantfile – Defines and configures the virtual machine

### Provisioning
The VM automatically:
- Updates package lists
- Installs Apache2

### Notes
This lab demonstrates how to automate the setup of a web server using Vagrant and shell provisioning.
