## Lab 1,5 – Web Server with Synced Folder (Vagrant)

### Description

This lab sets up a virtual machine using Vagrant with Ubuntu as the operating system. The VM runs an Apache2 web server and uses a synced folder to deploy a custom web page.

### Objective
Create and configure a virtual machine using Vagrant
Set up port forwarding
Use synced folders to share files between host and VM
Automatically install and configure a web server

### Tools Required
- Vagrant
- VirtualBox
- A code editor (e.g., VS Code)

### Configuration
OS: Ubuntu (jammy64)
VM Name: webserver_vm1
RAM: 512 MB
- CPU: 1 core
- Port forwarding: Host 8080 → Guest 80
- Synced folder: Local project folder → /vagrant inside VM

### How to run
 - Open PowerShell

- Navigate to the lab folder:
  cd lab

- Start the virtual machine:
  vagrant up

- (Optional) Connect to the VM:
  vagrant ssh

- Open your browser and go to:
  http://localhost:8080
### Files
Vagrantfile – Defines and configures the virtual machine
index.html – Custom web page deployed to the server

### Provisioning
The VM automatically:

- Updates package lists
- Installs Apache2
- Enables and starts the web server
- Copies a custom index.html from the synced folder to /var/www/html/

### Notes

This lab demonstrates how to use synced folders in Vagrant to deploy custom content to a web server automatically
