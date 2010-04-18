#!/bin/sh
# script you can loop from Startup Applications that will
# exit when you log out of gnome, by gmargo, from http://goo.gl/Y639
# http://no.ubuntuforums.org/showpost.php?p=9088014&postcount=5
while :
do
    # When gnome exits, our parent process becomes init,
    # but the PPID stays set.
    if ! ps -p $PPID > /dev/null
    then
        exit 0
    fi
    ~/randomize_compiz_wallpaper.sh
    sleep 1800
done
