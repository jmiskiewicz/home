' Paul Price
' http://ifc0nfig.com
' modified by james miskiewicz 08/24/2009 to do wuauctl /detectnow /force /whatever

On Error Resume Next

Set wshArguments = WScript.Arguments
Set objComputer = GetObject(wshArguments(0))

Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & objComputer.get("name") & "\root\cimv2:Win32_Process")

If Err.Number <> 0 Then
     MsgBox Err.Description
     Err.Clear
     WScript.Quit
Else
     Set objProcess = objWMIService.Create("wuauclt /detectnow /downloadnowfast", null, null, intProcessID)
     MsgBox "wuauclt launched sucessfully"
End If

WScript.Quit