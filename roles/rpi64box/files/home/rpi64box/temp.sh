tail /sys/class/thermal/thermal_zone0/temp | awk '{print $1/1000}'
