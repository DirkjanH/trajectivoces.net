<?php
require_once __DIR__ . '/modules/bestelfuncties.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

Kint::$enabled_mode = false;

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validDate(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

function validTime(string $value): bool
{
    if ($value === '') {
        return true;
    }
    $time = DateTimeImmutable::createFromFormat('!H:i', $value);
    return $time && $time->format('H:i') === $value;
}

function decimalValue($value): ?float
{
    $value = str_replace(',', '.', trim((string) $value));
    if (!preg_match('/^(?:0|[1-9]\d*)(?:\.\d{1,2})?$/', $value)) {
        return null;
    }
    $number = (float) $value;
    return $number >= 0 && $number <= 10000 ? $number : null;
}

function safePostString(string $name, int $maxLength): string
{
    $value = trim((string) ($_POST[$name] ?? ''));
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $maxLength, 'UTF-8')
        : substr($value, 0, $maxLength);
}

if (empty($_SESSION['concert_admin_csrf'])) {
    $_SESSION['concert_admin_csrf'] = bin2hex(random_bytes(32));
}

$errors = [];
$notice = '';
if (!empty($_SESSION['concert_admin_notice'])) {
    $notice = (string) $_SESSION['concert_admin_notice'];
    unset($_SESSION['concert_admin_notice']);
}

