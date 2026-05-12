# ITS25-School-project-Load-balanced-Video-Streaming-Server
 The project implements a scalable video streaming service where clients connect to a load balancer that distributes traffic to multiple web servers. Each web server delivers video content, while a separate backend server stores media files. The architecture is partitioned to enable high availability and resource isolation.

## Toplogy for the project
![Topology](/Pictures/Topology.png)

# Branch- and patchnotes

## 01-added-vagrantfile
     - Created empty vagrant file
 
## 02-added-webserver-vm-1-and-2
     - Webserver 1 and 2 added to vagrant file
 
## 03-added-.gitignore
     - Added working .gitignore-file
 
## 04-added-rest-of-vm-ta-vafrantfile
     - Added control vm, Loadbaring vm, Database vm and streaming vm
 
## 05-asnible-file-strukter
     - Added ansible files needed for basic functions
 
     pic eller nått över filstrukturen idk
 
## 06-add-gitclone-to-vagrantfile
     - Added gitclone code to vagrant file
 
## 07-added-inventory.ini
     - Added inventory.ini with ip-adresses and ssh-key adress
     - # Added in vagrant file to pull branch 07
       git clone -b 07-added-inventory.ini https://github.com/A-Hagman/ITS25-School-project-Load-balanced-Video-Streaming-Server.git /home/vagrant/project
### Tested with:

**In powershell:**
 
*vagrant ssh control*
*vagrant@control:~$ cd "/home/vagrant/project/ansible"*
 
*vagrant@control:~/project/ansible$ ssh-keyscan -H 192.168.56.11 >> ~/.ssh/known_hosts*
*vagrant@control:~/project/ansible$ ssh-keyscan -H 192.168.56.12 >> ~/.ssh/known_hosts*
*vagrant@control:~/project/ansible$ ssh-keyscan -H 192.168.56.13 >> ~/.ssh/known_hosts*
*vagrant@control:~/project/ansible$ ssh-keyscan -H 192.168.56.14 >> ~/.ssh/known_hosts*
*vagrant@control:~/project/ansible$ ssh-keyscan -H 192.168.56.15 >> ~/.ssh/known_hosts*
 
*vagrant@control:~/project/ansible$ ansible all -i inventory.ini -m ping*
 
### Expected results:
 
192.168.56.11 | SUCCESS => {
    "ansible_facts": {
        "discovered_interpreter_python": "/usr/bin/python3"
    },
    "changed": false,
    "ping": "pong"
}

192.168.56.12 | SUCCESS => {
    "ansible_facts": {
        "discovered_interpreter_python": "/usr/bin/python3"
    },
    "changed": false,
    "ping": "pong"
}

192.168.56.13 | SUCCESS => {
    "ansible_facts": {
        "discovered_interpreter_python": "/usr/bin/python3"
    },
    "changed": false,
    "ping": "pong"
}

192.168.56.15 | SUCCESS => {
    "ansible_facts": {
        "discovered_interpreter_python": "/usr/bin/python3"
    },
    "changed": false,
    "ping": "pong"
}

192.168.56.14 | SUCCESS => {
    "ansible_facts": {
        "discovered_interpreter_python": "/usr/bin/python3"
    },
    "changed": false,
    "ping": "pong"
}

### Comment(s):
 
We had to ssh-keyscan to avoid the system asking "Are you sure you want to continue connecting (yes/no/[fingerprint])?" for each ping which caused the system to hang after the first ping. To avoid this we will add this code in the vagrant file after the key has been generated but before the ansible-playbook runs:
 
**Wait for each VM to come online before adding to known_hosts**   
for ip in 192.168.56.11 192.168.56.12 192.168.56.13 192.168.56.14 192.168.56.15; do
      echo "Väntar på $ip..."
      until nc -z -w 3 $ip 22; do
         sleep 3 
      done 
      ssh-keyscan -H $ip >> /home/vagrant/.ssh/known_hosts
    done
    chown vagrant:vagrant /home/vagrant/.ssh/known_hosts
 
 
