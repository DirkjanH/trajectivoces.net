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

use Joomla\CMS\Language\Text;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

	<style type="text/css">
        html, 
        body {			
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important; 
			padding: 0 !important; 
			font-family: Verdana, Geneva, sans-serif;
			font-size: 14px;
            line-height: 1.5;
            overflow: hidden;
        }

        #no-permission-wrapper {            
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #222;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #FFF;
            text-align: center;
        }

        #no-permission-consent-block {
            position: relative;
            margin: 0 30px;
            padding: 15px;
            top: 50%;
            background: #000;
            border-radius: 3px;
            opacity: 0.9;
            transform: translateY( -50% );
            -ms-transform: translateY(- 50% );
            -webkit-transform: translateY( -50% );
        }

        @media only screen and (max-width: 250px) {
            #no-permission-consent-block {
                margin: 0;
                font-size: 12px;               
            }
        }
    </style>

    <?php
    if ( ! empty( $this->params->get( 'custom_css' ) ) ) {
        printf( '<style type="text/css">%s</style>', $this->params->get( 'custom_css' ) );
    }
    ?>
</head>
<body>    
    <div id="no-permission-wrapper" style="background-image: url(<?php echo $this->item->thumb; ?>);">
		<div id="no-permission-consent-block" >
			<div id="no-permission-consent-message"><?php echo Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_VIEW' ); ?></div>
		</div>
	</div>	
</body>
</html>
