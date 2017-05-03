# Raspi2GarageOpenerVideo
Raspberry Pi Garage Door Opener With Streaming Video of Door Status

I have borrowed from @ https://www.instructables.com/id/Raspberry-Pi-Garage-Door-Opener
updated or changed the process

Raspbery Pi2 or Pi3
Wifi or Ethernet - Hardwired recommended
Raspberry Pi Camera v1
Relay Board
Case that is mountable
Magnetic reed switch
wire for Mag Sensor
jumpers
One of the big clunky RF Garage Door remotes


===============================================================================================
Step 1>>>   Gather Software images

Download Rasbian Jessie Lite @ https://www.raspberrypi.org/downloads/raspbian/
Unzip the IMG
Use Etcher or some SDCARD imaging software for the IMG @ https://etcher.io/
Open the boot directory on the SDCARD and make a "ssh" file so SSH will work
Place card back in Raspbarry Pi2 or 3
Turn on

If not wired then you need to get Wifi working so I recommend doing wired then get WiFi working
once you can SSH in, otherwise you would have to drag a Monitor,mouse,keyboard out and hook it up.

===============================================================================================
Step 2>>>   Get Wifi working if needed.   
@ https://www.instructables.com/id/Raspberry-Pi-Garage-Door-Opener


================================================================================================
Step 3>>>   Install Software

ssh to your pi
sudo raspi-config
#1 change the password from default
#2 change hostname
#3 Boot options
  B2 yes
#4 Localisation .....
  I1 , I2, I3
#5 Interface options
  P1 enable Pi camera
#7 Advanced
  A1 expand file system
#8 Update tool to latest version
  Finish
  Reboot

Update Raspbian and its packages
  sudo apt-get update
  sudo apt-get upgrade
  sudo reboot

Go to http://wiringpi.com/download-and-install/

To install…

First check that wiringPi is not already installed. In a terminal, run:

$ gpio -v
If you get something, then you have it already installed. The next step is to work out if it’s installed via a standard package or from source. If you installed it from source, then you know what you’re doing – carry on – but if it’s installed as a package, you will need to remove the package first. To do this:

$ sudo apt-get purge wiringpi
$ hash -r
Then carry on.

If you do not have GIT installed, then under any of the Debian releases (e.g. Raspbian), you can install it with:

$ sudo apt-get install git-core
If you get any errors here, make sure your Pi is up to date with the latest versions of Raspbian: (this is a good idea to do regularly, anyway)

$ sudo apt-get update
$ sudo apt-get upgrade
To obtain WiringPi using GIT:

$ cd
$ git clone git://git.drogon.net/wiringPi
If you have already used the clone operation for the first time, then

$ cd ~/wiringPi
$ git pull origin
Will fetch an updated version then you can re-run the build script below.

To build/install there is a new simplified script:

$ cd ~/wiringPi
$ ./build
The new build script will compile and install it all for you – it does use the sudo command at one point, so you may wish to inspect the script before running it.

Test wiringPi’s installation

run the gpio command to check the installation:

$ gpio -v
$ gpio readall
That should give you some confidence that it’s working OK.

Once Wiring Pi is installed, you will want to install Apache and PHP via these commands:

$ sudo apt-get update
$ sudo apt-get install apache2 php5 libapache2-mod-php5

Once this is done, you will have a working webserver! To verify that, just type in your pi's ip adress in a browser. You should see Apache's default website which says "It Works!".

===============================================================================================================\
Step 4>>>>   Upload the Garage Opener Website