We will also add netcat to automatically install to enable the loop to work:
 
**Installs Ansible and Github-git on boot up**      
apt-get install -y ansible git netcat-openbsd
 
 
Another solution would be to change the inventory.ini-file to not enable "StrictHostKeyChecking", which automatically accepts all hostkeys without asking. This would solve the same problem, but that would also compromise the security due to disabling the SSH-keyverification permanently. The known_host solution is saving the keys during start-up and if the keys changes och gets manupilated the SSH will alarm - which is much safer.
 
 
### UPDATE:
Due to problems with the loop not finding the VMs after creating the control node we changed to using vagrants SSH-keys instead of our own. This led to changes in both the vagrant file and the inventory.ini.
 
We removed the key-generating code in Vagrant aswell as moving the creation of the control to run last. The loop we added earlier we deemed unnecessary if we're working with vagrants own keys. We added paths to Vagrants keys in the inventory.ini.

### UPDATE 2:
The last changes did not work and after consulting the teacher we opted for "Host_key_checking = False" in the inventory.ini. This is not a "safe" method bud is will help us with the problem that occured when pinging. We also went back to using our own SSH-keys.

### UPDATE 3 (Final):
The "Host_key_checking = False" was not supposed to be in inventory.ini, it was supposed to be in the ansible.cfg. We moved the code to the right file. Added "ansible_python_interpreter=/usr/bin/python3" to the inventory.ini to specify what version of python we are using. We also corrected some big and small letters, added "chmod 755 /home/vagrant/project" and removed the "export ANSIBLE_CONFIG=/path/to/ansible.cfg" cause it was both typed wrong and does not help us cause we are mixing both Windows and Ubuntu.

This resulted in working pings between Control to all VMs! This concludes this branch.

## 08-Loadbalancer-VM
     - Updated tasks/main.yml with remove default site
     - Added /handlers/main.yml
     - Added /templates/nginx.conf.j2

## 09-Webbserver-1-VM 
Clarification: This branch is for both webserver1 and webserver2

     - Created roles/webserver/files/requirements.txt
     - Created roles/webserver/files/app.py 
       (simplified version to test functionality - it will only return hostname to verify loadbalancer without a database)
     - Created roles/webserver/tasks/main.yml
     - Created roles/webserver/templates/flask.service.j2
     - Created roles/webserver/handlers/main.yml
     - Updated site.yml to include webservers

### Verification:
  **Run playbook**
  ```
ansible-playbook site.yml -v
```

  *failed=0 after playbook is running*

  **Check if flask is active on webserver1**
  ```
ansible webservers -m command -a "systemctl status flask"
```

  **Test throught loadbalancer**
  ```
curl http://192.168.56.11/
curl http://192.168.56.11/health
  ```

  *Expected result:*
 ```
<h1>Hej fran webserver1!</h1>
{"hostname": "webserver1", "status": "ok"}
```

## 10-Database-VM
     - Created roles/database/files/seed.sql
     - Created roles/database/tasks/main.yml
     - Created roles/database/handlers/main.ym
     - Replaced roles/webserver/files/app.py with the full version that uses SQLAlchemy
     - Updated roles/webserver/templates/flask.service.j2 with database details in Environment.
       (this refers to /vars/vars.yml and /vars/secrets.yml)
     - Updated site.yml to include the database (running first)
     - Added code to vagrant file to copy secrets.yml from /vagrant/ to
       /home/vagrant/project/ansible/vars/secrets.yml

## 11-Streaming-VM
     - Created roles/streaming/tasks/main.yml
     - Created roles/streaming/handlers/main.yml
     - Created roles/streaming/templates/nginx.conf.j2
     - Updated site.yml to include the streaming VM
     - Added code to vagrant file to create /var/www/videos and copy the .mp4-file from /vagrant/

 ### Verification:
  **Run playbook**
  ```
ansible-playbook site.yml -v
```

  **Verify that the streaming-server is serving files**
  ```
curl -I http://192.168.56.15/videos/nitflix.mp4
```
*Expected result:*
```
HTTP/1.1 200 OK
Content-Type: video/mp4
```

  **Verify the entire chain via the loadbalancer**
  ```
curl http://192.168.56.11/
```
*Expected result:*
```
Nitflix HTML page with the video player
```

  **Verify thought web browser**
