#!/bin/bash
VSIZE=$(gconftool-2 -g /apps/compiz/general/screen0/options/vsize)
HSIZE=$(gconftool-2 -g /apps/compiz/general/screen0/options/hsize)
WORKSPACE=$[ $VSIZE*$HSIZE ]
declare -a PICTURES
#echo "VSIZE*HSIZE=WORKSPACES:" $VSIZE $HSIZE $WORKSPACE
PICTURES=(`find ~/Pictures/Wallpaper -type f \( -iname '*.jpg' -o -iname '*.png' \) | rl -c $WORKSPACE`)
len=${#PICTURES[*]}
i=0
files="[" # gconftool-2 needs the wallpaper argument in a bracketed list so we begin by adding an open bracket
	while [ $i -lt $len ]; do
	# echo "$i: ${PICTURES[$i]}" # Echos the array items (suitable wallpaper found)
	files=$files"${PICTURES[$i]}," 
	let i++
done
files=$files"]" # close the file list bracket
# echo $files
gconftool-2 --set /apps/compiz/plugins/wallpaper/screen0/options/bg_image --type list --list-type string $files
