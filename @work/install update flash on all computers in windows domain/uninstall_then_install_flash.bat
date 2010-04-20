@echo off
:PREPARE
c:
cd\
copy \\Server\Folder\install_flash_player_10_active_x.msi c:\
copy \\Server\Folder\install_flash_player_10_plugin.msi c:\
copy \\Server\Folder\uninstall_flash_player.exe c:\

:CHECKFLASHLEGACY
REM This will check for a legacy Flash Player
if exist "C:\WINDOWS\system32\Macromed\Flash\flash.ocx" (goto DELETEFLASHLEGACY) ELSE (goto CHECKFLASH8X)

:DELETEFLASHLEGACY
REM This will uninstall a legacy Flash Player
c:\uninstall_flash_player.exe /silent
echo detected and deleted legacy flash on %computername% - %time% - %date% >> \\Server\Folder\install_log.txt

:CHECKFLASH8X
REM This will remove an advertised version of Flash 8
if exist "C:\WINDOWS\Installer\{6815FCDD-401D-481E-BA88-31B4754C2B46}" (goto UNINSTALLFLASH8X) ELSE (goto INSTALLFLASH9)

:UNINSTALLFLASH8X
c:\uninstall_flash_player.exe /silent
echo detected and deleted flash 8x on %computername% - %time% - %date% >> \\Server\Folder\install_log.txt

:INSTALLFLASH10
msiexec.exe /i c:\install_flash_player_10_active_x.msi /qb
echo flash 10.x IE installed on %computername% - %time% - %date% >> \\Server\Folder\flash_log.txt
msiexec.exe /i c:\install_flash_player_10_plugin.msi /qb
echo flash 10.x Firefox installed on %computername% - %time% - %date% >> \\Server\Folder\flash_log.txt

:CLEANUP
del c:\install_flash_player_10_active_x.msi
del c:\install_flash_player_10_plugin.msi
del c:\uninstall_flash_player.exe

:EOF
