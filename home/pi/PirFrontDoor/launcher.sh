#!/bin/sh
# launcher.sh
# sudo crontab -e
# place line 5 at bottom of crontab
# @reboot sh /home/pi/PirFrontDoor/launcher.sh >/home/pi/PirFrontDoor/logs/cronlog 2>&1
cd /
cd /home/pi/PirFrontDoor
sudo python frontdoor.py
cd /
