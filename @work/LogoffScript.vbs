' Logon5.vbs
' VBScript Logon script.
' This program demonstrates how to log information to a log file.
'
' ----------------------------------------------------------------------
' Copyright (c) 2003 Richard L. Mueller
' Hilltop Lab web site - http://www.rlmueller.net
' Version 1 - March 26, 2003
' Version 1.1 - January 25, 2004 - Modify error trapping.
'
' You have a royalty-free right to use, modify, reproduce, and
' distribute this script file in any way you find useful, provided that
' you agree that the copyright owner above has no warranty, obligations,
' or liability for such use.

Option Explicit

Dim objFSO, objLogFile, objNetwork, objShell, strText, intAns
Dim intConstants, intTimeout, strTitle, intCount, blnLog
Dim strUserName, strComputerName, strIP, strShare, strLogFile

strShare = "\\VFP-NAS\LogonLogs"
strLogFile = "LogonLogs.txt"
intTimeout = 20

Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objNetwork = CreateObject("Wscript.Network")
Set objShell = CreateObject("Wscript.Shell")

strUserName = objNetwork.UserName
strComputerName = objNetwork.ComputerName
strIP = Join(GetIPAddresses())

' Log date/time, user name, computer name, and IP address.
If (objFSO.FolderExists(strShare) = True) Then
    On Error Resume Next
    Set objLogFile = objFSO.OpenTextFile(strShare & "\" _
        & strLogFile, 8, True, 0)
    If (Err.Number = 0) Then
        ' Make three attempts to write to log file.
        intCount = 1
        blnLog = False
        Do Until intCount = 3
            objLogFile.WriteLine "Logoff ; "  & Now & " ; " _
                & strComputerName & " ; " & strUserName & " ; " & strIP
            If (Err.Number = 0) Then
                intCount = 3
                blnLog = True
            Else
                Err.Clear
                intCount = intCount + 1
                If (Wscript.Version > 5) Then
                    Wscript.Sleep 200
                End If
            End If
        Loop
        On Error GoTo 0
        If (blnLog = False) Then
            strTitle = "Logon Error"
            strText = "Log cannot be written."
            strText = strText & vbCrlf _
                & "Another process may have log file open."
            intConstants = vbOKOnly + vbExclamation
            intAns = objShell.Popup(strText, intTimeout, strTitle, _
                intConstants)
        End If
        objLogFile.Close
    Else
        On Error GoTo 0
        strTitle = "Logon Error"
        strText = "Log cannot be written."
        strText = strText & vbCrLf & "User may not have permissions,"
        strText = strText & vbCrLf & "or log folder may not be shared."
        intConstants = vbOKOnly + vbExclamation
        intAns = objShell.Popup(strText, intTimeout, strTitle, intConstants)
    End If
    Set objLogFile = Nothing
End If

' Clean up and exit.
Set objFSO = Nothing
Set objNetwork = Nothing
Set objShell = Nothing

Wscript.Quit

Function GetIPAddresses()
    ' Based on a Michael Harris script, modified by Torgeir Bakken
    '
    ' Returns array of IP Addresses as output
    ' by IPConfig or WinIPCfg...
    '
    ' Win98/WinNT have IPConfig (Win95 doesn't)
    ' Win98/Win95 have WinIPCfg (WinNt doesn't)
    '
    ' Note: The PPP Adapter (Dial Up Adapter) is
    ' excluded if not connected (IP address will be 0.0.0.0)
    ' and included if it is connected.

    Dim objShell, objFSO, objEnv, strWorkFile, objFile
    Dim arrData, intIndex, n, arrIPAddresses, arrParts

    Set objShell = CreateObject("wscript.shell")
    Set objFSO = CreateObject("scripting.filesystemobject")
    Set objEnv = objShell.Environment("PROCESS")
    If (objEnv("OS") = "Windows_NT") Then
        strWorkFile = objEnv("TEMP") & "\" & objFSO.GetTempName
        objShell.Run "%comspec% /c IPConfig >" & Chr(34) _
            & strWorkFile & Chr(34), 0, True
    Else
        ' WinIPCfg in batch mode sends output to
        ' filename WinIPCfg.out
        strWorkFile = "WinIPCfg.out"
        objShell.Run "WinIPCfg /batch", 0, True
    End If
    Set objShell = Nothing
    Set objFile = objFSO.OpenTextFile(strWorkFile)
    arrData = Split(objFile. ReadAll, vbCrLf)
    objFile.Close
    Set objFile = Nothing
    objFSO.DeleteFile strWorkFile
    Set objFSO = Nothing
    arrIPAddresses = Array()
    intIndex = -1
    For n = 0 To UBound(arrData)
        If (InStr(arrData(n), "IP Address") > 0) Then
            arrParts = Split(arrData(n), ":")
            If (InStr(Trim(arrParts(1)), "0.0.0.0") = 0) Then
                intIndex = intIndex + 1
                ReDim Preserve arrIPAddresses(intIndex)
                arrIPAddresses(intIndex)= Trim(CStr(arrParts(1)))
            End If
        End If
    Next
    GetIPAddresses = arrIPAddresses
End Function