if (!isset($_SESSION['zoeknaam'])) {
    $_SESSION['zoeknaam'] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['concert_admin_csrf'], (string) $_POST['csrf'])) {
        $errors[] = 'De sessie is verlopen. Probeer het formulier opnieuw.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'zoeken') {
            $_SESSION['zoeknaam'] = safePostString('zoeknaam', 100);
        } elseif ($action === 'wissenzoek') {
            $_SESSION['zoeknaam'] = '';
        } elseif ($action === 'leegmaken') {
            header('Location: ' . e($_SERVER['PHP_SELF']));
            exit;
        } elseif ($action === 'Wissen') {
            $concertId = filter_var($_POST['concertId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($concertId === false) {
                $errors[] = 'Geef een geldige concertId op.';
            } else {
                try {
                    $statement = $db->prepare("DELETE FROM {$tabel_concerten} WHERE concertId = :concertId");
                    $statement->execute(['concertId' => $concertId]);
                    $_SESSION['concert_admin_notice'] = 'Concert is gewist.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } catch (PDOException $exception) {
                    error_log('Concert delete failed: ' . $exception->getMessage());
                    $errors[] = 'Het concert kon niet worden gewist.';
                }
            }
        } elseif ($action === 'Toevoegen' || $action === 'Wijzigen') {
            $concertId = $action === 'Wijzigen'
                ? filter_var($_POST['concertId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : null;

            $fields = [
                'concerttitel' => [255, 'Concerttitel'],
                'details' => [65535, 'Publieke details'],
                'opmerking_intern' => [65535, 'Interne opmerking'],
                'plaats' => [255, 'Plaats'],
                'txt_red' => [255, 'Tekst kortingsprijs'],
                'txt_kind' => [255, 'Tekst kinderprijs'],
            ];
            $values = [];
            foreach ($fields as $name => [$maxLength, $label]) {
                $value = safePostString($name, $maxLength);
                if ($name === 'concerttitel' && $value === '') {
                    $errors[] = 'Concerttitel is verplicht.';
                }
                $values[$name] = $value;
            }

            $datum = safePostString('datum', 10);
            $tijd = safePostString('tijd', 5);
            if (!validDate($datum)) {
                $errors[] = 'Geef een geldige datum op in het formaat jjjj-mm-dd.';
            }
            if (!validTime($tijd)) {
                $errors[] = 'Geef een geldige tijd op in het formaat uu:mm.';
            }

            $prices = [];
            foreach (['prijs_vol', 'prijs_red', 'prijs_kind'] as $name) {
                $price = decimalValue($_POST[$name] ?? '');
                if ($price === null) {
                    $errors[] = 'Gebruik voor prijzen een geldige waarde met maximaal twee decimalen.';
                    break;
                }
                $prices[$name] = $price;
            }

            $aantalPlaatsen = filter_var($_POST['aantal_plaatsen'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            if ($aantalPlaatsen === false) {
                $errors[] = 'Aantal plaatsen moet een positief geheel getal zijn.';
            }

            if ($action === 'Wijzigen' && $concertId === false) {
                $errors[] = 'Geef een geldige concertId op.';
            }

            if (!$errors) {
                $data = [
                    'concerttitel' => $values['concerttitel'],
                    'details' => $values['details'],
                    'opmerking_intern' => $values['opmerking_intern'],
                    'datum' => $datum,
                    'tijd' => $tijd,
                    'plaats' => $values['plaats'],
                    'prijs_vol' => $prices['prijs_vol'],
                    'prijs_red' => $prices['prijs_red'],
                    'prijs_kind' => $prices['prijs_kind'],
                    'txt_red' => $values['txt_red'],
                    'txt_kind' => $values['txt_kind'],
                    'online' => isset($_POST['online']) ? 1 : 0,
                    'aantal_plaatsen' => $aantalPlaatsen,
                    'uitverkocht' => isset($_POST['uitverkocht']) ? 1 : 0,
                ];

                try {
                    if ($action === 'Toevoegen') {
                        $statement = $db->prepare(
                            "INSERT INTO {$tabel_concerten}
                            (concerttitel, details, opmerking_intern, datum, tijd, plaats, prijs_vol,
                             prijs_red, prijs_kind, txt_red, txt_kind, online, aantal_plaatsen, uitverkocht)
                            VALUES (:concerttitel, :details, :opmerking_intern, :datum, :tijd, :plaats, :prijs_vol,
                             :prijs_red, :prijs_kind, :txt_red, :txt_kind, :online, :aantal_plaatsen, :uitverkocht)"
                        );
                        $statement->execute($data);
                        $concertId = (int) $db->lastInsertId();
                        $_SESSION['concert_admin_notice'] = 'Concert is toegevoegd.';
                    } else {
                        $data['concertId'] = $concertId;
                        $statement = $db->prepare(
                            "UPDATE {$tabel_concerten} SET
                            concerttitel = :concerttitel, details = :details, opmerking_intern = :opmerking_intern,
                            datum = :datum, tijd = :tijd, plaats = :plaats, prijs_vol = :prijs_vol,
                            prijs_red = :prijs_red, prijs_kind = :prijs_kind, txt_red = :txt_red,
                            txt_kind = :txt_kind, online = :online, aantal_plaatsen = :aantal_plaatsen,
                            uitverkocht = :uitverkocht
                            WHERE concertId = :concertId"
                        );
                        $statement->execute($data);
                        $_SESSION['concert_admin_notice'] = 'Concert is gewijzigd.';
                    }

                    header('Location: ' . $_SERVER['PHP_SELF'] . '?concertId=' . (int) $concertId);
                    exit;
                } catch (PDOException $exception) {
                    error_log('Concert save failed: ' . $exception->getMessage());
                    $errors[] = 'Het concert kon niet worden opgeslagen.';
                }
            }
        } else {
            $errors[] = 'Ongeldige actie.';
        }
    }
}

$concertId = null;
if (isset($_GET['concertId'])) {
    $concertId = filter_var($_GET['concertId'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($concertId === false) {
        $concertId = null;
    }
}

$concert = [
    'concertId' => '', 'concerttitel' => '', 'details' => '', 'opmerking_intern' => '',
    'datum' => '', 'tijd' => '', 'plaats' => '', 'prijs_vol' => '',
    'prijs_red' => '', 'prijs_kind' => '', 'txt_red' => '', 'txt_kind' => '',
    'online' => 0, 'aantal_plaatsen' => '', 'uitverkocht' => 0,
];

if ($concertId !== null) {
    $statement = $db->prepare("SELECT * FROM {$tabel_concerten} WHERE concertId = :concertId");
    $statement->execute(['concertId' => $concertId]);
    $found = $statement->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        $concert = array_merge($concert, $found);
    }
}

$zoekterm = '%' . $_SESSION['zoeknaam'] . '%';
$statement = $db->prepare(
    "SELECT * FROM {$tabel_concerten}
     WHERE datum LIKE :term OR concerttitel LIKE :term OR plaats LIKE :term
     ORDER BY datum DESC"
);
$statement->execute(['term' => $zoekterm]);
$concerten = $statement->fetchAll(PDO::FETCH_ASSOC);
$toon_navigatie = ($_SESSION['zoeknaam'] !== '') || isset($_GET['concertId'])
    || in_array($_POST['action'] ?? '', ['zoeken', 'wissenzoek'], true);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Concertonderhoud <?= e($organisator ?? '') ?></title>
    <link href="<?= e($css ?? '') ?>" rel="stylesheet">
    <link href="<?= e($bestellijst_css ?? '') ?>" rel="stylesheet">
    <?php if (!empty($favicon)): ?>
        <link rel="icon" type="image/png" href="<?= e($favicon) ?>">
    <?php endif; ?>
    <style>
        .concert-admin { box-sizing: border-box; max-width: 960px; margin: 0 auto; padding: 16px; }
        .concert-admin * { box-sizing: border-box; }
        .concert-card { background: #fff; border: 1px solid #d7d7d7; border-radius: 10px; box-shadow: 0 2px 8px rgb(0 0 0 / 8%); padding: 20px; }
        .concert-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .concert-field { min-width: 0; }
        .concert-field.full { grid-column: 1 / -1; }
        .concert-field label, .concert-field legend { display: block; margin-bottom: 6px; font-weight: 700; }
        .concert-field input, .concert-field textarea { width: 100%; min-height: 42px; padding: 9px 10px; }
        .concert-field textarea { min-height: 110px; resize: vertical; }
        .concert-checks { display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: center; border: 0; margin: 0; padding: 0; }
        .concert-checks label { display: inline-flex; align-items: center; gap: 7px; margin: 0; }
        .concert-checks input { width: auto; min-height: auto; }
        .concert-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 20px; }
        .concert-actions input { min-height: 42px; padding: 9px 14px; }
        .concert-danger { margin-left: auto; }
        .concert-message { padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .concert-message.error { background: #fee2e2; border: 1px solid #dc2626; color: #991b1b; }
        .concert-message.success { background: #dcfce7; border: 1px solid #16a34a; color: #166534; }
        .concert-help { color: #666; font-size: .88rem; }
        #navcontainer { width: auto; }
        #navcontainer a { width: auto; max-width: 220px; }
        @media (max-width: 992px) {
            #main { margin-left: 0 !important; }
            #openNav { position: sticky; top: 0; z-index: 2; background: #fff; }
            #nav { top: 0; padding-top: 60px; }
        }
        @media (max-width: 600px) {
            .concert-admin { padding: 8px; }
            .concert-card { padding: 12px; border-radius: 6px; }
            .concert-grid { grid-template-columns: 1fr; gap: 12px; }
            .concert-field.full { grid-column: auto; }
            .concert-actions { display: grid; grid-template-columns: 1fr 1fr; }
            .concert-actions input { width: 100%; }
            .concert-danger { margin-left: 0; }
            #navcontainer { margin-left: 12px; }
        }
    </style>
    <script>
        function toggleNav() {
            const button = document.getElementById("openNav");
            const nav = document.getElementById("nav");
            const open = button.getAttribute("aria-expanded") !== "true";
            nav.style.display = open ? "block" : "none";
            button.setAttribute("aria-expanded", String(open));
        }
    </script>
</head>
<body>
    <button id="openNav" class="w3-button w3-xlarge w3-hide-large" type="button" aria-controls="nav" aria-expanded="<?= $toon_navigatie ? 'true' : 'false' ?>" onclick="toggleNav()">&#9776; Concerten</button>
    <aside id="nav" class="w3-sidebar w3-bar-block w3-collapse w3-card w3-animate-left"<?= $toon_navigatie ? ' style="display:block"' : '' ?>>
        <form id="vinden" method="post" action="<?= e($_SERVER['PHP_SELF']) ?>">
            <input type="hidden" name="csrf" value="<?= e($_SESSION['concert_admin_csrf']) ?>">
            <div class="w3-panel">
                <label for="zoeknaam">Zoekterm:</label>
                <input name="zoeknaam" type="text" id="zoeknaam" value="<?= e($_SESSION['zoeknaam']) ?>" maxlength="100">
                <button name="action" type="submit" value="zoeken" class="w3-button w3-light-grey">Zoek</button>
                <button name="action" type="submit" value="wissenzoek" class="w3-button w3-light-grey">Wis</button>
            </div>
            <?php if ($concerten): ?>
                <p><?= count($concerten) ?> resultaten. Klik een item aan:</p>
                <div id="navcontainer">
                    <ul id="navlist">
                        <?php foreach ($concerten as $item): ?>
                            <?php
                            $datum = date('d-m-Y', strtotime((string) $item['datum']));
                            $href = $_SERVER['PHP_SELF'] . '?concertId=' . (int) $item['concertId'];
                            ?>
                            <li><a href="<?= e($href) ?>"><?= e($item['concerttitel']) ?><br><span class="klein">(<?= e($datum) ?>)</span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </form>
    </aside>

    <main id="main" class="w3-main w3-container w3-padding">
        <div class="concert-admin">
            <h1>Concertonderhoud</h1>
            <?php if ($errors): ?>
                <div class="concert-message error" role="alert"><?= e(implode(' ', $errors)) ?></div>
            <?php endif; ?>
            <?php if ($notice): ?>
                <div class="concert-message success" role="status"><?= e($notice) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= e($_SERVER['PHP_SELF']) ?>" id="concert" class="concert-card">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['concert_admin_csrf']) ?>">
                <div class="concert-grid">
                    <div class="concert-field">
                        <label for="concertId">concertId</label>
                        <input type="number" name="concertId" id="concertId" value="<?= e($concert['concertId']) ?>" min="1" inputmode="numeric">
                    </div>
                    <div class="concert-field">
                        <label for="concerttitel">Concerttitel</label>
                        <input type="text" name="concerttitel" id="concerttitel" value="<?= e($concert['concerttitel']) ?>" maxlength="255" required>
                    </div>
                    <div class="concert-field">
                        <label for="datum">Datum <span class="concert-help">(jjjj-mm-dd)</span></label>
                        <input type="date" name="datum" id="datum" value="<?= e($concert['datum']) ?>" required>
                    </div>
                    <div class="concert-field">
                        <label for="tijd">Tijd <span class="concert-help">(uu:mm)</span></label>
                        <input type="time" name="tijd" id="tijd" value="<?= e($concert['tijd']) ?>">
                    </div>
                    <div class="concert-field">
                        <label for="plaats">Plaats</label>
                        <input type="text" name="plaats" id="plaats" value="<?= e($concert['plaats']) ?>" maxlength="255">
                    </div>
                    <div class="concert-field">
                        <label for="aantal_plaatsen">Aantal plaatsen</label>
                        <input type="number" name="aantal_plaatsen" id="aantal_plaatsen" value="<?= e($concert['aantal_plaatsen']) ?>" min="0" inputmode="numeric">
                    </div>
                    <div class="concert-field">
                        <label for="prijs_vol">Prijs vol</label>
                        <input type="text" name="prijs_vol" id="prijs_vol" value="<?= e($concert['prijs_vol']) ?>" inputmode="decimal" pattern="[0-9]+([.,][0-9]{1,2})?" placeholder="Bijv. 12.50">
                    </div>
                    <div class="concert-field">
                        <label for="prijs_red">Prijs red.</label>
                        <input type="text" name="prijs_red" id="prijs_red" value="<?= e($concert['prijs_red']) ?>" inputmode="decimal" pattern="[0-9]+([.,][0-9]{1,2})?" placeholder="Bijv. 10.00">
                        <label for="txt_red" class="concert-help">Korting voor</label>
                        <input type="text" name="txt_red" id="txt_red" value="<?= e($concert['txt_red']) ?>" maxlength="255">
                    </div>
                    <div class="concert-field">
                        <label for="prijs_kind">Prijs kind</label>
                        <input type="text" name="prijs_kind" id="prijs_kind" value="<?= e($concert['prijs_kind']) ?>" inputmode="decimal" pattern="[0-9]+([.,][0-9]{1,2})?" placeholder="Bijv. 5.00">
                        <label for="txt_kind" class="concert-help">Korting voor</label>
                        <input type="text" name="txt_kind" id="txt_kind" value="<?= e($concert['txt_kind']) ?>" maxlength="255">
                    </div>
                    <div class="concert-field">
                        <fieldset class="concert-checks">
                            <legend>Status</legend>
                            <label><input type="checkbox" name="online" value="1" <?= (int) $concert['online'] === 1 ? 'checked' : '' ?>> Online</label>
                            <label><input type="checkbox" name="uitverkocht" value="1" <?= (int) $concert['uitverkocht'] === 1 ? 'checked' : '' ?>> Uitverkocht</label>
                        </fieldset>
                    </div>
                    <div class="concert-field full">
                        <label for="details">Publieke details</label>
                        <textarea name="details" id="details"><?= e($concert['details']) ?></textarea>
                    </div>
                    <div class="concert-field full">
                        <label for="opmerking_intern">Interne opmerking</label>
                        <textarea name="opmerking_intern" id="opmerking_intern"><?= e($concert['opmerking_intern']) ?></textarea>
                    </div>
                </div>
                <div class="concert-actions">
                    <input type="submit" name="action" value="Toevoegen">
                    <input type="submit" name="action" value="Wijzigen">
                    <input type="submit" name="action" value="Wissen" class="concert-danger" onclick="return confirm('Weet u zeker dat u dit concert wilt wissen?');">
                    <input type="submit" name="action" value="leegmaken" formnovalidate>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
