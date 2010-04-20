@echo off
%~d1
cd "%~p1"
grep -o "[[:alnum:]+\.\_\-]*@[[:alnum:]+\.\_\-]*" %~nx1 > emails_%~nx1
sort -o sorted_%~nx1 emails_%~nx1
tr "[:upper:]" "[:lower:]" < sorted_%~nx1 > lowered_%~nx1
uniq lowered_%~nx1 emails_extracted_from_%~nx1
del emails_%~nx1
del sorted_%~nx1
del lowered_%~nx1