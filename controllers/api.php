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

        $this->load->config('paypal');

        if (config_item('paypal_sandbox') == 'TRUE') $sandbox = TRUE;
        else $sandbox = FALSE;

		$config = array( 
			'Sandbox'				=> $sandbox,
			'APIVersion'			=> '85.0',
			'APIUsername'			=> config_item('paypal_username'),		// PRODUCTION_USERNAME_GOES_HERE
			'APIPassword'			=> config_item('paypal_password'),		// PRODUCTION_PASSWORD_GOES_HERE
			'APISignature'			=> config_item('paypal_signature'),		// PRODUCTION_SIGNATURE_GOES_HERE
			'ApplicationID'			=> config_item('paypal_application_id'),// PRODUCTION_APP_ID_GOES_HERE
			'DeveloperEmailAccount'	=> config_item('paypal_account_email')	// PRODUCTION_DEV_EMAIL_GOES_HERE
		);

		$this->load->library('paypal_adaptive', $config);               
		$this->load->library('paypal_pro', $config);
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
	
	function direct_payment_authd_post()
	{
		$user = $this->social_auth->get_user('user_id', $this->oauth_user_id);
	
		$DPFields = array(
			'paymentaction'		=> 'Sale',                      // How you want to obtain payment. Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
			'ipaddress'			=> $_SERVER['REMOTE_ADDR'],     // Required. IP address of the payer's browser.
			'returnfmfdetails'	=> '1'                     		// Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
		);
		
		$CCDetails = array(
			'creditcardtype'	=> $this->input->post('creditcardtype'),	// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
			'acct' 				=> $this->input->post('acct'),      		// Required. Credit card number.  No spaces or punctuation.  
			'expdate' 			=> $this->input->post('expdate'),			// Required. Credit card expiration date.  Format is MMYYYY
			'cvv2' 				=> $this->input->post('cvv2'),      		// Requirements determined by your PayPal account settings.  Security digits for credit card.
			'startdate' 		=> $this->input->post('startdate'),         // Month and year that Maestro or Solo card was issued.  MMYYYY
			'issuenumber' 		=> $this->input->post('issuenumber')        // Issue number of Maestro or Solo card.  Two numeric digits max.
		);

		$PayerInfo = array(
			'email'				=> $user->email,           		// Email address of payer.
			'payerid'			=> '',                          // Unique PayPal customer ID for payer.
			'payerstatus' 		=> '',                         	// Status of payer.  Values are verified or unverified
			'business' 			=> ''    			            // Payer's business name.
		);

		$PayerName = array(
			'salutation'		=> '', 		                    // Payer's salutation. 20 char max.
			'firstname'			=> $user->name,                 // Payer's first name. 25 char max.
			'middlename'		=> '',                         	// Payer's middle name. 25 char max.
			'lastname'			=> '',                 			// Payer's last name. 25 char max.
			'suffix' 			=> ''                           // Payer's suffix. 12 char max.
		);
		
		$BillingAddress = array(
			'street' 			=> $this->input->post('street'),            // Required. First street address.
			'street2' 			=> $this->input->post('street2'),           // Second street address.
			'city' 				=> $this->input->post('city'),              // Required. Name of City.
			'state' 			=> $this->input->post('state'),             // Required. Name of State or Province.
			'countrycode' 		=> $this->input->post('countrycode'),       // Required. Country code.
			'zip' 				=> $this->input->post('zip'),               // Required. Postal code of payer.
			'phonenum' 			=> $user->phone_number               		// Phone Number of payer. 20 char max.
		);
		
		$ShippingAddress = array(
			'shiptoname' 		=> $this->input->post('shiptoname'),        // Required if shipping is included. Person's name associated with this address. 32 char max.
			'shiptostreet' 		=> $this->input->post('shiptostreet'),      // Required if shipping is included. First street address.  100 char max.
			'shiptostreet2' 	=> $this->input->post('shiptostreet2'),     // Second street address. 100 char max.
			'shiptocity' 		=> $this->input->post('shiptocity'),        // Required if shipping is included. Name of city. 40 char max.
			'shiptostate' 		=> $this->input->post('shiptostate'),       // Required if shipping is included. Name of state or province. 40 char max.
			'shiptozip' 		=> $this->input->post('shiptozip'),         // Required if shipping is included. Postal code of shipping address. 20 char max.
			'shiptocountry' 	=> $this->input->post('shiptocountry'),     // Required if shipping is included. Country code of shipping address. 2 char max.
			'shiptophonenum' 	=> $this->input->post('shiptophonenum')     // Phone number for shipping address. 20 char max.
		);
		
		$PaymentDetails = array(
			'amt' 				=> $this->input->post('amt'),                    // Required. Total amount of order, including shipping, handling, and tax.  
			'currencycode' 		=> 'USD',                     	// Required. Three-letter currency code.  Default is USD.
			'itemamt' 			=> $this->input->post('itemamt'),                    // Required if you include itemized cart details. (L_AMTn, etc.) Subtotal of items not including S&H, or tax.
			'shippingamt' 		=> $this->input->post('shippingamt'),                     // Total shipping costs for the order. If you specify shippingamt, you must also specify itemamt.
			'shipdiscamt' 		=> '',                     		// Shipping discount for the order, specified as a negative number.  
			'handlingamt' 		=> '',                    		// Total handling costs for the order. If you specify handlingamt, you must also specify itemamt.
			'taxamt' 			=> '',                         	// Required if you specify itemized cart tax details. Sum of tax for all items on the order. Total sales tax. 
			'desc' 				=> $this->input->post('source'),                	// Description of the order the customer is purchasing. 127 char max.
			'custom' 			=> '',                         	// Free-form field for your own use. 256 char max.
			'invnum' 			=> $this->input->post('order_id'),               // Your own invoice or tracking number
			'notifyurl' 		=> ''                        	// URL for receiving Instant Payment Notifications.  This overrides what your profile is set to use.
		);    
		
		$OrderItems = array();

		$Item = array(
			'l_name' 			=> $this->input->post(''),           // Item Name.  127 char max.
			'l_desc' 			=> $this->input->post(''), // Item description.  127 char max.
			'l_amt' 			=> $this->input->post(''),                     // Cost of individual item.
			'l_number' 			=> $this->input->post(''),                       // Item Number.  127 char max.
			'l_qty' 			=> '1',                         // Item quantity.  Must be any positive integer.  
			'l_taxamt' 			=> '',                         	// Item's sales tax amount.
		);

		array_push($OrderItems, $Item);

		$Secure3D = array(
			'authstatus3d'		=> '', 
			'mpivendor3ds'		=> '', 
			'cavv' 				=> '', 
			'eci3ds'			=> '', 
			'xid'				=> ''
		);
		
		$PayPalRequestData = array(
			'DPFields'			=> $DPFields, 
			'CCDetails'			=> $CCDetails, 
			'PayerInfo'			=> $PayerInfo, 
			'PayerName'			=> $PayerName, 
			'BillingAddress'	=> $BillingAddress, 
			'ShippingAddress'	=> $ShippingAddress, 
			'PaymentDetails'	=> $PaymentDetails, 
			'OrderItems'		=> $OrderItems, 
			'Secure3D'			=> $Secure3D
		);

	    $result = $this->paypal_pro->DoDirectPayment($PayPalRequestData);

	    if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
            $message = array('status' => 'error', 'message' => 'We could not accept your payment', 'data' => $result['ERRORS']);
        }
        else
        {
            $message = array('status' => 'success', 'message' => 'Your payment was received', 'data' => $result);        
        
        }		
		
		$this->response($message, 200);
	}

}