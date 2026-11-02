<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');

require_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');

Kint::$enabled_mode = false;

require_once(__DIR__ . '/modules/bestelfuncties.php');

use function PHP81_BC\strftime;

$res = $_GET['res'] ?? null;
$reservering = false;

try {
if (is_string($res) && preg_match('/\A[0-9a-f]{8}\z/', $res) === 1) {
  $reservering_query = $db->prepare("SELECT * FROM {$tabel_reserveringen} WHERE `random_id` = :res");
  $reservering_query->execute(['res' => $res]);
  $reservering = $reservering_query->fetch(PDO::FETCH_ASSOC);
}

if ($reservering) {

  $naam = str_replace('  ', ' ', ($reservering['voornaam'].' '.$reservering['tussenvoegsel'].
  ' '.$reservering['achternaam']));

  $concert_query = $db->prepare("SELECT * FROM {$tabel_concerten} WHERE `concertId` = :concertId");
  $concert_query->execute(['concertId' => $reservering['concertId']]);
  $concert = $concert_query->fetch(PDO::FETCH_ASSOC);
  if (!$concert) throw new UnexpectedValueException('Concert ontbreekt bij QR-reservering.');
    
  $nu = date('Y-m-d');
  $datumentijd = strftime("%A %e %B %Y, aanvang %H:%M", strtotime($concert['datum'].' '.$concert['tijd']));
  $naam = htmlspecialchars($naam, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $concert['concerttitel'] = htmlspecialchars($concert['concerttitel'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $datumentijd = htmlspecialchars($datumentijd, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  foreach (['aantal_vol', 'aantal_red', 'aantal_kind'] as $veld) {
    $reservering[$veld] = (int) $reservering[$veld];
  }

    if ($concert['datum'] == $nu) {
      $afvinken = $db->prepare("UPDATE {$tabel_reserveringen} SET verschenen = 1 WHERE `random_id` = :res AND (verschenen IS NULL OR verschenen = 0)");
      $afvinken->execute(['res' => $res]);
      if ($afvinken->rowCount() > 0) {
      $message = 
<<<MESSAGE
        <body class="w3-green w3-center">
            <div class="w3-content w3-panel">
              <h3>Ticketcontrole middels QR codes</h3>
              <p>Het kaartje is afgevinkt</p>
              <p>Naam: $naam | Concert: {$concert['concerttitel']} - {$datumentijd}</p>
			  <h4>Aantal plaatsen:<br>
			  gewoon {$reservering['aantal_vol']}<br>
              reductie {$reservering['aantal_red']}<br>
              kinderen {$reservering['aantal_kind']} </h4>
            </div>
        </body>
MESSAGE;
      }
      else {
      $message = 
<<<MESSAGE
        <body class="w3-red w3-center">
            <div class="w3-content w3-panel">
              <h3>Ticketcontrole middels QR codes</h3>
              <p><b>HET KAARTJE IS AL AFGEVINKT</b></p>
              <p>Naam: $naam | Concert: {$concert['concerttitel']} - {$datumentijd} </p>
              <p>Aantal plaatsen: gewoon {$reservering['aantal_vol']} | reductie {$reservering['aantal_red']} | kinderen {$reservering['aantal_kind']}</p>
            </div>
        </body>
MESSAGE;        
      }
  }
elseif ($concert['datum'] > $nu) {
        $message = 
<<<MESSAGE
        <body class="w3-pale-green w3-center">
            <div class="w3-content w3-panel">
              <h3>Het concert is binnenkort!</h3>
              <p>Naam: $naam | Concert: {$concert['concerttitel']} - {$datumentijd} </p>
              <p>Aantal plaatsen: gewoon {$reservering['aantal_vol']} | reductie {$reservering['aantal_red']} | kinderen {$reservering['aantal_kind']}</p>
              <p>Het kaartje is nog niet afgevinkt</p>
          </div>
      </body>
MESSAGE;
    }
    elseif ($concert['datum'] < $nu) {
        $message = 
<<<MESSAGE
        <body class="w3-orange w3-center">
            <div class="w3-content w3-panel">
                <h3>Het concert is al geweest!</h3>
                <p>Naam: $naam | Concert: {$concert['concerttitel']} - {$datumentijd} </p>
                <p>Aantal plaatsen: gewoon {$reservering['aantal_vol']} | reductie {$reservering['aantal_red']} | kinderen {$reservering['aantal_kind']}</p>
            </div>
        </body>
MESSAGE;
    }

}
else {
http_response_code(404);
$message = <<<MESSAGE
           <body class="w3-red w3-center">
            <div class="w3-content w3-panel">
              <h3>Deze boeking bestaat niet</h3>
            </div>
           </body>
MESSAGE; 
}
} catch (PDOException | UnexpectedValueException $e) {
    if (!error_log('[check_QR] Ticketcontrole mislukt: ' . get_class($e) . ' (code ' . $e->getCode() . ')')) {
        trigger_error('Kon de QR-controlefout niet naar de PHP-errorlog schrijven.', E_USER_WARNING);
    }
    http_response_code(500);
    $message = '<body class="w3-red w3-center"><div class="w3-content w3-panel"><h3>Ticketcontrole tijdelijk niet mogelijk</h3><p>Probeer het opnieuw of neem contact op met de organisatie.</p></div></body>';
}
?>

<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link href="<?php echo htmlspecialchars($css, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/png" href="<?php echo htmlspecialchars($favicon, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
    <title>Controle QR codes</title>
</head>
    <?php echo $message;?>
</html>