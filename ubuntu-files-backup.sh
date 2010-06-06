#!/bin/sh
# http://bit.ly/bynyi3
# This will generate a list of installed packages on the current system
# To reinstall a system from the file generated from this backup:
#   sudo apt-get update
#   sudo apt-get dist-upgrade
#   dpkg -–set-selections < ubuntu-files
#   sudo dselect
sleep 10s;dpkg --get-selections | grep -v deinstall > /home/james/ubuntu-files
