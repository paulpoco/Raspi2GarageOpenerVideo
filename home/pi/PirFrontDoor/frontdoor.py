#!/usr/bin/python

import RPi.GPIO as GPIO
import time
import requests

GPIO.setmode(GPIO.BCM)

PIR_PIN = 22

GPIO.setup(PIR_PIN, GPIO.IN)

def MOTION(PIR_PIN):
        print "Motion Detected!"
        payload = { 'value1' : 'Someone at Front Door'}
        r = requests.post("https://maker.ifttt.com/trigger/{Event}/with/key/{secret key}", data=payload)
        print r.text
print "PIR Module Test (CTRL+C to exit)"
time.sleep(2)
print "Ready"

try:
        GPIO.add_event_detect(PIR_PIN, GPIO.RISING, callback=MOTION)
        while 1:
                time.sleep(120)

except KeyboardInterrupt:
        print "Quit"
        GPIO.cleanup()
        
