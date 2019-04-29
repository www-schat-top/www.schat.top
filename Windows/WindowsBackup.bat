@echo off

::Set Date Variable
  set d=%date:~0,4%%date:~5,2%%date:~8,2%
  set t=%time:~0,2%%time:~3,2%%time:~6,2%

  cd /d d:\\backup
  mkdir %d% 

::Backup IIS
  c:\\windows\\system32\\inetsrv\\appcmd list site > d:\\backup\\%d%\\sites.xls
  
::Backup SQLServer DB
  PowerShell.exe -Command "Backup-SqlDatabase -ServerInstance localhost -Database mydb -BackupFile d:\\backup\\%d%\\mydb.bak" 

::Backup
  "C:\Program Files\7-Zip\7z.exe" a "d:\\backup\\%d%\\myserver007.7z"  d:\\backup\\%d%
  
::del files 20 days ago 
  forfiles  /p d:\backup  /m *.7z  /d -20 /c "cmd /c del @file"

::Ftp Update
D:\\backup\\curl\\curl.exe -u user:passwd  --ftp-create-dirs -T  "d:\\backup\\%d%\\1.210.7z"  ftp://8.8.8.8:21/%d%/ 
