<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Trajecti = "localhost";
$database_Trajecti = "pellegri-6";
$username_Trajecti = "pellegri";
$password_Trajecti = "fbPdiJDk";
$Trajecti = mysql_pconnect($hostname_Trajecti, $username_Trajecti, $password_Trajecti) or trigger_error(mysql_error(),E_USER_ERROR); 

/* Stel de character set in */
mysql_query("SET NAMES UTF8;");
setlocale(LC_ALL, 'nl_NL');

$rekeningnummer	= 'girorekening 254286';
$organisator 	= 'Trajecti Voces';
$plaats 		= 'Utrecht';
$afzender 		= 'reserveren@trajectivoces.nl';
?>