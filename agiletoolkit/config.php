<?php


$config['atk']['base_path']='./atk4/';
$config['dsn']='mysql://trajecnet:Muziek@localhost/trajecnet_trajectivoces';

function page_helloworld($page){
        $page->add('Button')->setLabel('Click Me');
    }
	
$config['url_postfix']='';
$config['url_prefix']='?page=';

# Agile Toolkit attempts to use as many default values for config file,
# and you only need to add them here if you wish to re-define default
# values. For more options look at:
#
#  http://www.atk4.com/doc/config

