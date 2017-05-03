# Raspi2GarageOpenerVideo
Raspberry Pi Garage Door Opener With Streaming Video of Door Status

Raspbery Pi2 or Pi3
Wifi or Ethernet - Hardwired recommended
Raspberry Pi Camera v1
Relay Board
Case that is mountable
Magnetic reed switch
wire for Mag Sensor
jumpers

======================================================================================================
Step 1>>>
You need do this Instructable @ https://www.instructables.com/id/Raspberry-Pi-Garage-Door-Opener

In above external Instructable:

Instead of step 1
Download Rasbian Jessie Lite has no GUI
Unzip the IMG
Use Etcher or some SDCARD imaging software for the IMG
Open the boot directory on the SDCARD and make a "ssh" file so SSH will work
Place card back in Raspbarry Pi2 or 3
Turn on

If not wired then you need to get Wifi working so I recommend doing wired then get WiFi working
once you can SSH in, otherwise you would have to drag a Monitor,mouse,keyboard out and hook it up.

=======================================================================================================
Step 2>>>
Get Wifi working if needed.   @ https://www.instructables.com/id/Raspberry-Pi-Garage-Door-Opener


=======================================================================================================
Step 3>>>   Before step 3

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
Continue Step 3 above @ https://www.instructables.com/id/Raspberry-Pi-Garage-Door-Opener

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

$ ssh pi@[YOUR PI'S IP ADDRESS]
$ sudo chown -R pi:root /var/www



