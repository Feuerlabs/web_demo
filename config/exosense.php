<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Config elements to talk to exosense server
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/
$config['exosense_url']	= 'https://vps.ulf.wiger.net:8088/ck3/rpc';
$config['exosense_user'] = 'exosense_user';
$config['exosense_password'] = 'exosense_password';
$config['exosense_yang_file'] = 'demo.yang';
$config['exosense_notification_url'] = 'http://localhost:9123';
$config['exosense_timeout'] = 30;
