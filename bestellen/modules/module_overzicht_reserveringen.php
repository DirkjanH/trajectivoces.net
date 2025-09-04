<?php
// stel php in dat deze fouten weergeeft
//ini_set('display_errors', 1);
error_reporting( E_ALL );

require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/vendor/autoload.php' );

Kint::$enabled_mode = false; //($_SERVER[ 'REMOTE_ADDR' ] === '83.85.191.103');
Kint\ Renderer\ RichRenderer::$folder = false;

session_start();
require_once( 'bestelfuncties.php' );

$concerten = select_query( "SELECT * FROM {$tabel_concerten} WHERE online = 1 ORDER BY datum" );
d( $concerten );
foreach ( $concerten AS $row ) {
	$datum = strftime( "%A %e %B %Y", strtotime( $row[ 'datum' ] ) );
	$tijd = strftime( "%H:%M", strtotime( $row[ 'tijd' ] ) );
	$row[ 'dag' ] = ( strtotime( $row[ 'datum' ] ) - time() ) / ( 60 * 60 * 24 );
	$row[ 'euro_vol' ] = euro2( $row[ 'prijs_vol' ] );
	$row[ 'euro_red' ] = euro2( $row[ 'prijs_red' ] );
	$row[ 'euro_kind' ] = euro2( $row[ 'prijs_kind' ] );
	$row[ 'concert' ] = "<b>{$row['concerttitel']}</b>, te {$row['plaats']}, op {$datum}";
	if ( $tijd != '00:00' )$row[ 'concert' ] .= ", {$tijd} uur";
	if ( !( $row[ 'prijs_vol' ] > 0 or $row[ 'prijs_red' ] > 0 ) )
		$row[ 'entree' ] = "toegang gratis (collecte na afloop)";
	else
		$row[ 'entree' ] = "entree {$row['euro_vol']}";
	if ( $row[ 'prijs_red' ] > 0 )
		if ( isset( $row[ 'txt_red' ] )AND $row[ 'txt_red' ] != '' )$row[ 'entree' ] .= ' | ' . $row[ 'txt_red' ] . " {$row['euro_red']}";
		else $row[ 'entree' ] .= " | CJP/studenten {$row['euro_red']}";
	if ( $row[ 'prijs_kind' ] > 0 )
		if ( isset( $row[ 'txt_kind' ] )AND $row[ 'txt_kind' ] != '' )$row[ 'entree' ] .= ' | ' . $row[ 'txt_kind' ] . " {$row['euro_kind']}";
		else $row[ 'entree' ] .= " | kinderen tot 12 jaar {$row['euro_kind']}";
	$concert[ $row[ 'concertId' ] ] = $row;
}

$res = select_query( "SELECT MAX(reserveringnr) FROM {$tabel_reserveringen}", 0 ) + 1;

d( $concert );

// begin Recordset
$query_reserveringen = "SELECT reserveringnr, CONCAT_WS(' ', voornaam, tussenvoegsel, achternaam) as naam, r.plaats, telefoon, r.email, r.concertId, aantal_vol, aantal_red, publiciteit, aanbrenger, opmerkingen, flyers FROM {$tabel_reserveringen} AS r, {$tabel_concerten} AS c WHERE r.concertId = c.concertId AND online = 1 ORDER BY reserveringnr ASC";
$reserveringen = select_query( $query_reserveringen );
// end Recordset

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Overzicht reserveringen</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<META HTTP-EQUIV=Refresh CONTENT="900; URL=" <?php echo $_SERVER[ 'PHP_SELF']; ?>"">
	<link href="<?php echo $css; ?>" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/png" href=<?php echo $favicon; ?>>
</head>

<body>
	<div class="w3-panel w3-margin-top">
		<p>
			<?php foreach ($concert as $pl) {
				$query_aantal = "SELECT sum(aantal_vol) as aantal_vol, sum(aantal_red) as aantal_red FROM {$tabel_reserveringen} 
				WHERE concertId={$pl['concertId']}";
				$aantal = select_query($query_aantal);
				d($aantal);
				$vol = (int)$aantal['aantal_vol']; $red = (int)$aantal['aantal_red']; $som = $vol + $red;
				echo "{$pl['concert_kort']}: Aantal volle kaarten: {$vol}; aantal red. kaarten: {$red}; samen: {$som}<br>";
				$totaal[vol] += $vol;
				$totaal[red] += $red;
				$totaal[totaal] += $som;
			}
	echo "<b>Totaal via de site verkochte kaarten: $totaal[vol] x vol;  $totaal[red] x red.; samen: $totaal[totaal]</b>";
// end Recordset
?>
		</p>
		<h1>Gereserveerde kaarten: </h1>
		<table id="res" class="w3-table-all">
			<tr>
				<th width="1%">Res. nr.:</th>
				<th>Naam:</th>
				<th>Plaats:</th>
				<th>Telefoon:</th>
				<th>Email:</th>
				<th width="20%">Concert:</th>
				<th width="1%">Aantal kaarten vol:</th>
				<th width="1%">Aantal kaarten red.:</th>
				<th>Weet ervan via: </th>
				<th>Aanbrenger:</th>
				<th>Opmerkingen:</th>
				<th>Wil digiflyers:</th>
			</tr>
			<?php
			foreach ( $reserveringen as $res ) {

				$c = $res[ 'concertId' ];
				$concertnaam = $concert[ $c ][ 'concert_kort' ];
				?>
			<tr>
				<td>
					<div align="right">
						<?php echo $res['reserveringnr']; ?>
					</div>
				</td>
				<td>
					<?php echo $res['naam']; ?>&nbsp;</td>
				<td>
					<?php echo $res['plaats']; ?>&nbsp;</td>
				<td>
					<?php echo $res['telefoon']; ?>&nbsp;</td>
				<td>
					<?php echo $res['email']; ?>&nbsp;</td>
				<td width="20%">
					<?php echo $concertnaam; ?>&nbsp;</td>
				<td width="1%">
					<div align="center">
						<?php echo $res['aantal_vol']; ?>
					</div>
				</td>
				<td width="1%">
					<div align="center">
						<?php echo $res['aantal_red']; ?>
					</div>
				</td>
				<td>
					<?php echo $res['publiciteit']; ?>&nbsp;</td>
				<td>
					<?php echo $res['aanbrenger']; ?>&nbsp;</td>
				<td>
					<?php echo $res['opmerkingen']; ?>&nbsp;</td>
				<td>
					<div align="center">
						<?php if ($res['flyers'] == 1) echo 'ja'; ?> &nbsp;
					</div>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
	<p class="klein">laatste verversing: <?php echo strftime("%c"); ?></p>
	</div>
</body>
</html>