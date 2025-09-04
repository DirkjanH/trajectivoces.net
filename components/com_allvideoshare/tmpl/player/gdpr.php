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
use Joomla\CMS\Uri\Uri;
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

        #privacy-wrapper {            
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

        #privacy-consent-block {
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

        #privacy-consent-button {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 15px;
            background: #F00;
            border-radius: 3px;
            cursor: pointer;
        }

        #privacy-consent-button:hover {
            opacity: 0.8;
        }

        @media only screen and (max-width: 250px) {
            #privacy-consent-block {
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
    <div id="privacy-wrapper" style="background-image: url(<?php echo $this->item->thumb; ?>);">
		<div id="privacy-consent-block" >
			<div id="privacy-consent-message"><?php echo Text::_( 'COM_ALLVIDEOSHARE_PLAYER_GDPR_CONSENT_MESSAGE' ); ?></div>
			<div id="privacy-consent-button"><?php echo Text::_( 'COM_ALLVIDEOSHARE_PLAYER_GDPR_CONSENT_BUTTON_LABEL' ) ; ?></div>
		</div>
	</div>
				
	<script type="text/javascript">
		/**
		* Set cookie for accepting the privacy consent.
		*
		* @since  4.1.0
		*/
		function ajaxSubmit() {	
            document.getElementById( 'privacy-consent-button' ).innerHTML = '...';

			var xmlhttp;

			if ( window.XMLHttpRequest ) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			};
			
			xmlhttp.onreadystatechange = function() {				
				if ( 4 == xmlhttp.readyState && 200 == xmlhttp.status ) {					
					if ( xmlhttp.responseText ) {
                        var url = window.location.href;    

                        if ( url.indexOf( '?' ) > -1 ) {
                            url += '&r=' + Math.random();
                        } else {
                            url += '?r=' + Math.random();
                        }

                        window.location.href = url;
					}						
				}					
			};	

			xmlhttp.open( 'GET', '<?php echo URI::root(); ?>index.php?option=com_allvideoshare&task=video.cookie', true );
			xmlhttp.send();							
		}
		
		document.getElementById( 'privacy-consent-button' ).addEventListener( 'click', ajaxSubmit );
	</script>
</body>
</html>
