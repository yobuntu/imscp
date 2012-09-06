; <?php exit; ?> DO NOT REMOVE THIS LINE
; this file for documentation purposes; DO NOT COPY this file to config.ini.php;
; the config.ini.php is normally created during the installation process
; (see http://piwik.org/docs/installation)
; when this file is absent it triggers the Installation process to create
; config.ini.php; that file will contain the superuser and database access info

[superuser]
login			= admin
password		= {SUPERUSERMD5}
email			= {DEFAULT_ADMIN_ADDRESS}

[database]
host			= {DB_HOST}
username		= {DB_USER}
password		= {DB_PASS}
dbname			= {DB_NAME}
adapter			= MYSQLI
tables_prefix	= ""
charset		= utf8
