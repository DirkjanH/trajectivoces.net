<?php 
// stel php in dat deze fouten weergeeft
//ini_set('display_errors',1 );

error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';
require_once('../bestelsysteem/bestelsysteem/bestelfuncties.php'); 

if ((isset($_POST["Toevoegen"])) && ($_POST["Toevoegen"] == "Toevoegen")) {
  $insertSQL = sprintf("INSERT INTO {$tabel_concerten} (concerttitel, details, opmerking_intern, datum, tijd, plaats, prijs_vol,
  prijs_red, prijs_kind, txt_red, txt_kind, online, aantal_plaatsen, uitverkocht) VALUES (%s, %s, %s, %s, %s, %s, %F, %F, %F, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['concerttitel'], "text"),
                       GetSQLValueString($_POST['details'], "text"),
                       GetSQLValueString($_POST['opmerking_intern'], "text"),
                       GetSQLValueString($_POST['datum'], "date"),
                       GetSQLValueString($_POST['tijd'], "date"),
                       GetSQLValueString($_POST['plaats'], "text"),
                       round($_POST['prijs_vol'], 2),
                       round($_POST['prijs_red'], 2),
                       round($_POST['prijs_kind'], 2),
                       GetSQLValueString($_POST['txt_red'], "text"),
                       GetSQLValueString($_POST['txt_kind'], "text"),
                       GetSQLValueString($_POST['online'], "int"),
                       GetSQLValueString($_POST['aantal_plaatsen'], "int"),
                       GetSQLValueString($_POST['uitverkocht'], "int"));

d($insertSQL);
  $Result1 = mysqli_query($KAARTEN_DB, $insertSQL) or die(mysqli_error($KAARTEN_DB));
}

if ((isset($_POST["Wijzigen"])) && ($_POST["Wijzigen"] == "Wijzigen")) {
  $updateSQL = sprintf("UPDATE {$tabel_concerten} SET concerttitel=%s, details=%s, opmerking_intern=%s, datum=%s, tijd=%s, plaats=%s, 
  prijs_vol=%F, prijs_red=%F, prijs_kind=%F, txt_red=%s, txt_kind=%s, online=%s, aantal_plaatsen=%s, uitverkocht=%s WHERE concertId=%s",
                       GetSQLValueString($_POST['concerttitel'], "text"),
                       GetSQLValueString($_POST['details'], "text"),
                       GetSQLValueString($_POST['opmerking_intern'], "text"),
                       GetSQLValueString($_POST['datum'], "date"),
                       GetSQLValueString($_POST['tijd'], "date"),
                       GetSQLValueString($_POST['plaats'], "text"),
                       round($_POST['prijs_vol'], 2),
                       round($_POST['prijs_red'], 2),
                       round($_POST['prijs_kind'], 2),
                       GetSQLValueString($_POST['txt_red'], "text"),
                       GetSQLValueString($_POST['txt_kind'], "text"),
                       GetSQLValueString($_POST['online'], "int"),
                       GetSQLValueString($_POST['aantal_plaatsen'], "int"),
                       GetSQLValueString($_POST['uitverkocht'], "int"),
					   GetSQLValueString($_POST['concertId'], "int"));

d($updateSQL);

$Result1 = mysqli_query($KAARTEN_DB, $updateSQL) or die(mysqli_error($KAARTEN_DB));
}

if ((isset($_POST["Wissen"])) && ($_POST["Wissen"] == "Wissen") and (isset($_POST['concertId'])) and ($_POST['concertId'] != "")) {
  $deleteSQL = sprintf("DELETE FROM {$tabel_concerten} WHERE concertId=%s",
                       GetSQLValueString($_POST['concertId'], "int"));

  $Result1 = mysqli_query($KAARTEN_DB, $deleteSQL) or die(mysqli_error($KAARTEN_DB));
}

// begin Recordset
$concertId = "-1";
if (isset($_GET['concertId'])) {
  	if (!isset($_POST["leegmaken"])) $concertId = $_GET['concertId'];
	$query_concert = sprintf("SELECT * FROM {$tabel_concerten} WHERE concertId = %s", $concertId);
  	$concert_tabel = mysqli_query($KAARTEN_DB, $query_concert) or die(mysqli_error($KAARTEN_DB));
	$concert = mysqli_fetch_assoc($concert_tabel);
}
// end Recordset

