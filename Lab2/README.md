## LAB 2 - Database VM
# Description

This lab extends the previous setup by introducing a second virtual machine acting as a database server. The web server now runs a PHP application that connects to a MySQL database and logs visitor data.

The environment demonstrates VM-to-VM communication over a private network and basic backend integration.

# Objective

- Create and configure two virtual machines using Vagrant
- Set up VM-to-VM communication via a private network
- Install and configure a MySQL database server
- Connect a PHP web application to a database
- Log and display dynamic data (visitor tracking)

# Tools Required
- Vagrant
- VirtualBox
- A code editor (e.g., VS Code)

# Configuration
*Web Server VM*
- OS: Ubuntu (jammy64)
- RAM: 512 MB
- CPU: 1 core
- Port forwarding: Host 8080 → Guest 80
- Private IP: 192.168.56.10

*Database Server VM*
- OS: Ubuntu (jammy64)
- RAM: 1024 MB
- CPU: 1 core
- Private IP: 192.168.56.11

# How to run
- Open PowerShell
- Navigate to the lab folder:
cd lab2
Start the virtual machines:
vagrant up
(Optional) Connect to a VM:
vagrant ssh web
vagrant ssh db
Open your browser and go to:
http://localhost:8080
Files
Vagrantfile – Defines and configures both virtual machines
index.php – PHP application that connects to the database and logs visits
Provisioning
Web Server automatically:
Updates package lists
Installs:
Apache2
PHP
MySQL client
Removes default index.html
Deploys custom index.php from synced folder
Starts and enables Apache
Database Server automatically:
Installs MySQL server
Configures MySQL to listen on private network
Creates database: webdb
Creates table: visits
Creates user:
Username: testuser
Password: testpass
Grants access only from web server IP (192.168.56.10)
Application Behavior

The PHP application:

Connects to the database
Logs each visitor:
Timestamp
Server name
IP address
Displays the 5 most recent visits in the browser
Notes
This lab introduces client-server architecture inside a virtualized environment
The database is not exposed to the host machine (no port forwarding)
Communication happens only via the private network
This is a first step toward network segmentation and secure architecture
Key Learning Takeaways
How services communicate across VMs
Difference between local vs network-based services
Basic database integration in a web application
Foundations for 3-tier architecture and security design
