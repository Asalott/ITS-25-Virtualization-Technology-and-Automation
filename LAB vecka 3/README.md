# Lab 3 – Multi-VM Environment with Network Segmentation (Vagrant)

## Description

This lab builds a multi-machine virtual environment using Vagrant. 
Two virtual machines are created:

- A **web server**
- A **database server**

The lab demonstrates **network segmentation**, where only the web server is exposed externally, while the database is isolated and protected.

## Objective

- Create a multi-VM environment using Vagrant
- Configure private networking between VMs
- Implement basic network segmentation
- Install and configure Apache (web server)
- Install and secure PostgreSQL (database server)
- Understand security concepts like **least privilege** and **defense in depth**

## Tools Required

- Vagrant
- VirtualBox
- Terminal (PowerShell / Bash)

## Configuration

### General
- OS: Ubuntu (jammy64)
- RAM per VM: 512 MB
- CPU per VM: 1 core

### Network Setup
- Private network: `192.168.56.0/24`

| Machine      | IP Address         | Description |
|-----------------|-----------------------|------------|
| Web Server | 192.168.56.11    | Public-facing server |
| Database    | 192.168.56.12    | Internal-only server |

### Port Forwarding
- Host `8080` → Web server `80`
- **No port forwarding for database** (security)

## Architecture

### This lab simulates a simplified **2-tier architecture**:
[ Host Machine ] → localhost:8080 → [ Web Server ] 192.168.56.11 → [ Database ] 192.168.56.12

- Only the web server is externally accessible
- Database is only reachable internally

This reflects real-world segmentation (DMZ + internal network) :contentReference[oaicite:0]{index=0}

## How to Run

### 1. Start the environment: 
vagrant up

### 2. Check status: 
vagrant status

### 3. Access the web server: 
Open browser: 
http://localhost:8080
or
http://192.168.56.11

## Accessing the Virtual Machines
### Connect to web server:
vagrant ssh webbserver

### Connect to database server:
vagrant ssh database

## Provisioning
###  Web Server
The web server automatically:
* Updates package lists
* Installs Apache2, ping, and netcat
* Enables and starts Apache
* Deploys a simple HTML page

###  Database Server
The database server automatically:
* Installs PostgreSQL and UFW
* Enables and starts PostgreSQL
* Configures PostgreSQL to listen on all interfaces
* Applies firewall rules

### Firewall configuration:
* ufw default deny incoming
* ufw default allow outgoing
* ufw allow ssh
* ufw allow from 192.168.56.11 to any port 5432
* ufw --force enable
* Only the web server can access the database on port 5432

## Network Segmentation
###  Without segmentation
All systems can communicate freely
A compromised system can access all others
###  With segmentation
The database server is isolated
Only the web server can access the database
External access is limited to the web server

## Verification
### Test web server:
http://localhost:8080

### Test connectivity from web server:
vagrant ssh webbserver →
ping 192.168.56.12

### Test database port:
nc -zv 192.168.56.12 5432
- Works from web server
- Blocked from host machine

## Troubleshooting
### Check PostgreSQL status:
systemctl status postgresql
### Check firewall rules:
sudo ufw status
### Check PostgreSQL configuration:
sudo -u postgres psql -c "SHOW listen_addresses;"

## Key Concepts
* Multi-VM environments
* Private networking
* Network segmentation
* Firewall configuration (UFW)
* Infrastructure as Code

## Security Principles
### Least privilege
Database only accessible from web server
### Defense in depth
Network + firewall + service configuration
### Isolation
Separate roles per VM

## Notes
- Vagrant adds a NAT adapter for SSH access
- Host can reach private network unless restricted
- Firewall is required for proper isolation

## Conclusion
This lab demonstrates how to build a simple and secure environment using Vagrant.
By separating services and controlling communication, the attack surface is reduced and security is improved.

