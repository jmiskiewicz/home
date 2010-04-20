Rundll32 printui.dll,PrintUIEntry /ga /n\\VFP-SBS\Sales4100
start /wait sc stop spooler
start /wait sc start spooler
\\VFP-SBS\NETLOGON\sleep.exe 25
