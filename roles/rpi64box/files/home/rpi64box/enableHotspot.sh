#!/bin/bash

sleep 5 && sudo service hostapd stop && sleep 10 && sudo iwconfig wlan0 power off && sleep 5 && sudo service hostapd start && sudo service dnsmasq start
