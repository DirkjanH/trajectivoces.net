<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="robots" content="noindex,nofollow" />
<title>Nagalm gastenboek Trajecti Voces</title>
<link href="nagalm_iframe.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="Inhoudpagina">
<?
//------------------------//
// DATABASE CONFIGURATIE  //
//------------------------//
$mysql_user = "trajecnet_beheer"; // je mysql login naam
$mysql_pass = "Sweelinck2012"; // je mysql wachtwoord
$mysql_host = "localhost"; // de host van je mysql database (localhost is meestal wel oke)
$mysql_dbn = "trajecnet_nagalm"; // de naam van je database

/*
Hier maakt hij een database connectie
Hij selecteert de database $mysql_dbn
Deze heb je hierboven aangegeven.
Vervolgens gebruikt hij als tweede argument,
Dit is dus na de komma, de verbinding met de
database.

Hiervoor worden $mysql_host, $mysql_user en $mysql_pass
gebruikt. Ook deze heb je hierboven bij de database
configuratie aangegeven.

De @ voor de functie mysql_select_db() en mysql_connect(),
staat voor het verbergen van eventuele foutmeldingen
die hij geeft. Dit is gedaan zodat je een eigen foutmelding
kan creeren wanneer de database connectie mislukt is.

De regel hieronder betekent dus:
Als er GEEN verbinding kan worden gemaakt, doe dan:
*/
if (!@mysql_select_db($mysql_dbn, @mysql_connect($mysql_host, $mysql_user, $mysql_pass)))
{
    //--- De verbinding is nu dus mislukt, geef hier een melding van
    echo "database connectie mislukt!";
    
    //--- Zorg ervoor dat het script stopt.
    exit();
}

