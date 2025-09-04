<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Zorgen dat de array met errors leeg is.
    $_SESSION['errors'] = array();
    $_SESSION['input'] = array();
    
    // Velden in het formulier definiëren
    $formuliervelden = array(
        'voornaam' => 'text',
        'achternaam' => 'text',
        'stemsoort' => 'radio',
        'zangervaring' => 'textarea',
        'telefoonnummer' => 'text',
		'mobiel' => 'text',
        'email' => 'text',
        'geboortejaar' => 'select',
        'informatieverzoek' => 'radio'
    );
    
    // Velden waar geen controle op uitgevoerd hoeft te worden.
    $no_check = array('telefoonnummer','mobiel','email');
    
    // Loop alle elementen uit de $_POST array langs
    foreach($formuliervelden as $veld => $type)
    {
        if($type == 'checkbox')
        {
            if(empty($_POST[$veld]) && !in_array($veld, $no_check))
            {
                $_SESSION['errors'][$veld] = true;
                $errors[] = 'Geef aan of U informatie wilt ontvangen';
            }
            else
            {
                $_SESSION['input'][$veld] = $_POST[$veld];
            }
        }    
        elseif($type == 'radio')
        {
            if(empty($_POST[$veld]) && !in_array($veld, $no_check))
            {
                $_SESSION['errors'][$veld] = true;
                $errors[] = 'Kies een optie bij "'.$veld.'"';
            }
            else
            {
                $_SESSION['input'][$veld] = $_POST[$veld];
            }
        }    
        else
        {
            if(isset($_POST[$veld]))
            {
                // Spaties voor en achter input verwijderen
                $value = trim($_POST[$veld]);
                
                // Ingevulde waarden in een sessievariabele zetten.
                $_SESSION['input'][$veld] = $value;
                
                // Controle van verschillende velden.
                // Velden waar geen controle op uitgevoerd hoeft te worden overslaan.
                if(in_array($veld, $no_check))
                {
                    continue;
                }
                // Controle op geldige gebruikersnaam (langer dan 3 tekens).
                elseif($veld == 'zangervaring')
                {
                    if(strlen($value) <= 3)
                    {
                        $_SESSION['errors'][$veld] = true;
                        $errors[] = 'Je hebt geen zangervaring ingevuld.';
                    }
                }
                // Controle op geldige postcode (4 cijfers + 2 letters).
                elseif($veld == 'mobiel')
                {
                    if(!preg_match('/^[0-9]{10}$/i', $value))
                    {
                        $_SESSION['errors'][$veld] = true;
                        $errors[] = 'Je hebt geen geldig telefoonnummer ingevuld.';
                    }
                }
                // Controle of rest van de velden ingevuld is.
                else
                {
                    if(empty($value))
                    {
                        $_SESSION['errors'][$veld] = true;
                        $errors[] = 'Je bent vergeten het veld '.$veld.' in te vullen.';            
                    }
                }
            }
            else
            {
                $errors[] = 'Het veld '.$veld.' ontbreekt aan het formulier';
            }
        }        
    }
    
    // Verwerk het formulier als er geen fouten opgetreden zijn.
    if(empty($_SESSION['errors']))
    {
        // Verwerk het formulier:
        // vb. Schrijf gegevens naar een database
		
    $con = mysql_connect("localhost","trajecnet","Muziek"); //Replace with your actual MySQL DB Username and Password
if (!$con)
{
die('Could not connect: ' . mysql_error());
}
mysql_select_db("trajecnet_wervingzangerstrajectriste", $con); //Replace with your MySQL DB Name
$voornaam=mysql_real_escape_string($_POST['voornaam']); //This value has to be the same as in the HTML form file
$achternaam=mysql_real_escape_string($_POST['achternaam']); //This value has to be the same as in the HTML form file
$stemsoort=mysql_real_escape_string($_POST['stemsoort']); //This value has to be the same as in the HTML form file
$zangervaring=mysql_real_escape_string($_POST['zangervaring']); //This value has to be the same as in the HTML form file
$telefoonnummer=mysql_real_escape_string($_POST['telefoonnummer']); //This value has to be the same as in the HTML form file
$mobiel=mysql_real_escape_string($_POST['mobiel']); //This value has to be the same as in the HTML form file
$email=mysql_real_escape_string($_POST['email']); //This value has to be the same as in the HTML form file
$geboortejaar=mysql_real_escape_string($_POST['geboortejaar']); //This value has to be the same as in the HTML form file
$informatieverzoek=mysql_real_escape_string($_POST['informatieverzoek']); //This value has to be the same as in the HTML form file
$sql="INSERT INTO wervingzangerstristeespana (voornaam,achternaam,stemsoort,zangervaring,telefoonnummer,mobiel,email,geboortejaar,informatieverzoek) VALUES ('$voornaam','$achternaam','$stemsoort','$zangervaring','$telefoonnummer','$mobiel','$email','$geboortejaar','$informatieverzoek')"; /*form_data is the name of the MySQL table where the form data will be saved.
name and email are the respective table fields*/
if (!mysql_query($sql,$con)) {
 die('Error: ' . mysql_error());
}
echo "Verzending formulier gelukt";
mysql_close($con);

        // vb. Stuur een email met de gegevens
			
  mail("organisatie@trajectivoces.nl, secretariaat@trajectivoces.nl", "wervingzangers", "(voornaam:'$voornaam',achternaam:'$achternaam',stemsoort:'$stemsoort',zangervaring:'$zangervaring',telefoonnummer:'$telefoonnummer',mobiel:'$mobiel',email:'$email',geboortejaar:'$geboortejaar',informatieverzoek:'$informatieverzoek')");

        // Stuur gebruiker door naar een volgende pagina.
		$content[] = '<p><b>Je aanmelding is verwerkt. Dank voor de getoonde interesse. We nemen spoedig contact op.</b></p>';
        $content[] = '<p><b>Je verwerkte gegevens</b></p>';
        $content[] = '<ul>';
        foreach($_SESSION['input'] as $key => $value)
        {
            $content[] = '<li>'.$key.': '.$value.'</li>';
        }
        $content[] = '</ul>';
        
        unset($_SESSION['input']);
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>formulier belangstellende zangers</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="opmaak/Stijlblad formulier werving zangers.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
</head>

<body>
  <div id="kop">
    
    <table>
      <tr> 
        <td id="aanhefvacatureformulier"> In het huidige project zijn   geen vacatures. Middels onderstaand formulier kun je belangstelling voor een toekomstig project aangeven.</td>
      </tr>
      <tr><td height="2px"></td></tr>
      <tr> 
        <td id="tekstkader">Vul  het formulier hieronder in  als je interesse hebt een  project mee te zingen. Wij nemen dan contact met je op als er plaats is. De repetities vinden in de regel plaats op dinsdagavond van 20:00 tot 22:30 in de Utrechtse Pieterskerk. Data van de komende projecten vind je <a href="../../Rubriekpaginas/DataKomendeProjecten.html" target="_parent">hier</a>.</td>
        </tr>
      </table>
    
  </div>
  
  <div id="Bovenbalkformulier">Indien je interesse hebt om mee te zingen, vul dan hieronder je gegevens in en klik op de knop verzenden.</div>
  
  
  <form action="#" method="post" name="example" id="example">
    <div id="formulierbody">
      <table width="650" cellpadding=6 border="0" align="center">
        <tr><td>
          <label 
                for="voor" 
                class="field <?php if(!empty($_SESSION['errors']['voornaam'])) echo 'error'; ?>"
            >Voornaam:*</label></td><td>
            <input 
                name="voornaam" type="text" class="Invulveld" 
                id="voornaam" 
                value="<?php echo isset($_SESSION['input']['voornaam']) ? $_SESSION['input']['voornaam'] : ''; ?>"
            /></td> 
        </tr>
        <tr> 
          <td>
          <label 
                for="achter" 
                class="field <?php if(!empty($_SESSION['errors']['achternaam'])) echo 'error'; ?>"
            >Achternaam:*</label></td><td>
            <input 
                name="achternaam" type="text" class="Invulveld" 
                id="achter" 
                value="<?php echo isset($_SESSION['input']['achternaam']) ? $_SESSION['input']['achternaam'] : ''; ?>"
            /></td>
        </tr>
        <tr> 
          <td><label 
                class="field <?php if(!empty($_SESSION['errors']['stemsoort'])) echo 'error'; ?>"
            >Stemsoort:*</label></td>
          <td>
            <tr> 
              <td width="30%">
                <input 
                type="radio" 
                name="stemsoort" 
                id="sopraan" 
                value="sopraan"
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['stemsoort'] == 'sopraan') echo 'checked="checked"'; ?> 
            /><label for="sopraan" class="stemselectie">Sopraan&nbsp;</label><br />
                <input 
                type="radio" 
                name="stemsoort" 
                id="mezzosopraan" 
                value="mezzosopraan"
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['stemsoort'] == 'mezzosopraan') echo 'checked="checked"'; ?>
            /><label for="mezzosopraan" class="stemselectie">Mezzosopraan</label><br />
                <input 
                type="radio" 
                name="stemsoort" 
                id="alt" 
                value="alt"
                <?php if(isset($_SESSION['input']['stemsoort']) && $_SESSION['input']['stemsoort'] == 'alt') echo 'checked="checked"'; ?>
            /><label for="alt" class="stemselectie">Alt</label><br /></td><td>
                
                <input 
                type="radio" 
                name="stemsoort" 
                id="tenor" 
                value="tenor"
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['stemsoort'] == 'tenor') echo 'checked="checked"'; ?>
               
            /><label for="tenor" class="stemselectie">Tenor</label><br />
                <input 
                type="radio" 
                name="stemsoort" 
                id="bariton" 
                value="bariton"
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['stemsoort'] == 'bariton') echo 'checked="checked"'; ?>
            /><label for="bariton" class="stemselectie">Bariton</label><br />
                <input 
                type="radio" 
                name="stemsoort" 
                id="bas" 
                value="bas"
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['stemsoort'] == 'bas') echo 'checked="checked"'; ?>
            /><label for="bas" class="stemselectie">Bas</label>
                
                </td>
              </tr>
        <tr> 
          <td> <label 
                for="zangervaring" 
                class="field <?php if(!empty($_SESSION['errors']['zangervaring'])) echo 'error'; ?>"
            >Zangervaring:*</label></td>
          <td>
          <textarea name="zangervaring" cols="60" rows="3" class="Invulveld" id="zangervaring"><?php echo isset($_SESSION['input']['zangervaring']) ? $_SESSION['input']['zangervaring'] : ''; ?></textarea></td>
        </tr>
        <tr> 
          
          <td>
          <label 
                for="telefoonnummer" 
                class="field <?php if(!empty($_SESSION['errors']['telefoonnummer'])) echo 'error'; ?>"
            >telefoonnummer:</label></td><td>
            <input 
                name="telefoonnummer" type="text" class="Invulveld" 
                id="telefoonnummer" 
                value="<?php echo isset($_SESSION['input']['telefoonnummer']) ? $_SESSION['input']['telefoonnummer'] : ''; ?>"
            />
            </td>
        </tr>
        <tr> 
          <td>
          <label 
                for="mobiel" 
                class="field <?php if(!empty($_SESSION['errors']['mobiel'])) echo 'error'; ?>"
            >mobiel:</label></td><td>
            <input 
                name="mobiel" type="text" class="Invulveld" 
                id="mobiel" 
                value="<?php echo isset($_SESSION['input']['mobiel']) ? $_SESSION['input']['mobiel'] : ''; ?>"
            />
            </td>
        </tr>
        <tr> 
          <td>            <label class="field" for="email" >email:</label></td><td>
            <input 
                name="email" type="text" class="Invulveld" 
                id="email" 
                value="<?php echo isset($_SESSION['input']['email']) ? $_SESSION['input']['email'] : ''; ?>"
            />
            </td>
        </tr>
        <tr> 
          <td> 
          <label 
                for="geboortejaar" 
                class="field <?php if(!empty($_SESSION['errors']['geboortejaar'])) echo 'error'; ?>"
            >Geboortejaar:*</label></td><td>
            <select name="geboortejaar" class="Invulveld" id="geboortejaar">
              <?php
                for($i = date('Y'); $i >= 1900; $i--)
                {
                    if(isset($_SESSION['input']['geboortejaar']) && $_SESSION['input']['geboortejaar'] == $i)
                    {
                        echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
                    }
                    else
                    {
                        echo '<option value="'.$i.'">'.$i.'</option>';
                    }
                }
                ?>
            </select>
            </td>
        </tr>
        <tr> 
          <td><label 
                class="field <?php if(!empty($_SESSION['errors']['informatieverzoek'])) echo 'error'; ?>"
            >Informatie:*</label></td>
          <td>
                
                <input
                type="radio" 
                name="informatieverzoek" 
                value="wil informatie" 
                id="wil geen informatie" 
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['informatieverzoek'] == 'wil informatie') echo 'checked="checked"'; ?> 
            />
                <label for="wil informatie" class="informatieverzoek">Ik wil graag concert-aankondigingen per e-mail ontvangen</label>
                <br />
                
                
                <input
                type="radio" 
                name="informatieverzoek" 
                value="wil geen informatie" 
                id="wil geen informatie" 
                <?php if(isset($_SESSION['input']['kleur']) && $_SESSION['input']['informatieverzoek'] == 'wil geen informatie') echo 'checked="checked"'; ?> 
            />
                <label for="wil geen informatie" class="informatieverzoek">Ik hoef geen informatie te ontvangen</label> </td>
              
         </td>
        </tr>
      </table>
    </div>
    <div id="kaderverzendknop">
      
    <input type="submit" value="Verzenden" class="Verzendknop" /></div>
    
  </form>        
  
  <?php
        // Weergeven van meldingen uit het phpscript.
        if(isset($errors))
        {
            echo '<ul>';
            foreach($errors as $error)
            {
                echo '<li>'.$error.'</li>'; 
            }
            echo '</ul>';
        }
        elseif(isset($content))
        {
            foreach($content as $line)
            {
                echo $line;
            }
        }
        ?>
</body>
</html>