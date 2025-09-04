<?php
require_once( 'modules/bestelfuncties.php' );

// stel php in dat deze fouten weergeeft
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use chillerlan\QRCode\{QRCode, QROptions};
use Pelago\Emogrifier\CssInliner;

Kint::$enabled_mode = false; //($_SERVER['REMOTE_ADDR'] === '83.85.191.103');

if (isset($_POST['id']) AND $_POST['id'] != '') $mollie_id = $_POST['id'];
	elseif (isset($_GET['mollie_id']) AND $_GET['mollie_id'] != '') $mollie_id = $_GET['mollie_id'];
else exit('Geen geldige ID voor Mollie<br>');

$bestaat_boeking = select_query("SELECT count(*) FROM {$tabel_reserveringen} WHERE `Mollie_ID` = '{$mollie_id}';", 0);

d($_REQUEST, $_POST['id'], $url, $logo_url, $mollie_id, $bestaat_boeking);

if (!is_null($bestaat_boeking) AND $bestaat_boeking == true) {
	
	$reservering_query = "SELECT * FROM {$tabel_reserveringen} WHERE Mollie_ID = '{$mollie_id}'";
	$reservering = select_query($reservering_query, 1);
	d($reservering_query, $reservering);
	
	try {
			$payment = $mollie->payments->get($mollie_id);
			$random_id = $payment->metadata->random_id;
			d($payment->status);
			if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {
				/*
				 * The payment is paid and isn't refunded or charged back.
				 * At this point you'd probably want to start the process of delivering the product to the customer.
				 */
				$gelukt = exec_query("UPDATE {$tabel_reserveringen} SET `betaalstatus` = '{$payment->status}' WHERE random_id = '{$random_id}'");
				d($payment, $gelukt);
				$concert = select_query("SELECT * FROM {$tabel_concerten} WHERE concertId = {$reservering['concertId']};", 1);

				$datumentijd = strftime("%A %e %B %Y, aanvang %H:%M", strtotime($concert['datum'].' '.$concert['tijd']));

				$concert['euro_vol'] = euro2($concert['prijs_vol']);
				$concert['euro_red'] = euro2($concert['prijs_red']);
				$concert['euro_kind'] = euro2($concert['prijs_kind']);

				if ($reservering['aantal_vol'] == 1) $kaartjes_vol = '1 kaartje à';
				elseif ($reservering['aantal_vol'] > 1) $kaartjes_vol = "{$reservering['aantal_vol']} kaartjes à";
				if ($reservering['aantal_red'] == 1) $kaartjes_red = '1 kaartje à';
				elseif ($reservering['aantal_red'] > 1) $kaartjes_red = "{$reservering['aantal_red']} kaartjes à";
				if ($reservering['aantal_kind'] == 1) $kaartjes_kind = '1 kaartje à';
				elseif ($reservering['aantal_kind'] > 1) $kaartjes_kind = "{$reservering['aantal_kind']} kaartjes à";

				$naam = str_replace('  ', ' ', $reservering['voornaam'].' '.$reservering['tussenvoegsel'].
					' '.$reservering['achternaam']);

				$reservering['euro_totaal'] = euro2($reservering['totaal']);

				d($reservering, $concert);

				// maak de bevestigingsmail aan:
				$subject = "Kaartbestelling {$organisator} nr. {$reservering['reserveringnr']}";

				if (isset($logo_url) AND $logo_url != '') {
					$logo=$url.rawurlencode($logo_url);
					$message = "<header class=\"w3-panel-0\"><img src=\"{$logo}\" alt=\"logo\" style=\"width: 100%; max-width: 600px; height: auto; border: none;\"></header>\n";}
				else $message = '';
				$message .= "<p>Beste {$reservering['voornaam']},</p>\n";
				$message .= "</p>Hartelijk dank voor je bestelling nr. {$reservering['reserveringnr']} van concertkaartjes. De volgende gegevens zijn geregistreerd:\n\n</p>";
				$message .= "<ul><li>Naam: {$naam}</li>\n\n";
				$message .= "<li>Gereserveerd voor het concert \"<b>{$concert['concerttitel']}</b>\" op <b>{$datumentijd}</b>:</li>\n\n";
				$message .= "<ul><li>$kaartjes_vol {$concert['euro_vol']}</li>\n";
				if ($reservering['aantal_red'] > 0) $message .= "<li>$kaartjes_red {$concert['euro_red']}</li>\n";
				if ($reservering['aantal_kind'] > 0)$message .= "<li>$kaartjes_kind {$concert['euro_kind']}</li>\n";
				$message .= "</ul><li>\nHet totale verschuldigde bedrag is {$reservering['euro_totaal']}. Dit bedrag heb je reeds betaald via {$payment->method}. Neem SVP dit kaartje mee naar het concert, geprint op papier of op je telefoon. De QR code die je hieronder vindt wordt daar gescand.</li></ul>";

				if (isset($reservering['opmerkingen'])and $reservering['opmerkingen'] != "") {
					$reservering['opmerkingen'] = htmlentities(stripslashes($reservering['opmerkingen']));
					$message .= "<p>Je opmerkingen waren: {$reservering['opmerkingen']}</p>\n";
				}

				$message .= "<p>Met muzikale groet,\n\n<br><br>{$organisator}</p>\n<br>";

				$qrcode = (new QRCode)->render($url.'check_QR.php?res='.$reservering['random_id']);
				$file = fopen("qrcode.png", "w");
				$base64 = explode(',', $qrcode);
				fwrite($file, base64_decode($base64[1]));
				fclose($file);        
				d($qrcode, $base64);
			} 
		else {
			// maak een waarschuwngsmail aan:
			$subject = "Kaartbestelling {$organisator} niet gelukt";

			if (isset($logo_url) AND $logo_url != '') {
				$logo=$url.rawurlencode($logo_url);
				$message = "<header class=\"w3-panel-0\"><img src=\"{$logo}\" alt=\"logo\" style=\"width: 100%; max-width: 600px; height: auto; border: none;\"></header>\n";}
			else $message = '';
			$message .= "<p>Beste {$reservering['voornaam']},</p>\n";
			$message .= "<p>Hartelijk dank voor je bestelling van concertkaartjes. Helaas is de betaling van de kaartjes niet gelukt. Zou je het nog een keer willen proberen?</p>\n\n";

			if (isset($reservering['opmerkingen'])and $reservering['opmerkingen'] != "") {
				$reservering['opmerkingen'] = htmlentities(stripslashes($reservering['opmerkingen']));
				$message .= "<p>Je opmerkingen waren: {$reservering['opmerkingen']}</p>\n";
			}

			$message .= "<p>Met muzikale groet,\n<br><br>{$organisator}</p>\n";

		}
		}
	 catch (\Mollie\Api\Exceptions\ApiException $e) {
	 echo "API call failed: " . htmlspecialchars($e->getMessage());
	}


	// gegevens voor het mailtje 1:
	$to = $reservering[ 'email' ];
	$from = $afzender;
	$naam = str_replace('  ', ' ', $reservering['voornaam'].' '.$reservering['tussenvoegsel'].' '.$reservering['achternaam']);
	$message = CssInliner::fromHtml($message)->inlineCss($css)->render();
	d($message);


	//Create a new PHPMailer instance
	$mail = new PHPMailer;

	//Set who the message is to be sent from
	$mail->SMTPDebug = 0;
	//Set PHPMailer to use SMTP.
	$mail->Debugoutput = 'html';
	$mail->isSMTP();
	//Set SMTP host name                          
	$mail->Mailer = "smtp"; // set mailer to use SMTP
	$mail->Host = $mail_host; // specify main and backup server
	$mail->SMTPOptions = array(
	  'ssl' => array(
		'verify_peer' => false,
		'verify_peer_name' => false,
		'allow_self_signed' => true ) );
	$mail->SMTPAuth = true; // turn on SMTP authentication
	$mail->Username = $mail_username;
	$mail->Password = $mail_password;
	//If SMTP requires TLS encryption then set it
	$mail->SMTPSecure = "tls";
	//Set TCP port to connect to 
	$mail->Port = 587;

	$mail->CharSet = "UTF-8";
	$mail->Timeout = 300;
	$mail->setFrom( $from, $organisator );
	$mail->addAddress( $to, $naam );
	$mail->addBCC($from, $organisator);
	//Set the subject line
	$mail->Subject = $subject;
	//Send HTML or Plain Text email
	$mail->isHTML( true );
	if ($gelukt) {
		$mail->AddEmbeddedImage('qrcode.png', "qrcode");
		$message .= '<p>Toon deze QR code bij de kassa:<br><img class="w3-center" src="cid:qrcode" alt ="QR-code"></p>';	
	}
	$mail->Body = $message;
	$mail->AltBody = strip_tags( $message_mail );

	$mail_verzonden = $mail->send();
	d($mail, $mail_verzonden);

	if ( !$mail_verzonden ) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	}
}
else exit('Deze boeking bestaat niet in de tabel.<br');
?>


<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link href="<?php echo $css; ?>" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/png" href=<?php echo $favicon; ?>>
    <title>Bestelling kaarten</title>
</head>

<body class="w3-gray">
</body>
</html>