<?php
// Gegevens organisatie
$organisator 				= 'Trajecti Voces';
$plaats 					= 'Utrecht';
$afzender 					= 'reserveren@trajectivoces.net';
$aanspreekwijze 			= 'jij'; // default: jij;
$rekeningnummer			    = 'rekening NL90 INGB 0000 2542 86';
$rekeninghouder			    = 'Trajecti Voces';
$privacybeleid 			    = 'Privacydocumentenverklaring.php';
$tijdslot					= 0; // minuten voor volgende boeking met zelfde email
$GDPR 						= false; // Geen gegevens kaartbestellers prijsgeven volgens GDPR/AVG

// Gegevens database
$hostname 					= 'localhost';
$database 					= 'trajecti_bestel';
$username 					= 'trajecti_db';
$password 					= 'Sweelinck1';

// Gegevens tabellen
$tabel_reserveringen 	    = 'TV_reserveringen';
$tabel_concerten 			= 'TV_concerten';
$tabel_CDs 					= 'TV_CDs';
$tabel_CD_bestellingen 	    = 'TV_CD_bestellingen';
$dank_pagina 				= "dank_kaartbestelling.php";
$dank_CD 					= "dank_CD.php";
$dank_pagina 				= "dank_kaartbestelling.php";
$webhook                    = "verzend_ticket.php";
$url                        = "https://trajectivoces.net/bestellen/";

// Gegevens mail
$mail_host				 	= 'trajectivoces.net';
$mail_username			 	= 'dirkjan@pellegrina.net';
$mail_password			 	= 'Dirigent12.';

// Gegevens css
$css 						= 'css/bestel.css';
$favicon 					= '../images/logo/favicon.png';
?>