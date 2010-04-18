rem grep -o "[[:alnum:]+\.\_\-]*@[[:alnum:]+\.\_\-]*" vol21_no26 > emails_vol21_no26.txt
for %%i in (*.txt) do grep -o "[[:alnum:]+\.\_\-]*@[[:alnum:]+\.\_\-]*" %%i > emails_%%i