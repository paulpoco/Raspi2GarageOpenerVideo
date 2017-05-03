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
$ sudo raspi-config
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
$ sudo apt-get update
$ sudo apt-get upgrade
$ sudo reboot

Info @ http://wiringpi.com/download-and-install/

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

Any OS
Download files from Github https://github.com/paulpoco/Raspi2GarageOpenerVideo/tree/master/var/www   
Download Filezilla. Using Putty or another ssh terminal:

  ssh pi@[YOUR PI'S IP ADDRESS]
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
https://github.com/paulpoco/Raspi2GarageOpenerVideo/blob/master/etc/init.d/garagerelay

Make the file executable:
$ sudo chmod 777 /etc/init.d/garagerelay

Now tell your pi to run this script at boot:
$ sudo update-rc.d -f garagerelay start 4
(Note: You can safely ignore the "missing LSB tags" warning.)

=====================================================================================================
Step 7>>>>     Get the Camera Working on Raspbian Jessie

Ok here is the proper way to get the camera working:

Install motion to get dependecies.

$ sudo apt-get install motion
$ sudo apt-get install libjpeg62

Create a directory in your home called mmal

$ cd ~/
$ mkdir mmal
$ cd mmal

Download @maya fork of dozencrow’s motion program that works with Rasbian Jessie

$ wget https://www.dropbox.com/s/6ruqgv1h65zufr6/motion-mmal-lowflyerUK-20151114.tar.gz

$ sudo apt-get install -y libjpeg-dev libavformat56 libavformat-dev libavcodec56 libavcodec-dev libavutil54 libavutil-dev libc6-dev zlib1g-dev libmysqlclient18 libmysqlclient-dev libpq5 libpq-dev

$ tar -zxvf motion-mmal-lowflyerUK-20151114.tar.gz

$ ./motion -c motion-mmalcam-both.conf

It should work now when you goto RaspiIP:8081

CTRL-C to quit

Edit the config file and make changes

$ nano motion-mmalcam-both.conf

target_dir /home/pi/mmal/m-video
output_pictures off
framerate 100

===============================================================================================================
Step 8>>>>   Make Your Webpage More Secure

In order to create the file that will store the passwords needed to access our restricted content, we will use a utility called htpasswd. This is found in the apache2-utils package. This was installed in Step 1.

$ sudo htpasswd -c /etc/apache2/.htpasswd pi

(You will be asked to supply and confirm a password for the user)

New password:
Re-type new password:
Adding password for user pi

If we view the contents of the file, we can see the username and the encrypted password for each record:

$ cat /etc/apache2/.htpasswd

pi:$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/

Configuring Access Control within the Virtual Host Definition
Begin by opening up the virtual host file that you wish to add a restriction to. For our example, we'll be using the 000-default.conf file that holds the default virtual host installed through Raspbian's apache package:

$ sudo nano /etc/apache2/sites-enabled/000-default.conf

Inside, with the comments stripped, the file should look similar to this:

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

We will change the directory from /var/www/html to /var/www and add the directory section to make the site password protected.  Will at a later date make it https.

Authentication is done on a per-directory basis. To set up authentication, you will need to target the directory you wish to restrict with a block. In our example, we'll restrict the entire document root, but you can modify this listing to only target a specific directory within the web space. Within this directory block, specify that we wish to set up Basic authentication. For the AuthName, choose a realm name that will be displayed to the user when prompting for credentials. Use the AuthUserFile directive to point Apache to the password file we created. Finally, we will require a valid-user to access this resource, which means anyone who can verify their identity with a password will be allowed in:

https://github.com/paulpoco/Raspi2GarageOpenerVideo/blob/master/etc/apache2/sites-enabled/000-default.conf

<VirtualHost *:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        #ServerName www.example.com

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        <Directory "/var/www">
                AuthType Basic
                AuthName "Restricted Content"
                AuthUserFile /etc/apache2/.htpasswd
                Require valid-user
        </Directory>

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
</VirtualHost>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet

Crtl o
Crtl x

Save and close the file when you are finished. Restart Apache to implement your password policy:

$ sudo service apache2 restart

The directory you specified should now be password protected.

====================================================================================================
