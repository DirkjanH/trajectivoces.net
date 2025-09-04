<?php
require_once( 'modules/bestelfuncties.php' );

$tabel_reserveringen = 'TV_reserveringen';

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

Kint::$enabled_mode = true; //($_SERVER['REMOTE_ADDR'] === '83.85.191.103');

$reservering_query = "SELECT * FROM {$tabel_reserveringen} WHERE (`random_id` IS NULL OR `random_id` = '') AND concertId IN (103, 104, 105) AND betaald = 1;";
$reserveringen = select_query($reservering_query);
d($reservering_query, $reserveringen);

foreach ($reserveringen as $reservering) {
	$concertquery = "SELECT * FROM {$tabel_concerten} WHERE concertId = {$reservering['concertId']};";
	$concert = select_query($concertquery, 1);
	d($concertquery, $concert);

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

	$reservering['totaal'] = $reservering['aantal_vol'] * $concert['prijs_vol'] + $reservering['aantal_red'] * $concert['prijs_red'] + $reservering['aantal_kind'] * $concert['prijs_kind'];

	//d($reservering['totaal']);

	$reservering['euro_totaal'] = euro2($reservering['totaal']);

	$random_id = bin2hex( random_bytes( 4 ) );
	$gelukt_random = exec_query("UPDATE {$tabel_reserveringen} SET `random_id` = '{$random_id}' WHERE reserveringnr = {$reservering['reserveringnr']}");
	d($random_id, $reservering['reserveringnr'], $gelukt_random);


	// maak de bevestigingsmail aan:
	$subject = "QR-code voor kaartbestelling {$organisator} nr. {$reservering['reserveringnr']}";

	if (isset($logo_url) AND $logo_url != '') {
		$logo=$url.rawurlencode($logo_url);
		$message = "<header class=\"w3-panel-0\"><img src=\"{$logo}\" alt=\"logo\" style=\"width: 100%; max-width: 600px; height: auto; border: none;\"></header>\n";}
	else $message = '';
	$message .= "<p>Beste {$reservering['voornaam']},</p>\n";
	$message .= "<p>Hartelijk dank voor je bestelling nr. {$reservering['reserveringnr']} van concertkaartjes.</p>
	<p>Omdat we bezig zijn over te stappen op een kaartverkoopsysteem met QR-codes sturen we je opnieuw een bevestiging van je bestelling. De volgende gegevens zijn geregistreerd:\n\n</p>";
	$message .= "<ul><li>Naam: {$naam}</li>\n\n";
	$message .= "<li>Gereserveerd voor het concert \"<b>{$concert['concerttitel']}</b>\" op <b>{$datumentijd}</b>:</li>\n\n<ul>";
	if ($reservering['aantal_vol'] > 0) $message .= "<li>$kaartjes_vol {$concert['euro_vol']}</li>\n";
	if ($reservering['aantal_red'] > 0) $message .= "<li>$kaartjes_red {$concert['euro_red']}</li>\n";
	if ($reservering['aantal_kind'] > 0)$message .= "<li>$kaartjes_kind {$concert['euro_kind']}</li>\n";
	$message .= "</ul><li>\nHet totale verschuldigde bedrag is {$reservering['euro_totaal']}. Dit bedrag heb je reeds betaald. Neem SVP dit kaartje met QR-code mee naar het concert, geprint op papier of op je telefoon. De QR code die je hieronder vindt wordt daar gescand.</li></ul>";

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
	$mail->AddEmbeddedImage('qrcode.png', "qrcode");
	$message .= '<p>Toon deze QR code bij de kassa:<br><img class="w3-center" src="cid:qrcode" alt ="QR-code"></p>';
	$mail->Body = $message;
	$mail->AltBody = strip_tags($message);

	$mail_verzonden = $mail->send();
	d($mail, $mail_verzonden);

	if ( !$mail_verzonden ) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	}
}
?>


<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link href="<?php echo $css; ?>" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/png" href=<?php echo $favicon; ?>>
    <title>Toezending QR codes</title>
</head>
<body class="w3-gray">
<div class="w3-content w3-white">
	<?php echo($message);?>
</div>
</body>
</html>