```
http://192.168.56.11
```
*Expected result:*
```
Nitflix webpage that allows to play the video (.mp4)
```

# Readme
_________



# **Streaming service**

> This project is a fully automated simulation of a simple streaming service with six total VMs that are configured via Ansible and created with Vagrant.

______
## **Table of contents**
- [Architecture](#Architecture)
- [Environment and IP addresses](#Environment%20and%20IP%20addresses)
- [Map structure](#Map%20structure)
- [Componence](#Componence)
- [Requirements and prerequisites](#Requirements%20and%20prerequisites)
- [Geting started](#Geting%20started)
- [Security](#Security)
- [Securityanalisys](#Securityanalisys)
- [Validation](#Validation)
- [Design and Architecture](#Design%20and%20Architecture)

_________
## **Architecture**
![Topolgi](topolgy.png)
____
## **Environment and IP addresses**

| VM        | Roll              | IP-address    | port forwarding   | Deskription                                                                                   |
| --------- | ----------------- | ------------- | ----------------- | --------------------------------------------------------------------------------------------- |
| Control   | Ansible Control   | 192.168.56.10 | -                 | Ansible controler handels the installasion of all programs and configurasion on all VMs       |
| LB        | Loadbalancer      | 192.168.56.11 | : 80 -> host 8080 | Nginx routes incoming traffic and load balances it evenly across backend servers.             |
| web1      | Applikationserver | 192.168.56.12 | -                 | Flask + SQLAlchemy + Gunicorn                                                                 |
| web2      | Applikationserver | 192.168.56.13 | -                 | Flask + SQLAlchemy + Gunicorn                                                                 |
| database  | Databaseserver    | 192.168.56.14 | -                 | postegresSQL stores the streaming servers video url and other information liked to the video. |
| streaming | Streamingserver   | 192.168.56.15 | -                 | Nginx host the video on the streaming server.                                                 |

__________
## **Map structure**
```
repo/
├── Pictures/ 
│   └── Topology.png                    # Network topology diagram showing VM layout
│
├── Vagrant/
│   ├── Vagrantfile                     # Definens and creats all VMs, runs provision scripts
│   └── nitflix.mp4	                    # Video file copied to streaming VM on boot
│
├── ansible/
│   ├── ansible.cfg                     # Ansible settings: inventory path, host_key_checking, pipelining
│   ├── inventory.ini                   # Lists all VMs with IPs and groups (loadbalancer, webservers, etc.)
│   ├── site.yml                        # Master playbook — runs all roles in correct order
│   │
│   ├── vars/
│   │   ├── vars.yml                    # Non-sensitive variables: db_name, IP-addresses
│   │	└── secrets.example.yml         # Template showing structure of secrets.yml
│   │ 
│ 	 └── roles/              
│       ├── database/
│       │   ├── tasks/
│       │   │   └── main.yml            # Installs PostgreSQL, creates DB/user, runs seed.sql
│       │   ├── files/
│       │   │  	└── seed.sql            # Creates videos table, grants SELECT to nitflix_user, inserts test data
│       │   └── handlers/
│       │     	└── main.yml            # Restarts PostgreSQL when config changes
│       │
│       ├── loadbalancer/
│       │   ├── templates/
│       │ 	│ 	 └── nginx.conf.j2      # Nginx config — dynamically generates upstream block from inventory
│       │   ├── handlers/
│       │ 	│	  └── main.yml          # Reloads nginx when config changes
│       │   └── tasks/
│       │       └── main.yml            # Installs and configures nginx as load balancer
│       │
│       ├── streaming/
│       │   ├── tasks/
│       │   │   └── main.yml            # Installs and configures nginx as static file server
│       │   ├── handlers/
│       │   │   └── main.yml            # Reloads nginx when config changes
│       │   └── templates/
│       │       └── nginx.conf.j2       # Nginx config — serves video files from /var/www/videos/
│       │
│       └── webservers/
│           ├── tasks/
│           │   └── main.yml            # Installs Python, creates venv, copies app files, starts Flask
│           ├── files/
│           │  	├── requirements.txt    # Python dependencies: Flask, SQLAlchemy, psycopg2, Gunicorn
│           │  	├── app.py              # Flask app — fetches video data from PostgreSQL, renders HTML
│           │   ├── streming.css        # Stylesheet for the Nitflix web page
│           │  	└── templates/
│           │  	    └── index.html      # Jinja2 HTML template — displays video player with DB data
│           ├── handlers/
│           │  	└── main.yml            # Reloads systemd and restarts Flask when files change
│           └──templates/
│           	  └── flask.service.j2  # systemd service — autostart Flask/Gunicorn on boot
├── .gitignore                          # Excludes secrets.yml and unnecessary vagrant files from being uploaded to Github
└── README.md                           # Project documentation
  
```

___________

## **Componence**

### **Vagrantfile**

Defines six virtual machines in VirtualBox with a private network (_192.168.56.0/24_). Port forwarding from the load-balancing VM maps port 80 to 8080, making the web application reachable from the Windows host. The database server does not have any port forwarding deliberately, to keep it unreachable from outside.

### **ansible.cfg**

Points to the `inventory.ini` file and enables SSH connections for Ansible control with `host_key_checking = False` (this is only suitable in lab environments). `roles_path = ./roles` to point to the files for the roles.

### **Inventory.ini**

Groups the different servers into (_Loadbalancing_), (_Database_), (_Webservers_), (_Streaming_), and (_Control_). This is done with IP addresses.

### **Site.yml**

Master playbook for Ansible that both points to the `vars/vars.yml` and also couples the roles to the different groups made in the `inventory.ini`.

This file also controles the order in witch the roles are run

1. streaming - configures the streaming vm to 
2. database - configures the database vm to creat the db table
3. webservers - configures both of the webservers vm
4. loadbaring - configures the loadbaring vm

### **Roll loadbalancer**
The load balancer role installs and configures Nginx to redirect all traffic to the web servers, this allows the web servers to share the load for the site. It gets the web server IPs from the `inventory.ini` file and the configuration file is `/templates/nginx.conf.j2`.

### **Roll Webservers**
The web server role installs all the programs listed in `/files/requirements.txt` and configures Gunicorn and Flask. It also uses the Python library SQLAlchemy to connect the database table in `seed.sql` to the Flask `app.py` and the `index.html` that is loaded in the Flask app. This makes the HTML file able to load values from the database with out php code.

### **Roll Streaming**
Installs nginx and configures the vm to 

### **Roll Database**
Installs PostgreSQL and configures a database table in the `seed.sql` fill

### **Flask application (app.py)**

________

## **Requirements and prerequisites**

#### **Programs that must be installed on the Windows host for this project to work.**

- [VirtualBox ](https://www.virtualbox.org) —
- [Vagrant](https://developer.hashicorp.com/vagrant) —
- [Git](https://git-scm.com/install/windows)

### **Hardware requirements**

- At least 16 GB of RAM (The project uses a total of ~6 GB RAM)
- At least 20 GB of free disk space

#### **Secrets file:**

Creates a `secret.yml` file in `vagrant/secrets.yml` based on the template  
`secrets.example.yml`

________

## **Geting started**
```bash
# 1. clone the github repo vi ether ssh or https

# ssh
git clone  git@github.com:A-Hagman/ITS25-School-project-Load-balanced-Video-Streaming-Server.git

# https
git clone https://github.com/A-Hagman/ITS25-School-project-Load-balanced-Video-Streaming-Server.git

cd ITS25-School-project-Load-balanced-Video-Streaming-Server

# 2. creat the secrets-file
creat an secrets.yml based on the secrets.example.yml file

# 3. start all of the VMs
cd vargrant 
vagrant up

# 4. ssh into the ansible controlnode
vagrant ssh control

# 5. execute the ansible playbook
cd ~/home/vagrant/project/ansible
ansible-playbook -i inventory.yml site.yml -v

# 6. validate
bash test/verify.sh
```

### **Expectations**
Open `https://192.168.52.11` in a browser you should be able to se the website Nitflix and be able to watch the test video on the site. The site should retrive the information that is storde in the database vm where the videos url is stored form the streaming vm.

---
## **Secrets**

The file `/vagrant/secrets.yml` must be created locally and is never committed to GitHub because it contains sensitive information like passwords, and is therefore excluded from being committed to GitHub via the `.gitignore` file.

Copy the variables from the example secrets file and fill in real values.

The file should be available via on the control node via the shard vagrant folder .`vagrant`.

---
## **Security**
There is none

---
## **Securityanalisys**

### **Curent Shortcomings**
#### **1: No firewall rules**

####  **2: Unencrypted comunications**

#### **3: idk**

____

## **Validation**

To validate that everything is working correctly, run the automated validation script.

```bash
Bash ansible/test/verify.sh
```

The script validates the following
- That Ansible pings all the VMs
- That Nginx answers on the load balancing port `80`
- That Flask answers on both web servers on port `5000`
- That round-robin works (two calls give different host names)
- That the database is available from the control node
- That the streaming server answers on port `80`
- That Flask returns HTML (and not a 500 error)
- That the systemd services are running on the correct VMs

____

## **Design and Architecture**

### **Why are there two web servers and a load balancer?**
If we had only used one web server and no load balancer, that would have made the project easier, but we decided to add some more complexity by doing that to simulate how a real streaming service is set up. We also could have added another streaming server and a load balancer to have something even more like a real streaming service, but we decided not to do that.

The setup also allows one of the web servers to be taken down by, for example, a cyber attack without completely shutting down the site.

### **Why do we have a separate streaming server and not just use the database?**
By having a separate streaming server, it makes scaling the operation easier, as databases are quite hard to scale and it does not put unnecessary strain on the database when the site is in use. Databases are also quite bad at serving large files, which can result in the site becoming slower.

It may also add some extra layers of protection to both the streaming server and database when configured correctly.

### **Why do we use Gunicorn and SQLAlchemy?** 
Gunicorn, or **Green Unicorn**, is a Python based web server program that works with Flask. Gunicorn can handle multiple requests at the same time and allows for multiple workers on the same CPU core, making it generally faster and more reliable than just a Flask application. Gunicorn sits in front of the Flask application, allowing you to use a normal Flask application with Gunicorn to gain its benefits. There for we decided to use it in oure project to make the Flask application faster and more reliable.

SQLAlchemy is a Python based library that allows you to use Python code to interact with a database instead of using raw SQL queries. We use SQLAlchemy to allow the Flask application to request the necessary information from the database VM, like video title, views, and the streaming URL. 

Flask does not allow you to natively use a SQL database, so either way we would have needed a library, but we decided on SQLAlchemy because it uses Python code and not SQL code, and we are better at Python than SQL.

### **Why do we use Nginx for both the load baring vm and the streaming vm**
We use Nginx on both the load balancing VM and the streaming VM. For the load balancing VM, Nginx is one of the most widely used tools for that purpose in the world, being easy to install and configure, and relatively lightweight and fast, making it an extremely good tool for that role.

For the streaming VM we chose it because Nginx has a technique called sendfile, which allows it to send a file over the network without copying it through the application first, making it really efficient at sending large static files, which is exactly what a streaming service needs.

____
*Skapad av: [Anton Hagman, William Åström]*  
*Kurs: Virtualiseringsteknik*  
*Datum: [2026-05-12]*