?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<title>Update tabel concerten</title>
<link href="<?php echo $css; ?>" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>
<body>
<div id="main">
  <form action="<?php echo $editFormAction; ?>" method="get" name="zoek" target="_self" id="zoek">
    <table width="80%" align="center">
      <tr>
        <td width="50%"><div align="right">concertId =
            <input name="concertId" type="text" size="5" />
          </div></td>
        <td width="50%"><input type="submit" name="Submit" value="Zoek"></td>
      </tr>
    </table>
  </form>
</div>
  <form name="concert" method="POST" id="concert" action="<?php echo $editFormAction; ?>">

    <table width="100%" align="left">
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>concertId:</td>
        <td colspan="2"><input type="text" name="concertId" value="<?php echo $concert['concertId']; ?>" size="32" /></td>
        <td width="100" align="right" nowrap>Prijs vol:<br />
          <span class="style1">NB: gebruik '.'</span></td>
        <td width="70%"><input type="text" name="prijs_vol" value="<?php echo $concert['prijs_vol']; ?>" size="40" /> 
          <br /></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>Concerttitel:</td>
        <td><input type="text" name="concerttitel" value="<?php echo stripslashes($concert['concerttitel']); ?>" size="32" /></td>
        <td width="3%" align="right" nowrap="nowrap">Online:
          <input type="checkbox" name="online" value="1" <?php if ($concert['online'] == 1) 
		 echo 'checked'; ?> /></td>
        <td width="100" align="right" nowrap>Prijs red.:</td>
        <td width="70%"><input type="text" name="prijs_red" value="<?php echo $concert['prijs_red']; ?>" size="10" />
        ; korting voor 
        <input type="text" name="txt_red" value="<?php echo $concert['txt_red']; ?>" size="40" /></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>Datum (yyyy-mm-dd):</td>
        <td width="300" colspan="2"><input type="text" name="datum" value="<?php echo $concert['datum']; ?>" size="40" /></td>
        <td align="right" nowrap="nowrap">Prijs kind:</td>
        <td width="70%"><input name="prijs_kind" type="text" id="prijs_kind" value="<?php echo $concert['prijs_kind']; ?>" size="10" />
; korting voor
  <input type="text" name="txt_kind" value="<?php echo $concert['txt_kind']; ?>" size="40" /></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>Tijd (hh:mm):</td>
        <td width="300" colspan="2"><input type="text" name="tijd" value="<?php echo $concert['tijd']; ?>" size="40" /></td>
        <td width="100" align="right" nowrap>Aantal plaatsen: </td>
        <td width="70%"><input name="aantal_plaatsen" type="text" id="aantal_plaatsen" value="<?php 
		 echo $concert['aantal_plaatsen']; ?>" size="10" /></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>Plaats:</td>
        <td width="300" colspan="2"><input type="text" name="plaats" value="<?php echo stripslashes($concert['plaats']); ?>" size="40" /></td>
        <td width="100" align="right" nowrap>Uitverkocht:</td>
        <td width="70%"><input name="uitverkocht" type="checkbox" id="uitverkocht" value="1" <?php 
		 if ($concert['uitverkocht'] == 1) echo 'checked'; ?>></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" valign="top" nowrap>Publieke details:</td>
        <td colspan="2"><textarea name="details" cols="40" rows="3"><?php echo stripslashes($concert['details']); ?></textarea>        </td>
        <td width="100" align="right" valign="top">Interne opmerking: </td>
        <td width="70%" align="left" valign="top"><textarea name="opmerking_intern" cols="40" rows="3" id="opmerking_intern"><?php echo stripslashes($concert['opmerking_intern']); ?></textarea></td>
      </tr>
      <tr valign="baseline">
        <td width="10%" align="right" nowrap>&nbsp;</td>
        <td colspan="2"><input name="Toevoegen" type="submit" id="Toevoegen" value="Toevoegen" />
          <input name="Wijzigen" type="submit" id="Wijzigen" value="Wijzigen" />
          <input name="Wissen" type="submit" id="Wissen" value="Wissen" /></td>
        <td width="100" align="right"><input name="leegmaken" type="submit" id="leegmaken" value="Leegmaken" /></td>
        <td width="70%">&nbsp;</td>
      </tr>
    </table>
  </form>
</body>
</html>