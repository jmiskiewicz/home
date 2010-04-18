@echo off
if "%1"=="" goto help
echo Searching for things resembling email addresses...
grep -o "[[:alnum:]+\.\_\-]*@[[:alnum:]+\.\_\-]*" %1 > emails_%1
echo Sorting results...
sort -o sorted_%1 emails_%1
echo Lowercasing results...
tr "[:upper:]" "[:lower:]" < sorted_%1 > lowered_%1
echo Removing duplicates...
uniq lowered_%1 emails_extracted_from_%1
echo [ DONE ]
goto delworkingfiles

:delworkingfiles
del emails_%1
del sorted_%1
del lowered_%1
echo Temporary files deleted
echo Your email addresses are in the file emails_extracted_from_%1
goto end

:help
echo.
echo to extract all things resembling an email address (xxxx@yyyy.zzz) from a file, 
echo use the command: %0 filename.txt
echo.
goto end

:end
