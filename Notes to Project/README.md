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
 
## 08-BLABLABLA
