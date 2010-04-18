@echo off
rem ===========================================================
rem This stuff wasn't needed (EXE installers aren't automated)
rem windows IE EXE installer
rem wget http://www.adobe.com/go/full_flashplayer_win_ie
rem windows firefox EXE installer
rem wget http://www.adobe.com/go/full_flashplayer_win
rem ===========================================================

rem Windows IE MSI installer
wget http://www.adobe.com/go/full_flashplayer_win_msi

rem Windows firefox MSI installer
wget http://www.adobe.com/go/full_flashplayer_win_pl_msi

rem Mac OS X Universal Installer
wget http://www.adobe.com/go/full_flashplayer_macosx_ub

rem Copy Windows installers & uninstaller to network share
copy *.msi \\Server\Folder\ /y
copy *.exe \\Server\Folder\ /y
copy *.dmg \\Server\Folder\ /y
