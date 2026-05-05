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

## 08-BLABLABLA


# Readme
_________
# **Projectname**

> This project is a fully automated streaming service with six total VMs that are configured via Ansible.

______
## **Table of contents**
- [Architecture](#Architecture)
- [Environment and IP addresses](#EnvironmentandIPaddresses)
- [Map structure](#Mapstructure)
- [Komponence](#Komponence)
- [Requirements and prerequisites](#Requirementsandprerequisites)
- [Geting started](#Getingstarted)

_________
## **Architecture**

![[Streaming.drawio.png]]

____
## **Environment and IP addresses**

| VM        | Roll              | IP-address    | port forwarding   | Deskription                                                                       |
| --------- | ----------------- | ------------- | ----------------- | --------------------------------------------------------------------------------- |
| Control   | Ansible Control   | 192.168.56.10 | -                 | Ansible controler                                                                 |
| LB        | Loadbalancer      | 192.168.56.11 | : 80 -> host 8080 | Nginx routes incoming traffic and load balances it evenly across backend servers. |
| web1      | Applikationserver | 192.168.56.12 | -                 | Flask + SQLAlchemy                                                                |
| web2      | Applikationserver | 192.168.56.13 | -                 | Flask + SQLAlchemy                                                                |
| database  | Databaseserver    | 192.168.56.14 | -                 | postegresSQL                                                                      |
| streaming | Streamingserver   | 192.168.56.15 | -                 | Nginx                                                                             |

__________
## **Map structure**


```
repo/
├── Vagrant/
│   ├── Vagrantfile          # Definens and creats all VMs
│
├── ansible/
│   ├── ansible.cfg 
│   ├── inventory.ini        # List all the servers ansible controles
│   ├── site.yml             # Master playbook defines the roles and there order
│   │
│   ├── vars/
│   │   └── main.yml 
│   │ 
│ 	└── roles/              
│       ├── control/
│       │   └── tasks/
│       │       └── main.yml
│       │
│       ├── database/
│       │   └── tasks/
│       │       └── main.yml
│       │
│       ├── loadbalancer/
│       │   ├── teamplates/
│       │ 	│ 	└── nginx.conf.j2
│       │   ├── handlers/
│       │ 	│	└── main.yml
│       │   └── tasks/
│       │       └── main.yml
│       │
│       ├── mediaserver/
│       │   └── tasks/
│       │       └── main.yml
│       │
│       └── webservers/
│           ├── tasks/
│           │  └── main.yml
│           └── files/
│ 				└── reguierments.txt
│
├── flask/
│   ├── app.py
│   ├── models.py
│   ├── templates/
│ 	│ 	└── index.html
│   └── static/
│ 		└── streming.css
│
├── Pictures/ 
│   └── Topology.png
│
├── .gitignore
└── README.md
  
```

___________

## **Komponence**

### Vagrantfile

Defines six virtual machines in VirtualBox with a private network (_192.168.56.0/24_). Port forwarding from the load-balancing VM maps port 80 to 8080, making the web application reachable from the Windows host. The database server does not have any port forwarding deliberately, to keep it unreachable from outside.

### ansible.cfg

Points to the `inventory.ini` file and enables SSH connections for Ansible control with `host_key_checking = False` (this is only suitable in lab environments). `roles_path = ./roles` to point to the files for the roles.

### Inventory.ini

Groups the different servers into (_Loadbalancing_), (_Database_), (_Webservers_), (_Streaming_), and (_Control_). This is done with IP addresses.

### Site.yml

Master playbook for Ansible that both points to the `vars/vars.yml` and also couples the roles to the different groups made in the `inventory.ini`.

### Roll loadbalancer

Installs nginx and configures the nginx program to route all traffic from the webservers to it.

### Roll Webservers

Installs Flask and the plugin SQLAlchemy that allows the Flask app to be connected to the database VM.

### Roll Streaming

Installs nginx

### Roll Database

Installs PostgreSQL and configures a database table

### Flask application (app.py)



________

## **Requirements and prerequisites**

#### Programs that must be installed on the Windows host for this project to work.

- [VirtualBox ](https://www.virtualbox.org) —
- [Vagrant](https://developer.hashicorp.com/vagrant) —
- [Git](https://git-scm.com/install/windows)

### Hardware requierments

- Att least 16 GB of RAM (The prodject uses a total of ~6 GB RAM)
- Att least 20GB of free diskspace

#### Secrets-fil:

Creats a seacret.yml fille in the `vagrant/secrets.yml` based on the template
`FILEPATH TO EXEMPLE.yml`

________

## **Geting started**
```
# 1. clone the github repo vi ether ssh or https

# ssh
git clone  git@github.com:A-Hagman/ITS25-School-project-Load-balanced-Video-Streaming-Server.git

# https
git clone https://github.com/A-Hagman/ITS25-School-project-Load-balanced-Video-Streaming-Server.git

cd ITS25-School-project-Load-balanced-Video-Streaming-Server

# 2. creat the secrets-file


# 3. start all of the VMs
cd vargrant 
vagrant up

# 4. ssh into the ansible controlnode
vagrant ssh control

# 5. execute the ansible playbook
cd ~/ITS25-School-project-Load-balanced-Video-Streaming-Server/ansible
ansible-playbook -i inventory.yml site.yml -v

# 6. validate
bash test/verify.sh
```

### Expectations

---
## **Secrets**

---
## **Security**

---
## **Securityanalisys**

____
