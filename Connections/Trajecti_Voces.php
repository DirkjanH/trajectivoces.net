<?php 
	# PHP ADODB document - made with PHAkt
	# FileName="Connection_php_adodb.htm"
	# Type="ADODB"
	# HTTP="true"
	# DBTYPE="mysql"
	
	$MM_Trajecti_Voces_HOSTNAME = 'localhost';
	$MM_Trajecti_Voces_DATABASE = 'mysql:pellegri-6';
	$MM_Trajecti_Voces_DBTYPE   = preg_replace('/:.*$/', '', $MM_Trajecti_Voces_DATABASE);
	$MM_Trajecti_Voces_DATABASE = preg_replace('/^[^:]*?:/', '', $MM_Trajecti_Voces_DATABASE);
	$MM_Trajecti_Voces_USERNAME = 'pellegri';
	$MM_Trajecti_Voces_PASSWORD = 'fbPdiJDk';
	$MM_Trajecti_Voces_LOCALE = 'En';
	$MM_Trajecti_Voces_MSGLOCALE = 'En';
	$MM_Trajecti_Voces_CTYPE = 'P';
	$KT_locale = $MM_Trajecti_Voces_MSGLOCALE;
	$KT_dlocale = $MM_Trajecti_Voces_LOCALE;
	$KT_serverFormat = '%Y-%m-%d %H:%M:%S';
	$QUB_Caching = 'false';

	$KT_localFormat = $KT_serverFormat;
	
	if (!defined('CONN_DIR')) define('CONN_DIR',dirname(__FILE__));
	require_once(CONN_DIR.'/../adodb/adodb.inc.php');
	$Trajecti_Voces=&KTNewConnection($MM_Trajecti_Voces_DBTYPE);

	if($MM_Trajecti_Voces_DBTYPE == 'access' || $MM_Trajecti_Voces_DBTYPE == 'odbc'){
		if($MM_Trajecti_Voces_CTYPE == 'P'){
			$Trajecti_Voces->PConnect($MM_Trajecti_Voces_DATABASE, $MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD);
		} else $Trajecti_Voces->Connect($MM_Trajecti_Voces_DATABASE, $MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD);
	} else if (($MM_Trajecti_Voces_DBTYPE == 'ibase') or ($MM_Trajecti_Voces_DBTYPE == 'firebird')) {
		if($MM_Trajecti_Voces_CTYPE == 'P'){
			$Trajecti_Voces->PConnect($MM_Trajecti_Voces_HOSTNAME.':'.$MM_Trajecti_Voces_DATABASE,$MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD);
		} else $Trajecti_Voces->Connect($MM_Trajecti_Voces_HOSTNAME.':'.$MM_Trajecti_Voces_DATABASE,$MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD);
	}else {
		if($MM_Trajecti_Voces_CTYPE == 'P'){
			$Trajecti_Voces->PConnect($MM_Trajecti_Voces_HOSTNAME,$MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD, $MM_Trajecti_Voces_DATABASE);
		} else $Trajecti_Voces->Connect($MM_Trajecti_Voces_HOSTNAME,$MM_Trajecti_Voces_USERNAME,$MM_Trajecti_Voces_PASSWORD, $MM_Trajecti_Voces_DATABASE);
   }

	if (!function_exists('updateMagicQuotes')) {
		function updateMagicQuotes($HTTP_VARS){
			if (is_array($HTTP_VARS)) {
				foreach ($HTTP_VARS as $name=>$value) {
					if (!is_array($value)) {
						$HTTP_VARS[$name] = addslashes($value);
					} else {
						foreach ($value as $name1=>$value1) {
							if (!is_array($value1)) {
								$HTTP_VARS[$name1][$value1] = addslashes($value1);
							}
						}
					}
				}
			}
			return $HTTP_VARS;
		}
		
		if (!get_magic_quotes_gpc()) {
			$_GET = updateMagicQuotes($_GET);
			$_POST = updateMagicQuotes($_POST);
			$_COOKIE = updateMagicQuotes($_COOKIE);
		}
	}
	if (!isset($_SERVER['REQUEST_URI']) && isset($_ENV['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_ENV['REQUEST_URI'];
	}
	if (!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'].(isset($_SERVER['QUERY_STRING'])?"?".$_SERVER['QUERY_STRING']:"");
	}
?>