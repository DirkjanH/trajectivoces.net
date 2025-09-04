<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die; 

use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoSharePlayer;

AllVideoSharePlayer::updateViews( $this->item->id );

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <style type="text/css">
        html, 
        body, 
        iframe {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important; 
            padding: 0 !important; 
            overflow: hidden;
        }
    </style>

    <?php
    if ( ! empty( $this->params->get( 'custom_css' ) ) ) {
        printf( '<style type="text/css">%s</style>', $this->params->get( 'custom_css' ) );
    }
    ?>
</head>
<body>    
    <?php echo $this->item->thirdparty; ?>
</body>
</html>