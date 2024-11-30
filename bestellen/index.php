<?php
// stel php in dat deze fouten weergeeft
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php');

Kint::$enabled_mode = false;

require_once('modules/module_kaartverkoop.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Reserveer kaarten</title>
    <link href="<?php echo $css; ?>" rel="stylesheet">
    <link rel="icon" type="image/png" href=<?php echo $favicon; ?>>
</head>
<body>
    <div class="w3-content w3-card w3-white w3-padding">
        <form name="bestelformulier" id="bestelformulier" method="POST"
            action="<?php echo $editFormAction; ?>" class="w3-container">
            <h5><?php echo $txt['aanhef']; ?></h5>
            <div class="w3-row-padding">
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['voornaam']; ?><span
                            class="commentaar">*</span></label>
                    <input class="w3-input w3-border" name="voornaam"
                        type="text" required
                        value="<?php echo $_POST['voornaam']; ?>">
                </div>
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['tussenv']; ?></label>
                    <input class="w3-input w3-border" name="tussenvoegsel"
                        type="text" id="tussenvoegsel" size=30
                        value="<?php echo $_POST['tussenvoegsel']; ?>">
                </div>
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['achternaam']; ?><span
                            class="commentaar">*</span></label>
                    <input class="w3-input w3-border" name="achternaam"
                        type="text" id="achternaam" size=30
                        value="<?php echo $_POST['achternaam']; ?>" required>
                </div>
            </div>
            <div class="w3-row-padding">
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['telefoon']; ?><span
                            class="commentaar">*</span></label>
                    <input class="w3-input w3-border" name="telefoon" type="tel"
                        id="telefoon" size=30
                        value="<?php echo $_POST['telefoon']; ?>" required>
                </div>
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['email']; ?><span
                            class="commentaar">*</span></label>
                    <input class="w3-input w3-border" type="email" name="email"
                        id="email" size="30"
                        value="<?php echo $_POST['email']; ?>" required>
                </div>
                <div class="w3-third">
                    <label
                        class="w3-label w3-validate"><?php echo $txt['plaats']; ?><span
                            class="commentaar">*</span></label>
                    <input class="w3-input w3-border" name="plaats" type="text"
                        id="plaats" size=30 value="<?php
																											echo $_POST['plaats']; ?>" required>
                </div>
            </div>
            <div class="w3-container"> <?php
				$allesuitverkocht = false;
				//		foreach ($concert as $u) if ($u['uitverkocht']) $allesuitverkocht += 1;
				if (is_array($concert) and count($concert) > 0) {
					echo '<h5 class="w3-padding-0 w3-margin-0 w3-margin-top">' . $txt['wens'];
					if (count($concert) > 1) echo ' <span class="w3-tag w3-red w3-small">' . $txt['kiezen'] . '</span>';
					echo '</h5>';
					foreach ($concert as $c) {
						echo '<p class="w3-margin-0">'; ?> <input type="radio" class="w3-radio"
                    name="concertId" value="<?php echo $c['concertId']; ?>"
                    <?php if (count($concert) == 1) echo 'checked';
							if (isset($_POST['concertId']) and ($_POST['concertId'] == $c['concertId'])) echo 'checked';
							if (isset($c['uitverkocht']) and ($c['uitverkocht'] == 1)) echo ' disabled'; ?>> <?php echo stripslashes($c['concert']) . '; ';
						if (isset($c['uitverkocht']) and ($c['uitverkocht'] == 1))
							echo '<span class="w3-tag w3-red w3-small">' . $txt['uitverkocht'] . '</span>';
						else echo $c['entree']; ?><br> <?php if (isset($c['details'])) echo '<p class="details">' . stripslashes($c['details']) . '</p>';
					}
					?> </div>
            <div class="w3-container w3-margin-top">
                <div class="w3-row-padding">
                    <div class="w3-third">
                        <label
                            class="w3-label w3-validate"><?php echo $txt['aantal_k']; ?><?php if ($c['prijs_vol'] != '0.00') echo $txt['volle_prijs']; ?>:</label>
                        <input name="aantal_vol" type="text" required
                            class="w3-input w3-border aantal" id="aantal_vol"
                            value="<?php
																																if (isset($aantal_vol)) echo $aantal_vol; ?>">
                    </div>
                    <div class="w3-third"
                        <?php if (!(isset($c['prijs_red']) and $c['prijs_red'] > 0)) echo 'style="display: none;"'; ?>>
                        <div
                            <?php if (!(isset($c['prijs_red']) and $c['prijs_red'] > 0)) echo 'class="w3-grayscale w3-grey"'; ?>> <?php if (isset($c['txt_red']) and $c['txt_red'] != '') echo '<label class="w3-label">aantal ' . $c['txt_red'] . ':</label>';
							else echo '<label class="w3-label">' . $txt['CJP'] . '</label>'; ?>
                            <input class="w3-input w3-border aantal"
                                name="aantal_red" type="text" id="aantal_red"
                                value="<?php if (isset($aantal_red)) echo $aantal_red; ?>"
                                <?php if (!(isset($c['prijs_red']) and $c['prijs_red'] > 0)) echo 'disabled'; ?>>
                        </div>
                    </div>
                    <div class="w3-third"
                        <?php if (!(isset($c['txt_kind']) and $c['txt_kind'] != '')) echo 'style="display: none;"'; ?>>
                        <div
                            <?php if (!(isset($c['prijs_kind']) and $c['prijs_kind'] > 0)) echo 'class="w3-grayscale w3-grey"'; ?>>
                            <?php if (isset($c['txt_kind']) and $c['txt_kind'] != '') echo '<label class="w3-label>">kaarten ' . $c['txt_kind'] . ':</label>';
							else echo '<label class="w3-label w3-validate">' . $txt['12_jaar'] . '</label>'; ?> <input class="w3-input w3-border aantal <?php if (!(isset($c['prijs_kind']) and $c['prijs_kind'] > 0)) echo ' w3-grey'; ?>"
                                name="aantal_kind" type="text" id="aantal_kind"
                                value="<?php if (isset($aantal_kind)) echo $aantal_kind; ?>"
                                <?php if (!(isset($c['prijs_kind']) and $c['prijs_kind'] > 0)) echo 'disabled'; ?>>
                        </div>
                    </div> <?php
				}
				if (count($concert) == 0) echo '<h5 class="w3-btn-block w3-white w3-text-red">' . $txt['geen_concert'] . '</h5>';
				else echo '<h5 class="w3-tag w3-red w3-small">' . $txt['niet_prijs'] . '</h5>';
				?>
                </div>
            </div>
            <div
                class="<?php if ($allesuitverkocht) echo 'onzichtbaar'; ?> w3-panel panelkleur">
                <?php echo $txt['werkwijze']; ?></div>
            <div class="w3-panel panelkleur">
                <h5>
                    <input class="w3-checkbox" type="checkbox" name="flyers"
                        <?php if (isset($_POST['flyers'])) echo 'checked'; ?>>
                    <label><?php echo $txt['aankondiging']; ?></label>
                </h5>
            </div>
            <div class="w3-panel panelkleur">
                <h5><?php echo $txt['vraag_hoe']; ?></h5>
                <div class="w3-row">
                    <div class="w3-third w3-panel w3-leftbar w3-border-green">
                        <p>
                            <input type="radio" name="publiciteit"
                                value="viavia"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "viavia")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['kennis']; ?></label>
                            <br>
                            <input type="radio" name="publiciteit"
                                value="deelnemer"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "deelnemer")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['deelnemer']; ?></label>
                            <input class="blokje w3-input w3-border"
                                name="aanbrenger" type="text" id="aanbrenger"
                                size="20"
                                value="<?php
																																	if (isset($_POST['aanbrenger'])) echo $_POST['aanbrenger']; ?>">
                        </p>
                    </div>
                    <div class="w3-third w3-panel w3-leftbar w3-border-green">
                        <p>
                            <input type="radio" name="publiciteit" value="krant"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "krant")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['krant']; ?></label>
                            <br>
                            <input type="radio" name="publiciteit" value="flyer"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "flyer")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['flyer']; ?></label>
                            <br>
                            <input type="radio" name="publiciteit"
                                value="affiche"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "affiche")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['affiche']; ?></label>
                        </p>
                    </div>
                    <div class="w3-third w3-panel w3-leftbar w3-border-green">
                        <p>
                            <input type="radio" name="publiciteit"
                                value="internet"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "internet")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['internet']; ?></label>
                            <br>
                            <input type="radio" name="publiciteit" value="radio"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "radio")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['radio']; ?></label>
                            <br>
                            <input type="radio" name="publiciteit"
                                value="anders"
                                <?php if (isset($_POST['publiciteit']) and ($_POST['publiciteit'] == "anders")) echo 'checked'; ?>>
                            <label
                                class="w3-label w3-validate"><?php echo $txt['anders']; ?><input
                                    class="blokje w3-input w3-border"
                                    name="anders" type="text"
                                    value="<?php
																																										if (isset($_POST['anders'])) echo stripslashes($_POST['anders']); ?>"
                                    size="20"></label>
                    </div>
                    </p>
                    <div class="w3-panel">
                        <label class="w3-label w3-validate">Eventuele
                            opmerkingen: <br><input
                                class="blokje w3-input w3-border"
                                name="opmerkingen" type="textarea"
                                value="<?php if (isset($_POST['opmerkingen'])) echo stripslashes($_POST['opmerkingen']); ?>"
                                rows="2" cols="100"></label>
                    </div>
                </div>
                <div class="w3-container">
                    <p>Klik nu op 'verzenden'. Je wordt na het klikken
                        doorgeleid naar de betaalpagina van Mollie, waarop je
                        via iDeal kunt betalen. Daarna zie je een bedankpagina
                        en ontvang je een mail met een QR-code, die je bij het
                        concert aan de kassa kunt tonen.</p>
                    <input class="w3-btn w3-green"
                        <?php if (isset($allesuitverkocht) and $allesuitverkocht === true) echo 'DISABLED '; ?>name="submit"
                        type="submit" value="<?php echo $txt['verzenden']; ?>">
                    <p>P.S. het kaartverkoopsysteem is vernieuwd. Mocht je een
                        probleem tegenkomen, stuur dan SVP even een mailtje aan
                        <a
                            href="mailto:organisatie@trajectivoces.net">organisatie@trajectivoces.net</a>
                        of een appje aan 0619 224 758.</p>
                </div>
        </FORM>
    </div>
</body>
</html>