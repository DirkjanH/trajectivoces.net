<?php
define('__ROOT__', dirname(dirname(__FILE__)));

// echo __ROOT__;

require_once(__ROOT__ . '/provider.php');
require_once(__ROOT__ . '/instellingen.php');
require_once(__ROOT__ . '/teksten.php');
require_once(__ROOT__ . '/mollie.php');

$protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
$hostname = $_SERVER['HTTP_HOST'];
$path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

d($path);

try {
    $db = new PDO("mysql:host=localhost;dbname=$database;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $sMsg = '<p> 
            Regelnummer: ' . $e->getLine() . '<br /> 
            Bestand: ' . $e->getFile() . '<br /> 
            Foutmelding: ' . $e->getMessage() . '</p>';

    trigger_error($sMsg);
}

// zet de localiteit op Nederland
setlocale(LC_ALL, 'nl_NL');
date_default_timezone_set('Europe/Amsterdam');

function select_query($query, $index = 2)
{
    global $db;
    try {
        if (isset($query) and $query != '')
            foreach ($db->query($query, PDO::FETCH_ASSOC) as $row) {
                if (is_string($index) and $index != '') $result[$row[$index]] = $row;
                else $result[] = $row;
            }
        else echo 'Lege query<br>';
        //echo'$index = '.$index.'<br>';
        //d($result);
        if (empty($result) or !is_array($result)) $result = false;
        elseif ($index == 1 and count($result) == 1) {
            $result = $result[0];    // één rij
        } elseif ($index == 0 and count($result, COUNT_RECURSIVE) == 2) {
            $result = current($result[0]);  // één waarde
            //echo 'één waarde';
        }

        if (isset($result)) return $result;
        else return false;
    } catch (PDOException $e) {
        echo "Fout: {$e}<br>";
    }
}

function exec_query($query)
{
    global $db;
    // print_all($query);
    try {
        $db->exec($query);
        return (true);
    } catch (PDOException $e) {
        echo "Fout: {$e}<br>";
    }
}

function quote($value, $parameter_type = PDO::PARAM_STR)
{
    global $db;
    //d($value);
    if (is_null($value)) return 'NULL';
    elseif (is_bool($value)) return $value ? 'TRUE' : 'FALSE';
    elseif (is_int($value) || is_float($value)) return $value;
    return $db->quote($value);
}

function lastID()
{
    global $db;
    return $db->lastInsertId();
}

function bedrag($bedrag)
{
    return number_format($bedrag, 0, ',', '.') . ',&#8211;';
}

function euro($bedrag)
{
    return '&euro;&nbsp;' . number_format($bedrag, 0, ',', '.');
}

function euro2($bedrag)
{
    $bedr = '&#8364;&nbsp;' . number_format($bedrag, 2, ',', '.');
    return str_replace(',00', ',&#8212;', $bedr);
}

function euro_en($bedrag)
{
    return 'EUR&nbsp;' . number_format($bedrag, 0, ',', '.');
}

function euro_en2($bedrag)
{
    $bedr = 'EUR&nbsp;' . number_format($bedrag, 2, ',', '.');
    return str_replace(',00', ',&#8212;', $bedr);
}

function czk($bedrag)
{
    return 'CZK&nbsp;' . number_format($bedrag, 0, ',', '.');
}

function write_error($error_message)
{
    global $error, $fout;
    $error = true;
    $fout    .= $error_message;
}

// build the form action
$editFormAction = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "");