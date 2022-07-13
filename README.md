# RPI64Box


RPI64Box is a disk image for a portable web server running on a Single Board Computer and converts its hardware's Wi-Fi into a hotspot for networking. Implemented on a Ubuntu 64-bit Long Term version, it offers stability and increased performance. It was initially built to compare LAMP-based web server performance when OS bit-depth and storage devices vary. The RPI64Box's main use is to compare a microserver's hardware performance against the one published on [rpi64box.com](http://rpi64box.com). The default web application can also be replaced to create portable web applications for online and offline usage.

RPI64Box was built by porting Nicolas Martignoni's MoodleBox ansible script 3.9.0, 2020-11-08, from a 32-bit Pi OS to a 64-bit Ubuntu LTS. Moodlebox is available on [moodlebox.net](https://moodlebox.net) and https://github.com/moodlebox/moodlebox. RPI64Box is available on [rpi64box.com](http://RPI64Box.com) and https://github.com/dhuny/RPI64Box.

The article behind the RPI64Box is titled '[Performance evaluation of a portable single-board computer as a 3-tiered LAMP stack under 32-bit and 64-bit Operating Systems](https://doi.org/10.1016/j.array.2022.100196)' and is published in the [array journal](https://www.sciencedirect.com/journal/array). A video summary of the experiments is available on [YouTube](https://www.youtube.com/watch?v=PhBnIkH91Mg).  

RPI64Box also complements the contents of the book titled '[Portable & Home hosted Learning Management Systems](https://www.amazon.com/Portable-hosted-Learning-Management-Systems/dp/B091H18XFV)', which is available on Amazon.

Contributors and reviewers for all the content are most welcome. A polite mail to the [author](mailto:riyad@dhuny.org) would be highly appreciated.

## The RPI64Box Documentation

A Disk image is available on  [rpi64box.com](https://rpi64box.com). for a quick start. 
Alternatively, to build the RPI64Box, from a Ubuntu LTS 64-bit, please start with the section 
 [Building the RPI64Box disk image from scratch](./README.md#buildfromscratch).
 The section below is a continuity from the disk image.

### Creating test courses, users and test plans for JMeter
The instructions in this section cover the setting up of sample courses, users and test plans for performance evaluation.
1.	Download the disk image from [rpi64box.com](https://rpi64box.com) and transfer on disk storage using [Balena Etcher](https://www.balena.io/etcher/) or alternative transfer software.
2.	A Wi-Fi hotspot with the name of RPI64Box will appear approximately 2 minutes after boot.
3.	Connect via Wi-Fi to the hotspot using the password `rpi64box`.
4.	Connect via SSH with username `rpi64box` and password `Rpi64Box`.
5.	Visit /etc/netplan/01-netcfg.yaml to configure static IP address for LAN.

		sudo nano /etc/netplan/01-netcfg.yaml
6.	Apply the netplan changes.

		sudo netplan apply
7.	Enable the serveragent service for performance monitoring by JMeter.

		sudo systemctl enable serveragent.service
8.	Allow port 4444 through UFW firewall and reboot to start service.

		sudo ufw allow 4444 && sudo init 6
9.	Connect to the RPI64Box via SSH. If Wi-Fi is used, use `10.0.0.1` or `rpi64box.home` as the host name else use the assigned IP address. 
The username remains `rpi64box` and password is `Rpi64Box4$`.
10.	Http protocol is used for performance evaluation. If the disk image was used in the previous steps, amend the line $CFG-->wwwroot and change the `https` to `http` in `/var/www/moodle/config.php`

		sudo nano /var/www/moodle/config.php 
11.	Clone the RPI64Box ansible script into the home directory

		git clone https://github.com/dhuny/RPI64Box.git		
12.	Create a `keys` directory in the repository folder.

		mkdir /home/rpi64box/RPI64Box/keys/		
13.	If the disk image is used and the keys are required. Create a public key with command below and hit return to create one with blank values

		ssh-keygen -t rsa
14.	copy the keys to the RPI64Box folder

		sudo cp /home/rpi64box/.ssh/id_rsa.pub /home/rpi64box/RPI64Box/keys/
15.	Edit the `default.config.xml` file and configure the variables accordingly. The important variables are `platform_moodle`, `php_version`, `net_tx_max` and `throughput`. While these values are currently on the server-side. The client-side will have the same variables and will require the same values for the proper running of the tests.

		sudo nano ~/RPI64Box/default.config.yml
The variable platform_moodle creates a directory to store the test plans and results. The `net_tx_max` is the maximum network transmission rate in KB/s between the server and the client. This value can be measured through `iperf` for the network under test and will be different for cable-connected and wireless Local Area Networks (WLAN). 
JMeter uses the throughput value to ensure that the server has the right amount of requests for performance evaluation. A good starting value point would be to assume that each CPU core can handle at least one request/ second, and one CPU core is kept available for the OS.
Throughput = (number of processors/threads -1) X 60.
For Raspberry Pi, (4-3) X 60 = 180.

16.	Set the correct user and host in hosts.yml for smooth execution of the ansible script. The user is `rpi64box`
		
		nano ~/RPI64Box/hosts.yml
17.	The test scripts to create test courses and testplans works only if Moodle is set to developer mode. 
Visit [http://rpi64box.home/admin/settings.php?section=debugging](http://rpi64box.home/admin/settings.php?section=debugging) , login with Username `rpi64box` and Password `Rpi64Box4$`. To access via Admin panel, go through `Site administration` --> `Development` --> `Debugging` 
and set Debug Messages to DEVELOPER.  


18. Execute the testplans.yml script to generate the test courses.

		cd ~/RPI64Box && ansible-playbook testplans.yml
		
After successful execution of the testplans.yml, the moodle database is populated with three courses namely Extra Small (XS), Small (S) and Medium (M). Testplans were created and labelled as xx, ss, sm, m3-m9 and mm. 
Please check if a new directory and associated files is created inside the `/var/www/moodle/admin/tool/generator/cli/tests`. Files with the extension .links contain the necessary commands to run the JMeter tests. The next step is to connect via another client and launch the tests.


### Performance evaluation of the microserver

1.	The evaluation comprises four tests; the first two are executed on the server and are fast to return results. The remaining are tested from a client computer running JMeter.
2.	The first test is the Pi Benchmark test which is a test on the performance of the disk storage. 		sudo ~/scripts/./pibenchmark.sh
Connect via SSH hostname rpi64box.home or static ip address, username rpi64box and password Rpi64box4$. Then run 		

		sudo ~/scripts/./pibenchmark.sh
3.	The Moodle System Benchmark is the next one and is accessed by logging visiting the page below. The admin credentials are `rpi64box` and `Rpi64box4$`
		
		http://rpi64box.home/report/benchmark/index.php
		
4.	The last two tests, Apdex and resource monitoring are executed from a web client. Before running the tests, please ensure that the serveragent service is working by typing the command below. A response is  expected—type exit to quit.
		
		telnet localhost 4444

5.	The next step is to prepare a web client with JMeter to run the tests for performance evaluation. Download a Ubuntu server and boot a Raspberry Pi 4 with it. Next, visit the link below to continue with the process on the client-side.

		https://github.com/dhuny/web-performance
		
<a name="buildfromscratch">&nbsp; </a>
## Building the RPI64Box disk image from scratch

To build an RPI64Box from scratch with this script, a Raspberry Pi 3B, 3B+ or 4B will be required.

1. 	Clone[Ubuntu ARM-64 LTS v 20.04](https://cdimage.ubuntu.com/releases/20.04/release/ubuntu-20.04.4-preinstalled-server-arm64+raspi.img.xz) on your microSD card. Ubuntu-20.04 was previously used for the experiments and is working
2.	Static Network addresses and Wi-Fi credentials can be preconfigured in the network-config file present in the SD card (Optional).
3.	Insert the microSD card into the Raspberry Pi.
4.	Connect the Raspberry Pi to the Ethernet network and boot it.
5.  When requested change the default password from `ubuntu` to `Rpi64box4$` for the moment. Do change password again when done.
5.	Go to `/etc/netplan` , delete the existing files and download a sample config file for static IP address. Make sure to type the (dot slash) ./ properly in the command below.

		cd /etc/netplan && sudo rm -rf ./* && sudo wget https://rpi64box.com/01.netcfg.yaml
		
		
5.	Edit the 01-netcfg.yaml with appropriate network values.

		sudo nano 01-netcfg.yaml
		

6.  Apply the netplan changes

		sudo neplan apply

5.	The server is now reachable via SSH. The login with username `ubuntu` and password `Rpi64box4$`.
6.	Type the commands below to update and upgrade the Linux

		sudo apt update && sudo apt upgrade -y
		
8.	Ubuntu will take a few minutes to upgrade as it may be running some updates already. 
9.  Create a `ssh.txt` file on the `boot` partition, e.g. using 
        
		sudo touch /boot/ssh.txt
		
7. [Install Ansible](https://docs.ansible.com/intro_installation.html](https://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html#installing-ansible-on-ubuntu)) on the computer, followed by [ `sshpass`](https://gist.github.com/arunoda/7790979) to enable passing SSH password to the Raspberry Pi. The following commands will install both

			sudo apt install sshpass ansible -y
			sudo apt install --no-install-recommends python3-netaddr

10. [Clone the RPI64Box repository](https://github.com/dhuny/RPI64Box.git) to the local drive. A quick way is:
	
			git clone https://github.com/dhuny/RPI64Box.git
	
11. Create a `keys` directory in the repository folder. 

		mkdir /home/ubuntu/RPI64Box/keys/

12. Create a public keys. hit return to create one with blank values

		ssh-keygen -t rsa

13. copy the keys to the RPI64Box folder
	
		sudo cp /home/ubuntu/.ssh/id_rsa.pub /home/ubuntu/RPI64Box/keys/
	
14.	Get the IP address of the Raspberry Pi using `ifconfig` or `ip a` and update it in the RPI64Box/hosts.yml file with

		nano RPI64Box/hosts.yml
		
15.	Create a `config.yml` if required with `cp default.config.yml config.yml`.
16.	From the Home directory, run the following ansible script to prepare the OS

		  cd ~/RPI64Box && ansible-playbook ubuntu.yml   
		  
17.	Wait 1 -2 mins; the playbook will change the username from ubuntu to rpi64box; the script is expected to exit with failure as the existing user will be removed.
18.	Playbook will stop after username change, with an error similar to `Unable to create local directories(/home/ubuntu/.ansible/cp'...`
19.	Reboot server with 

		sudo init 6 
		
25.	Login with username `rpi64box` and password `Rpi64box4$`

28.	Change directory to RPI64Box and run the rpi64box.yml ansible script

		cd ~/RPI64Box && ansible-playbook rpi64box.yml
	 
29.	Wait 15–50 minutes, depending on the Raspberry Pi model, SD card speed, and Internet bandwidth and the installation will complete

30.	Connect via SSH again; username `rpi64box` and password `Rpi64box4$`

31.	Set/Change the Wi-Fi Country Code in `/etc/default/crda` , `/etc/wpa_supplicant/wpa_supplicant.conf` and restart the microserver.

        sudo nano /etc/default/crda
		sudo nano /etc/wpa_supplicant/wpa_supplicant.conf

32. To disable cloud-init service from ubuntu, use
	
		sudo systemctl disable cloud-init.service

33.	Set service to remove Wi-Fi power management to off. This will ensure Wi-Fi is always on.

		sudo systemctl enable WiFiPowerMgmtOff.service


34.	A new Wi-Fi hotspot with the name `RPI64Box` should be available. The default password is `rpi64box`

35. As a measure of security, do change the rpi64box user password with `sudo passwd rpi64box`
36. If required, the HTTPS protocol can be enabled in `/var/www/moodle/congig.php` by setting an 's' in the $CFG-->wwwroot variable.
36. Remove the previous SSH authorized keys from the root user with the command below

		sudo rm -rf /root/.ssh
37. Reboot the system with `sudo init 6`
38.	Via browser, visit [http://rpi64box.home](http://rpi64box.home), sign in with the username `rpi64box` and password `Rpi64Box4$` to get access to the LMS as administrator.


39.	At this point, the RPI64Box is ready for use as a LAMP stack. The Disk image with this section completed is available via [https://rpi64box.com/](RPI64Box_with_Moodle.img.zip) for download.
The upcoming steps prepares the system for performance evaluation. 
To exit here and use RPI64Box as a LAMP stack, clear the root keys by running command at number [41](./README.md#end) and use apt get and remove to modify the application packages.


## Availability

The Moodlebox code is available on [Moodlebox git](https://github.com/moodlebox/moodlebox).
The RPI64Box code is available on [RPI64Box git](https://github.com/dhuny/rpi64box).

### Release notes, Thanks & License

This work is simply a port of the original work by Nicolas Martignoni, please visit [Moodlebox git](https://github.com/moodlebox/moodlebox) for the Original Release Notes, Thanks and License. 

This contribution is from [R.Dhuny](riyad@dhuny.org)

All contributions to this repository are licensed under AGPLv3 or any later version.
The copyright belongs to all the individual contributors. 
```
@copyright Copyright © 2021 onwards, Dhuny Riyad (riyad@dhuny.org)
```

