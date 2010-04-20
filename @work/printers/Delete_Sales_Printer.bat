Rundll32 printui.dll,PrintUIEntry /gd /n\\VFP-SBS\Sales4100
start /wait sc stop spooler
start /wait sc start spooler