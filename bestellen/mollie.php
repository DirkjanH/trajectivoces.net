<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

$mollie = new \Mollie\Api\MollieApiClient();
//$mollie->setApiKey("test_S9snrpq8PEj43UsSRvmJURuSMFqKMx"); // Trajecti Voces via Tessa
$mollie->setApiKey("live_2U7QFQTH4Fe5E7SemxSKHB2rJzzppn"); // Trajecti Voces via Tessa
?>