' Paul Price
' http://ifc0nfig.com

On Error Resume Next

Set wshArguments = WScript.Arguments
Set objComputer = GetObject(wshArguments(0))

Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & objComputer.get("name") & "\root\cimv2:Win32_Process")

If Err.Number <> 0 Then
     MsgBox Err.Description
     Err.Clear
     WScript.Quit
Else
     Set objProcess = objWMIService.Create("gpupdate /force /wait:100", null, null, intProcessID)
     MsgBox "gpupdate launched sucessfully"
End If

WScript.Quit