$ ssh pi@[YOUR PI'S IP ADDRESS]
$ sudo chown -R pi:root /var/www

Any OS
Download files from Github https://github.com/paulpoco/Raspi2GarageOpenerVideo/tree/master/var/www   
Download Filezilla. Using Putty or another ssh terminal:
$ ssh pi@[YOUR PI'S IP ADDRESS]
$ sudo chown -R pi:root /var/www
$ sudo cd /var/www/html
$ sudo rm index.html
$ sudo cd ..
$ sudo rmdir html  
After this step the webpage will not work until a later step.

Start filezilla from non Raspi. Log into the raspberry pi with these credentials:
Host: sftp://[YOUR PI'S IP ADDRESS]
Username: pi
Password: xxxxxxxx
Copy using Filezilla file in /var/www and /var/www/css and /var/www/js to place the files in /var/www as the root folder for the webserver on Raspi
Edit index.php and away.php as needed

Some Technical Notes (for those interested):
The website uses jQuery to post to itself (via AJAX) when a user clicks on the big button.  Quartarian did this so that if you refresh the page it doesn't trigger your garage to open.

If your using an iPhone (or the latest dev version of Chrome on Android) and add this website to your home screen, it should work like an app without the browser chrome. (It will still only work when your on your home wifi though :-P )  There is a way to get it to work outside your home network with the away.php file.

=============================================================================================================
Step 5>>>>    Wire the Circuit to the Pi!

Assumption is a Raspberry Pi2 or Pi3 with 40 pin header is used.

RF Garage Door soldered accross button <===============> [Relay1] <===> Normally open terminals
                                                         [Relay] GND <---------------> Ground[Raspi2] pin 06
                                                         [Relay] IN1 <----> GPIO17(GPIO_GEN0)[Raspi2] pin 11 out
                                                         [Relay] VCC <-----------> DCPower 5V[Raspi2] pin 02
                                                         [MagSW] <--------> GPIO27(GPIO_GEN2)[Raspi2] pin 13 in
                                                         [MagSW] <-------------------> Ground[Raspi2] pin 14
                                                  Future [ PIR ] 5V <------------> DCPower 5V[Raspi2] pin 04
                                                  Future [ PIR ] GND <---------------> Ground[Raspi2] pin 20
                                                  Future [ PIR ] Out <----> GPIO22(GPIO_GEN3)[Raspi2] pin 15 
                                                  
===================================================================================================================
Step 6>>>>    Create a Startup Service

This step is important. 
Most relays including the one I purchased, operate like this - when the signal is ON the circuit stays off. When the signal is OFF then the circuit is on. So what happens if your pi looses power? Well most relays have a safety mechanism that keeps the circuit OFF when there is no power at all. The problem that occurs happens between when the pi (and subsequently the relay) gets its power back but before the pi has finished booting to turn the signal ON which is need to keep the circuit off. You could wake up in the morning with your garage open and potentially a few new friends!

After some experimenting, I found a simply work around. I found out that my relay doesn't actually initialize until the GPIO pin mode is set via this command: gpio mode 0 out. Furthermore, I found out that it you set the GPIO pin to ON (gpio write 0 1)before you set the GPIO mode, the relay will stay off once initialized.  

GPIO mode 2 in and then set up pullup.

To make this initialization run at boot, I created a start-up script.

$ ssh pi@[Your Pi's IP]
$ sudo nano /etc/init.d/garagerelay

Then paste this script:
--------------------------------------------------------------------------------------------------
#!/bin/bash
# /etc/init.d/garagerelay


# Carry out specific functions when asked to by the system
case "$1" in
start)
echo "Starting Relay"
# Turn 0 on which keeps relay off
/usr/local/bin/gpio write 0 1
#Start Gpio 0 or 17 in BCM out mode
/usr/local/bin/gpio mode 0 out
#Start Gpio 2 or 27 in BCM in mode with pull up
/usr/local/bin/gpio mode 2 in
/usr/local/bin/gpio mode 2 up
;;
stop)
echo "Stopping gpio"
;;
*)
echo "Usage: /etc/init.d/garagerelay {start|stop}"
exit 1
;;
esac

exit 0
---------------------------------------------------------------------------------------------------------------

Make the file executable:
$ sudo chmod 777 /etc/init.d/garagerelay

Now tell your pi to run this script at boot:
$ sudo update-rc.d -f garagerelay start 4
(Note: You can safely ignore the "missing LSB tags" warning.)

=====================================================================================================
