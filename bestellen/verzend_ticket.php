<?php
$ticket_stage = 'initialisatie';
$ticket_context = [];
$ticket_log = function ($stage, array $details = []) use (&$ticket_stage, &$ticket_context) {
	$ticket_stage = $stage;
	$entry = json_encode(
		array_merge(['tijd' => date(DATE_ATOM), 'stap' => $stage], $ticket_context, $details),
		JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
	);
	if (!error_log('[verzend_ticket] ' . $entry)) {
		trigger_error('Kon de webhookdiagnose niet naar de PHP-errorlog schrijven.', E_USER_WARNING);
	}
};
register_shutdown_function(function () use (&$ticket_stage, $ticket_log) {
	$error = error_get_last();
	if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
		$ticket_log('fatale_fout', [
			'laatste_stap' => $ticket_stage,
			'fout' => $error['message'],
			'bestand' => $error['file'],
			'regel' => $error['line']
		]);
	}
});
$ticket_log('initialisatie');

// stel php in dat deze fouten weergeeft
ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php');
Kint::$enabled_mode = false;

require_once('modules/bestelfuncties.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use chillerlan\QRCode\{QRCode, QROptions};
use Pelago\Emogrifier\CssInliner;
use function PHP81_BC\strftime;

if (isset($_POST['id']) and $_POST['id'] != '') $mollie_id = $_POST['id'];
elseif (isset($_GET['mollie_id']) and $_GET['mollie_id'] != '') $mollie_id = $_GET['mollie_id'];
else {
	$ticket_log('geen_mollie_id');
	exit('Geen geldige ID voor Mollie<br>');
}

$ticket_context['mollie_id'] = $mollie_id;
$ticket_log('boeking_opzoeken');

$bestaat_boeking = select_query("SELECT count(*) FROM {$tabel_reserveringen} WHERE `Mollie_ID` = '{$mollie_id}';", 0);

d($_REQUEST, $_POST['id'], $url, $logo_url, $mollie_id, $bestaat_boeking);

if (!is_null($bestaat_boeking) and $bestaat_boeking == true) {

	$reservering_query = "SELECT * FROM {$tabel_reserveringen} WHERE `Mollie_ID` = '{$mollie_id}';";
	$reservering = select_query($reservering_query, 1);
	d($reservering_query, $reservering);
	$ticket_context['reservering'] = $reservering['reserveringnr'];

	try {
		$ticket_log('betaling_ophalen');
		$payment = $mollie->payments->get($mollie_id);
		$random_id = $payment->metadata->random_id;
		d($payment->status);
		$payment_succeeded = $payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks();
		$payment_definitively_failed = in_array($payment->status, ['failed', 'canceled', 'expired'], true);
		$ticket_log('betaling_gecontroleerd', [
			'status' => $payment->status,
			'payment_succeeded' => $payment_succeeded,
			'payment_definitively_failed' => $payment_definitively_failed
		]);

		if (!$payment->isPaid() && !$payment_definitively_failed) {
			$ticket_log('opnieuw_proberen_geen_definitieve_betaling');
			http_response_code(503);
			exit;
		}

		if (!$payment_succeeded && !$payment_definitively_failed) {
			$ticket_log('overgeslagen_terugbetaald_of_teruggeboekt');
			http_response_code(200);
			exit;
		}

		$gelukt = false;
		if ($payment_succeeded) {
			$ticket_log('bevestiging_opbouwen');
			/*
				 * The payment is paid and isn't refunded or charged back.
				 * At this point you'd probably want to start the process of delivering the product to the customer.
				 */
			$gelukt = exec_query("UPDATE {$tabel_reserveringen} SET `betaalstatus` = '{$payment->status}' WHERE `Mollie_ID` = '{$mollie_id}';");
			d($payment, $gelukt);
			$reservering_query = "SELECT * FROM {$tabel_reserveringen} WHERE `Mollie_ID` = '{$mollie_id}';";
			$reservering = select_query($reservering_query, 1);
			d($reservering_query, $reservering);

			$concert = select_query("SELECT * FROM {$tabel_concerten} WHERE concertId = {$reservering['concertId']};", 1);

			$concert_timestamp = strtotime($concert['datum'] . ' ' . $concert['tijd']);
			$concert_plaats = htmlspecialchars($concert['plaats'], ENT_QUOTES, 'UTF-8');
			$datumentijd = strftime("%A %e %B %Y", $concert_timestamp)
				. " te {$concert_plaats}, aanvang " . strftime("%H:%M", $concert_timestamp);

			$concert['euro_vol'] = euro2($concert['prijs_vol']);
			$concert['euro_red'] = euro2($concert['prijs_red']);
			$concert['euro_kind'] = euro2($concert['prijs_kind']);

			if ($reservering['aantal_vol'] == 1) $kaartjes_vol = '1 kaartje à';
			elseif ($reservering['aantal_vol'] > 1) $kaartjes_vol = "{$reservering['aantal_vol']} kaartjes à";
			if ($reservering['aantal_red'] == 1) $kaartjes_red = '1 kaartje à';
			elseif ($reservering['aantal_red'] > 1) $kaartjes_red = "{$reservering['aantal_red']} kaartjes à";
			if ($reservering['aantal_kind'] == 1) $kaartjes_kind = '1 kaartje à';
			elseif ($reservering['aantal_kind'] > 1) $kaartjes_kind = "{$reservering['aantal_kind']} kaartjes à";

			$naam = str_replace('  ', ' ', $reservering['voornaam'] . ' ' . $reservering['tussenvoegsel'] .
				' ' . $reservering['achternaam']);

			$reservering['euro_totaal'] = euro2($reservering['totaal']);

			d($reservering, $concert);

			// maak de bevestigingsmail aan:
			$subject = "Kaartbestelling {$organisator} nr. {$reservering['reserveringnr']}";

			if (isset($logo_url) and $logo_url != '') {
				$logo = $url . rawurlencode($logo_url);
				$message = "<header class=\"w3-panel-0\"><img src=\"{$logo}\" alt=\"logo\" style=\"width: 100%; max-width: 600px; height: auto; border: none;\"></header>\n";
			} else $message = '';
			$message .= "<p>Beste {$reservering['voornaam']},</p>\n";
			$message .= "</p>Hartelijk dank voor je bestelling nr. {$reservering['reserveringnr']} van concertkaartjes. De volgende gegevens zijn geregistreerd:\n\n</p>";
			$message .= "<ul><li>Naam: {$naam}</li>\n\n";
			$message .= "<li>Gereserveerd voor het concert \"<b>{$concert['concerttitel']}</b>\" op <b>{$datumentijd}</b>:</li>\n\n<ul>";
			if ($reservering['aantal_vol'] > 0) $message .= "<li>$kaartjes_vol {$concert['euro_vol']}</li>\n";
			if ($reservering['aantal_red'] > 0) $message .= "<li>$kaartjes_red {$concert['euro_red']}</li>\n";
			if ($reservering['aantal_kind'] > 0) $message .= "<li>$kaartjes_kind {$concert['euro_kind']}</li>\n";
			$message .= "</ul><li>\nHet totale verschuldigde bedrag is {$reservering['euro_totaal']}. Dit bedrag heb je reeds betaald via {$payment->method}. Neem SVP dit kaartje mee naar het concert, geprint op papier of op je telefoon. De QR code die je hieronder vindt wordt daar gescand.</li></ul>";

			if (isset($reservering['opmerkingen']) and $reservering['opmerkingen'] != "") {
				$reservering['opmerkingen'] = htmlentities(stripslashes($reservering['opmerkingen']));
				$message .= "<p>Je opmerkingen waren: {$reservering['opmerkingen']}</p>\n";
			}

			$message .= "<p>Met muzikale groet,\n\n<br><br>{$organisator}</p>\n<br>";

			$ticket_log('qr_code_maken');
			$qrcode = (new QRCode)->render($url . 'check_QR.php?res=' . $reservering['random_id']);
			if (!preg_match('#^data:image/png;base64,(.+)$#s', $qrcode, $qr_match)) {
				throw new RuntimeException('De QR-code is niet als PNG-data gegenereerd.');
			}
			$qr_image_data = base64_decode($qr_match[1], true);
			if ($qr_image_data === false) {
				throw new RuntimeException('De PNG-data van de QR-code is ongeldig.');
			}
		} else {
			// maak een waarschuwngsmail aan:
			$subject = "Kaartbestelling {$organisator} niet gelukt";

			if (isset($logo_url) and $logo_url != '') {
				$logo = $url . rawurlencode($logo_url);
				$message = "<header class=\"w3-panel-0\"><img src=\"{$logo}\" alt=\"logo\" style=\"width: 100%; max-width: 600px; height: auto; border: none;\"></header>\n";
			} else $message = '';
			$message .= "<p>Beste {$reservering['voornaam']},</p>\n";
			$message .= "<p>Hartelijk dank voor je bestelling van concertkaartjes. Helaas lijkt de door jou gestarte betaling te zijn mislukt. Mogelijk heb je de betaling zelf geannuleerd of is er iets mis gegaan. De kaartjes zijn dus nog niet gereserveerd en er is nog niets afgeschreven. We raden je aan om de bestelling en betaling nogmaals uit te voeren.</p>\n";
			$message .= "<p>Het kan zijn dat een volgende bestelling wel succesvol is geweest. In dat geval krijg je een bevestigingsmail met een QR code voor je concertkaartje. Dan is alles goed gegaan en kun je deze mail met een gerust hart negeren.</p>\n\n";
			if (isset($reservering['opmerkingen']) and $reservering['opmerkingen'] != "") {
				$reservering['opmerkingen'] = htmlentities(stripslashes($reservering['opmerkingen']));
				$message .= "<p>Je opmerkingen waren: {$reservering['opmerkingen']}</p>\n";
			}

			$message .= "<p>Met muzikale groet,\n<br><br>{$organisator}</p>\n";
		}
	} catch (\Mollie\Api\Exceptions\ApiException $e) {
		$ticket_log('mollie_api_fout', ['fout' => $e->getMessage()]);
		echo "API call failed: " . htmlspecialchars($e->getMessage());
		exit;
	}


	// gegevens voor het mailtje 1:
	$to = $reservering['email'];
	$from = $afzender;
	$naam = str_replace('  ', ' ', $reservering['voornaam'] . ' ' . $reservering['tussenvoegsel'] . ' ' . $reservering['achternaam']);
	$ticket_log('mail_opmaken');
	$message = CssInliner::fromHtml($message)->inlineCss($css)->render();
	$page_message = $message;
	d($message);


	//Create a new PHPMailer instance
	$ticket_log('smtp_voorbereiden');
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
			'allow_self_signed' => true
		)
	);
	$mail->SMTPAuth = true; // turn on SMTP authentication
	$mail->Username = $mail_username;
	$mail->Password = $mail_password;
	//If SMTP requires TLS encryption then set it
	$mail->SMTPSecure = "tls";
	//Set TCP port to connect to 
	$mail->Port = 587;

	$mail->CharSet = "UTF-8";
	$mail->Timeout = 300;
	$mail->setFrom($from, $organisator);
	$mail->addAddress($to, $naam);
	$mail->addBCC($from, $organisator);
	//Set the subject line
	$mail->Subject = $subject;
	//Send HTML or Plain Text email
	$mail->isHTML(true);
	if ($gelukt) {
		$mail->addStringEmbeddedImage($qr_image_data, 'qrcode', 'qrcode.png', PHPMailer::ENCODING_BASE64, 'image/png');
		$message .= '<p>Toon deze QR code bij de kassa:<br><img class="w3-center" src="cid:qrcode" alt ="QR-code"></p>';
		$page_message .= '<p>Toon deze QR code bij de kassa:<br><img class="w3-center" src="' .
			htmlspecialchars($qrcode, ENT_QUOTES, 'UTF-8') . '" alt="QR-code"></p>';
	}
	$mail->Body = $message;
	$mail->AltBody = strip_tags($message);

	$ticket_log('smtp_verzenden');
	$mail_verzonden = $mail->send();
	$ticket_log($mail_verzonden ? 'smtp_geaccepteerd' : 'smtp_mislukt', [
		'fout' => $mail_verzonden ? null : $mail->ErrorInfo
	]);
	d($mail, $mail_verzonden);

	if (!$mail_verzonden) {
		$mail_error = preg_replace('/[\r\n\t]+/', ' ', $mail->ErrorInfo);
		$log_entry = sprintf(
			'[verzend_ticket] E-mail verzenden mislukt; reservering=%s; mollie_id=%s; fout=%s',
			$reservering['reserveringnr'],
			$mollie_id,
			$mail_error
		);
		if (!error_log($log_entry)) {
			trigger_error('Kon de fout bij het verzenden van de kaartbestellingsmail niet naar de PHP-errorlog schrijven.', E_USER_WARNING);
		}
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$ticket_log('verzendtijd_opslaan');
		$timestamp_opgeslagen = exec_query(
			"UPDATE {$tabel_reserveringen} SET `email_verzonden` = NOW()"
				. " WHERE `Mollie_ID` = " . quote($mollie_id)
				. " AND `reserveringnr` = " . quote($reservering['reserveringnr'])
		);
		if (!$timestamp_opgeslagen) {
			error_log(sprintf(
				'[verzend_ticket] E-mail is verzonden, maar timestamp opslaan is mislukt; reservering=%s; mollie_id=%s',
				$reservering['reserveringnr'],
				$mollie_id
			));
		}
		$ticket_log($timestamp_opgeslagen ? 'verzendtijd_opgeslagen' : 'verzendtijd_opslaan_mislukt');
	}
} else {
	$ticket_log('boeking_niet_gevonden');
	exit('Deze boeking bestaat niet in de tabel.<br');
}
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
	<div class="w3-content"> <?php echo ($page_message ?? $message); ?> </div>
</body>

</html>