/*
De verbinding is nu dus wel gelukt. Nu kijken we
of het formulier verstuurd is d.m.v. de POST methode.
Als dit het geval is, dan is het formulier ingevuld en opgestuurd.
POST = via de server verstuurd.
GET = via het URL adres verstuurd (bijv.: index.php?naam=Bas)

Kijk ook of de velden naam, email en bericht zijn ingevuld.
Dit doe je door te kijken of ze niet leeg zijn dus: !empty($_POST['veld_naam'])

Kijk voor de zekerheid ook of er een apenstaartje (@) zit in het
e-mail adres. Dit is niet super veilig maar voldoet voor nu. Dit
gebeurt d.m.v. de functie strstr() (zie: www.php.net/strstr)
*/

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['naam']) && !empty($_POST['woonplaats']) && !empty($_POST['bericht']))
{
    //--- De datum staat als DATETIME gedefineerd in de database
    //--- Dit is dan het juiste 'formaat', bijv: 2006-11-27 12:03:53
    $datum = date('Y-m-d H:i:s');
	
    //controleer of het onzichtbare veld nog leeg is
	if (!empty($_POST['hiddenfield'])){
		
    //--- Voeg het bericht toe aan de database
    $sql = "INSERT INTO gastenboek_2011 SET ";
    $sql .= "id = ''";
    $sql .= ", naam = '" . $_POST['naam'] . "'";
    $sql .= ", woonplaats = '" . $_POST['woonplaats'] . "'";
    $sql .= ", bericht = '" . $_POST['bericht'] . "'";
    $sql .= ", datum = '" . $datum . "'";
    
    //--- Voor de SQL code uit
    $res = mysql_query($sql);
    
    //--- Als het goed is gegaan, is $res niet leeg
    if (!empty($res))
    {
        echo "<h2>Bericht toegevoegd</h2>";
    
        echo "<p>Bedankt voor uw reactie.<br>
 Het bericht is toegevoegd aan de lijst. Voor een overzicht van alle berichten: <a href=\"" . $_SERVER['PHP_SELF'] . "\" title=\"Berichten overzicht\">het berichten overzicht</a>";
    }
    //--- Het bericht is niet toegevoegd, problemen met de database!
    //--- Je ziet dat hier geen akkolades worden gebruikt (dus: { en }). Omdat er
    //--- Slechts 1 regel onder de 'else' moet worden uitgevoerd is dit niet nodig.
    else
        echo "Bericht NIET toegevoegd. Er is iets misgegaan met het invoeren in de database.";
}
 else{
        echo "ongeldig bericht";
//-------------------------------
// Voeg een nieuw bericht toe
//-------------------------------

/*
Als het GET is, wordt het dus meegegeven in het URL adres
Hier staat dus:

Als show=add in het URL adres staat, volg dan dit stukje.
Bijvoorbeeld: index.php?show=add
*/
}}
elseif ($_GET['show'] == "add" || $_POST['show'] == "add")
{
    echo "<h2>Voeg een bericht toe</h2>";
    
    // Als het formulier verstuurd is, dan ben je hier eerder geweest. De velden zijn dan niet juist ingevuld.
    if ($_SERVER['REQUEST_METHOD'] == "POST")
        echo "<p id=\"berichtnietgeplaatst\">Bericht nog niet geplaatst. Vul naam en woonplaats beide in en plaats opnieuw.</p>";
    
    /*
    Laat het formulier zien
    Hier zie je dus ook: method=POST. Dit betekent dus dat het server-side verstuurd wordt en niet via de URL.
    */
    echo "<form method=\"POST\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
    echo "<p>";
    echo "<input type=\"hidden\" name=\"hiddenfield\" value=\"add\">";
    echo "Naam:";
    /*
    Als het eerder verstuurd is, zet dan de value goed. De functie htmlentities() zorgt
    ervoor dat hij speciale tekens die de opmaak van de pagina zouden kunnen beinvloeden,
    of ervoor kunnen zorgen dat iemand kwade bedoelingen heeft omgezet worden in zogehete
    htmlentities. Dus: < wordt &lt; é wordt &eacute; enzovoort. Zie: www.php.net/htmlentities
    */
    echo "<input type=\"text\" class=\"invulveld\" name=\"naam\" value=\"" . htmlentities($_POST['naam']) . "\"><br />";
    echo "Woonplaats:";
    echo "<input type=\"text\" class=\"invulveld\" name=\"woonplaats\" value=\"" . htmlentities($_POST['woonplaats']) . "\"><br />";
	print("<div class=\"kaderrondpostbericht\">");
    echo "<h5>Bericht:</h5>";
    echo "<textarea class=\"invulveld\" id=\"bericht\" name=\"bericht\">" . htmlentities($_POST['bericht']) . "</textarea>";
	print("</div>");
	print("<div class=\"buitenkaderverzendknop\">");
    echo "<input type=\"submit\" class=\"verzendknop\" name=\"submit\" value=\" Bericht plaatsen \"></p>";
	print("</div>");
    echo "</form>";
}
//-------------------------------
// Voeg een nieuw bericht toe
//-------------------------------
else
{
	
    print("<div class=\"kaderrondkop\">");
    echo "<p>Wij hopen natuurlijk dat de echo van onze concerten nog lang nagalmt in de hoofden van ons publiek. Als dat zo is, voeg dan hier: <a href=\"" . $_SERVER['PHP_SELF'] . "?show=add\">&nbsp;&nbsp;uw gewaardeerde reactie</a> toe aan deze pagina.</p>";
    print("</div>");
	print("<br/>");
	echo "<h2>Ingezonden berichten:</h2>";
	print("<div class=\"kaderrondlijst\">");
    /*
    Hier worden de berichten geselecteerd uit de database
    DATE_FORMAT(datum, ...) is nodig omdat (zoals eerder geschreven)
    de datum als volgt in de database staat: 2006-11-27 12:02:53. Om
    dit op zijn Nederlands te weergeven, gebruiken we de MySQL functie DATE_FORMAT.
    De %d staat voor de dag, %m voor de maand en %Y voor het jaar dus: 27.11.2007
    */
    $sql = "SELECT id,naam,woonplaats,bericht,DATE_FORMAT(datum, '%d.%m.%Y') as show_datum FROM gastenboek_2011 ORDER BY datum DESC";
    
	
    // Voer SQL code uit
    $res = mysql_query($sql);
	
	
    // Kijk of er 1 of meerdere rijen gevonden zijn
    if (mysql_num_rows($res) >= 1)
    {
        // Toon elke rij tot dat er geen rijen meer zijn
        while ($row = mysql_fetch_array($res))
           {
            $row['woonplaats'] = htmlentities($row['woonplaats']);
            $row['naam'] = nl2br(htmlentities($row['naam']));
            $row['bericht'] = nl2br(htmlentities($row['bericht']));
            print("<div class=\"kaderrondlijstsegment\">");
			print("<div class=\"kaderdatum\">");
            echo "<i>" . $row['show_datum'] . "</i>";
			print("</div>");
			echo "<h3>" . $row['naam'] .  "</h3>uit:&nbsp;<h4>" . $row['woonplaats'] . "</h4><br />";
			print("<div class=\"kaderinhoudbericht\">");
            echo "<p>'" . $row['bericht'] . "'</p>";
			print("</div>");
			print("</div>");
			print("<hr class=\"stippellijn\"></hr>");
        }
    }
    // Er zijn geen rijen gevonden, geef aan dat er nog geen berichten zijn toegevoegd
    else
        echo "<p id=\"noggeenberichten\">Er zijn nog geen berichten geplaatst.</p>";
		print("</div>");
}
print("<div class=\"afsluitinginhoud\">");
			print("</div>");
?>
</div>
</body>
</html>