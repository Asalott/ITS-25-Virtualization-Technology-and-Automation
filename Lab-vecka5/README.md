# Lab-Vecka5 ReadMe

## Lab 6 – Multi-VM Environment with Ansible Automation

### Description
This lab extends the previous multi-VM environment by introducing a third virtual machine acting as an Ansible control node.

The environment now consists of:

* A control node (Ansible)
* A web server
* A database server

The control node uses SSH key-based authentication to manage the other machines and executes Ansible commands to automate configuration and verify connectivity.

### Objective
* Create a 3-VM environment using Vagrant
* Configure SSH key-based authentication between machines
* Install and use Ansible on a control node
* Understand basic Ansible architecture (control node, inventory)
* Execute Ansible commands to manage multiple servers
  
### Tools Required
* Vagrant
* VirtualBox
* A code editor (e.g., VS Code)
  
### Configuration
* OS: Ubuntu (jammy64)
* VMs:
  Control node: 192.168.56.10
  Web server: 192.168.56.11
  Database server: 192.168.56.12
* RAM: 512 MB per VM
* CPU: 1 core per VM
* Port forwarding:
  Web server: Host 8080 → Guest 80
  
### How to run
1. Open PowerShell
2. Navigate to the lab folder:
cd C:\vagrant-lab\vecka4
3. Start the environment:
vagrant up
4. Connect to the control node:
vagrant ssh control

### Files
* Vagrantfile – Defines all three virtual machines
* ansible_id_ed25519.pub – Public SSH key shared between machines
* inventory.ini – Ansible inventory (created during the lab)
  
## Provisioning
### Control Node
* Installs Ansible
* Generates an SSH key pair (ed25519)
* Shares the public key via /vagrant
  
### Web Server
* Installs Apache2
* Waits for the control node’s public key
* Adds the key to authorized_keys for SSH access
  
### Database Server
* Installs PostgreSQL
* Configures firewall (UFW)
* Allows controlled access from internal network
* Adds control node SSH key for Ansible access

## Ansible Setup

Inside the control node:

### 1. Create an inventory file:
[webservers]
webserver ansible_host=192.168.56.11

[databases]
database ansible_host=192.168.56.12

[all:vars]
ansible_user=vagrant
ansible_ssh_private_key_file=~/.ssh/id_ed25519
ansible_python_interpreter=/usr/bin/python3

### 2. Test connectivity:
ansible all -i inventory.ini -m ping

Expected result:
All hosts respond with "pong"

## Key Concepts
### SSH Key Authentication
* Public key is stored on target machines
* Private key remains on control node
* Enables secure, password-free automation

### Ansible Control Node
* Central machine where Ansible is installed
* Executes commands over SSH
* No agent required on target machines

### Idempotency
* Running the same command multiple times produces the same result
* Ensures consistent system configuration

### Notes
* Vagrant may start machines in parallel, so waiting for the SSH key is necessary
* Correct file permissions are required for SSH to work
* This lab demonstrates the foundation for automated infrastructure management

## Summary

This lab demonstrates how to:

* Expand a virtualized environment to multiple machines
* Secure access using SSH keys
* Automate system management with Ansible

It forms the basis for scalable and repeatable infrastructure automation.

## Reflektionsfrågor

### Varför ändrar vi aldrig filer direkt på kontrollnoden? Vad händer om någon gör det?
Gör man ändringar i kontrollnoden så kan den inte pusha upp ändringarna som gjorts till Github. Kontrollnoden är bara till för att kontrollera/testa filerna.

### Varför läggs secrets.yml i vagrant/-mappen istället för att committas till Git?
Secrets-mappen innehåller känslig data så som lösenord och användarnamn till t.ex. en DB eller Webserver. Om nån ska ladda ner repo:t 

### Vad är skillnaden mellan git pull och git fetch origin?
git fetch origin: Hämtar och tittar
git pull: hämtar och mergar i nuvarande branch

### Om din kollega pushar en ändring till main medan du arbetar på din branch — påverkar det din branch? Varför eller varför inte?
Det kommer inte påverka min branch direkt efter som det är sin egna version av repon som inte blir efekted av main om jag inte själv väljer att uppdatera den. Det kan forfarande bli conflicter när jag fösöker fetcha eller pull från main eller när jag försöker merga med main.

### Vad är skillnaden mellan kontrollnodens SSH-nyckel och Windows-datorns SSH-nyckel? Varför behöver båda vara tillagda på GitHub?
Efter som ssh nyckeln gäller bara för en enhet och kontrollnoden räknas som sin egen eneht separat från windos-datorn så behöver kontrollnoden sin egna ssh nyckel för att komma åt github.
