
<?php
// Connects to your Database
mysql_connect("localhost", "trajecnet", "Muziek") or die(mysql_error());
mysql_select_db("trajecnet_wervingzangerstrajectriste") or die(mysql_error());
$data = mysql_query("SELECT * FROM wervingzangerstristeespana")
or die(mysql_error());?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Zangers voor Triste Espa&ntilde;a</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link href="opmaak/Stijlblad datatabel.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table id="datatabel" >
<?php
while($info = mysql_fetch_array( $data ))
{?><tr class="herhaling"><td class="linkercel"><?php
Print "<h1>voornaam</h1>:<br />
".$info['voornaam'] ?></td><td class="standaard"><?php
Print "<h1>achternaam</h1>:<br />
".$info['achternaam'] ?></td><td class="standaard"><?php
Print "<h1>stemsoort</h1>:<br />
".$info['stemsoort'] ?></td><td class="zangervaring"><?php
Print "<h1>zangervaring</h1>:<br />
".$info['zangervaring'] ?></td><td class="standaard"><?php
Print "<h1>telefoonnummer</h1>:<br />
".$info['telefoonnummer'] ?></td><td class="standaard"><?php
Print "<h1>mobiel</h1>:<br />
".$info['mobiel'] ?></td><td class="standaard"><?php
Print "<h1>email</h1>:<br />
".$info['email'] ?></td><td class="standaard"><?php
Print "<h1>geboortejaar</h1>:<br />
".$info['geboortejaar'] ?></td><td class="standaard"><?php
Print "<h1>informatieverzoek</h1>:<br />
".$info['informatieverzoek'] ?></td></tr><?php
}
?>
</table> 
</body>
</html>