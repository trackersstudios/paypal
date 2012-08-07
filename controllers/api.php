<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:			Social Igniter : Paypal : API Controller
* Author: 		firepony
* 		  		tjgillies@gmail.com
* 
* Project:		http://social-igniter.com
* 
* Description: This file is for the Paypal API Controller class
*/
class Api extends Oauth_Controller
{
    function __construct()
    {
        parent::__construct();

		/* 
		$config['Sandbox'] = TRUE;
		$config['APIVersion'] = '85.0';
		$config['APIUsername'] = $config['Sandbox'] ? 'tjgill_1342254503_biz_api1.gmail.com' : 'PRODUCTION_USERNAME_GOES_HERE';
		$config['APIPassword'] = $config['Sandbox'] ? '1342254527' : 'PRODUCTION_PASSWORD_GOES_HERE';
		$config['APISignature'] = $config['Sandbox'] ? 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-A2.oNj-q1ACiwa2FlftkxYynZWAS' : 'PRODUCTION_SIGNATURE_GOES_HERE';
		$config['DeviceID'] = $config['Sandbox'] ? '' : 'PRODUCTION_DEVICE_ID_GOES_HERE';
		$config['ApplicationID'] = $config['Sandbox'] ? 'APP-80W284485P519543T' : 'PRODUCTION_APP_ID_GOES_HERE';
		$config['DeveloperEmailAccount'] = $config['Sandbox'] ? 'tjgillies@gmail.com' : 'PRODUCTION_DEV_EMAIL_GOES_HERE';
		*/
		
		$config = array( 
			'Sandbox'			=> TRUE,
			'APIVersion'		=> '85.0',
			'ApplicationID'		=> 'APP-80W284485P519543T',
			'APIUsername'		=> config_item('paypal_username'),
			'APIPassword'		=> config_item('paypal_password'),
			'APISignature'		=> config_item('paypal_signature')
		);

		$this->load->library('paypal_adaptive', $config);
	}

    /* Install App */
	function install_get()
	{
		// Load
		$this->load->library('installer');
		$this->load->config('install');        

		// Settings & Create Folders
		$settings = $this->installer->install_settings('paypal', config_item('paypal_settings'));
	
		if ($settings == TRUE)
		{
            $message = array('status' => 'success', 'message' => 'Yay, the Paypal App was installed');
        }
        else
        {
            $message = array('status' => 'error', 'message' => 'Dang Paypal App could not be installed');
        }		
		
		$this->response($message, 200);
	}

}