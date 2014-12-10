<?php

class WC_Gateway_Klarna_Account extends WC_Gateway_Klarna {
	
	/**
     * Class for Klarna Account payment.
     *
     */
     
	public function __construct() {
		global $woocommerce;
		
		parent::__construct();
		
		$this->id								= 'klarna_account';
		$this->method_title 					= __('Klarna Account', 'klarna');
		$this->has_fields 						= true;
		$this->order_button_text 				= apply_filters( 'klarna_order_button_text', __( 'Place order', 'woocommerce' ) );
		
		
		// Klarna warning banner - used for NL only
		$klarna_wb_img_checkout = 'http://www.afm.nl/~/media/Images/wetten-regels/kredietwaarschuwing/balk_afm1-jpg.ashx';
		$klarna_wb_img_single_product = 'http://www.afm.nl/~/media/Images/wetten-regels/kredietwaarschuwing/balk_afm2-jpg.ashx';
		$klarna_wb_img_product_list = 'http://www.afm.nl/~/media/Images/wetten-regels/kredietwaarschuwing/balk_afm2-jpg.ashx';
		
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Load shortcodes. 
		// This is used so that the merchant easily can modify the displayed monthly cost text (on single product and shop page) via the settings page.
		require_once( KLARNA_DIR . 'shortcodes.php');
		
		

		// Define user set variables
		$this->enabled							= ( isset( $this->settings['enabled'] ) ) ? $this->settings['enabled'] : '';
		$this->title 							= ( isset( $this->settings['title'] ) ) ? $this->settings['title'] : '';
		$this->description  					= ( isset( $this->settings['description'] ) ) ? $this->settings['description'] : '';
		
		$this->eid_se							= ( isset( $this->settings['eid_se'] ) ) ? $this->settings['eid_se'] : '';
       	$this->secret_se						= ( isset( $this->settings['secret_se'] ) ) ? $this->settings['secret_se'] : '';
       	$this->eid_no							= ( isset( $this->settings['eid_no'] ) ) ? $this->settings['eid_no'] : '';
       	$this->secret_no						= ( isset( $this->settings['secret_no'] ) ) ? $this->settings['secret_no'] : '';
		$this->eid_fi							= ( isset( $this->settings['eid_fi'] ) ) ? $this->settings['eid_fi'] : '';
       	$this->secret_fi						= ( isset( $this->settings['secret_fi'] ) ) ? $this->settings['secret_fi'] : '';
       	$this->eid_dk							= ( isset( $this->settings['eid_dk'] ) ) ? $this->settings['eid_dk'] : '';
       	$this->secret_dk						= ( isset( $this->settings['secret_dk'] ) ) ? $this->settings['secret_dk'] : '';
       	$this->eid_de							= ( isset( $this->settings['eid_de'] ) ) ? $this->settings['eid_de'] : '';
       	$this->secret_de						= ( isset( $this->settings['secret_de'] ) ) ? $this->settings['secret_de'] : '';
       	$this->eid_nl							= ( isset( $this->settings['eid_nl'] ) ) ? $this->settings['eid_nl'] : '';
       	$this->secret_nl						= ( isset( $this->settings['secret_nl'] ) ) ? $this->settings['secret_nl'] : '';
       	$this->eid_at							= ( isset( $this->settings['eid_at'] ) ) ? $this->settings['eid_at'] : '';
       	$this->secret_at						= ( isset( $this->settings['secret_at'] ) ) ? $this->settings['secret_at'] : '';
       	
       	
		$this->lower_threshold					= ( isset( $this->settings['lower_threshold'] ) ) ? $this->settings['lower_threshold'] : '';
		$this->upper_threshold					= ( isset( $this->settings['upper_threshold'] ) ) ? $this->settings['upper_threshold'] : '';
		$this->show_monthly_cost				= ( isset( $this->settings['show_monthly_cost'] ) ) ? $this->settings['show_monthly_cost'] : '';
		$this->show_monthly_cost_prio			= ( isset( $this->settings['show_monthly_cost_prio'] ) ) ? $this->settings['show_monthly_cost_prio'] : '15';
		$this->testmode							= ( isset( $this->settings['testmode'] ) ) ? $this->settings['testmode'] : '';
		$this->de_consent_terms					= ( isset( $this->settings['de_consent_terms'] ) ) ? $this->settings['de_consent_terms'] : '';
		$this->lower_threshold_monthly_cost		= ( isset( $this->settings['lower_threshold_monthly_cost'] ) ) ? $this->settings['lower_threshold_monthly_cost'] : '';
		$this->upper_threshold_monthly_cost		= ( isset( $this->settings['upper_threshold_monthly_cost'] ) ) ? $this->settings['upper_threshold_monthly_cost'] : '';
		$this->ship_to_billing_address			= ( isset( $this->settings['ship_to_billing_address'] ) ) ? $this->settings['ship_to_billing_address'] : '';
		
		if ($this->lower_threshold_monthly_cost == '') $this->lower_threshold_monthly_cost = 0;
		if ($this->upper_threshold_monthly_cost == '') $this->upper_threshold_monthly_cost = 10000000;
		
		
		
		// authorized countries
		$this->authorized_countries		= array();
		if(!empty($this->eid_se)) {
			$this->authorized_countries[] = 'SE';
		}
		if(!empty($this->eid_no)) {
			$this->authorized_countries[] = 'NO';
		}
		if(!empty($this->eid_fi)) {
			$this->authorized_countries[] = 'FI';
		}
		if(!empty($this->eid_dk)) {
			$this->authorized_countries[] = 'DK';
		}
		if(!empty($this->eid_de)) {
			$this->authorized_countries[] = 'DE';
		}
		if(!empty($this->eid_nl)) {
			$this->authorized_countries[] = 'NL';
		}
		
		$klarna_basic_icon = '';
		$klarna_account_info = '';

		// Apply filters to Country and language
		//$this->klarna_country 					= apply_filters( 'klarna_country', $klarna_country );
		//$this->klarna_language 					= apply_filters( 'klarna_language', $klarna_language );
		//$this->klarna_currency 					= apply_filters( 'klarna_currency', $klarna_currency );
		$this->klarna_account_info 				= apply_filters( 'klarna_account_info', $klarna_account_info );
		$this->icon 							= apply_filters( 'klarna_account_icon', $this->get_account_icon() );
		
		
		$this->icon_basic						= apply_filters( 'klarna_basic_icon', $klarna_basic_icon );
		$this->klarna_wb_img_checkout			= apply_filters( 'klarna_wb_img_checkout', $klarna_wb_img_checkout );
		$this->klarna_wb_img_single_product		= apply_filters( 'klarna_wb_img_single_product', $klarna_wb_img_single_product );
		$this->klarna_wb_img_product_list		= apply_filters( 'klarna_wb_img_product_list', $klarna_wb_img_product_list );
		
		
				
		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action('woocommerce_receipt_klarna_account', array( $this, 'receipt_page'));
		
		//add_action('admin_init', array(&$this, 'update_pclasses_from_klarna'));
		
		add_action('woocommerce_checkout_process', array( $this, 'klarna_account_checkout_field_process'));
		add_action('wp_print_footer_scripts', array( $this, 'footer_scripts'));
		
	}
	
	
	
	/**
	 * Initialise Gateway Settings Form Fields
	 */
	function init_form_fields() {

	   	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'klarna' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Klarna Account', 'klarna' ), 
							'default' => 'no'
						), 
			'title' => array(
							'title' => __( 'Title', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'klarna' ), 
							'default' => __( 'Part payments', 'klarna' )
						),
			'description' => array(
							'title' => __( 'Description', 'klarna' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during checkout. ', 'klarna' ), 
							'default' => ''
						), 
			'eid_se' => array(
							'title' => __( 'Eid - Sweden', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Sweden. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_se' => array(
							'title' => __( 'Shared Secret - Sweden', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Sweden.', 'klarna' ), 
							'default' => ''
						),
			'eid_no' => array(
							'title' => __( 'Eid - Norway', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Norway. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_no' => array(
							'title' => __( 'Shared Secret - Norway', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Norway.', 'klarna' ), 
							'default' => ''
						),
			'eid_fi' => array(
							'title' => __( 'Eid - Finland', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Finland. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_fi' => array(
							'title' => __( 'Shared Secret - Finland', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Finland.', 'klarna' ), 
							'default' => ''
						),
			'eid_dk' => array(
							'title' => __( 'Eid - Denmark', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Denmark. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_dk' => array(
							'title' => __( 'Shared Secret - Denmark', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Denmark.', 'klarna' ), 
							'default' => ''
						),
			'eid_de' => array(
							'title' => __( 'Eid - Germany', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Germany. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_de' => array(
							'title' => __( 'Shared Secret - Germany', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Germany.', 'klarna' ), 
							'default' => ''
						),
			'eid_nl' => array(
							'title' => __( 'Eid - Netherlands', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Eid for Netherlands. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'secret_nl' => array(
							'title' => __( 'Shared Secret - Netherlands', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your Klarna Shared Secret for Netherlands.', 'klarna' ), 
							'default' => ''
						),
			'lower_threshold' => array(
							'title' => __( 'Lower threshold', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Disable Klarna Account if Cart Total is lower than the specified value. Leave blank to disable this feature.', 'klarna' ), 
							'default' => ''
						),
			'upper_threshold' => array(
							'title' => __( 'Upper threshold', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Disable Klarna Account if Cart Total is higher than the specified value. Leave blank to disable this feature.', 'klarna' ), 
							'default' => ''
						),
			'show_monthly_cost' => array(
							'title' => __( 'Display monthly cost - product page', 'klarna' ), 
							'type' => 'checkbox',
							'label' => __( 'Display monthly cost on single products page.', 'klarna' ), 
							'default' => 'yes'
						),
			'show_monthly_cost_prio' => array(
								'title' => __( 'Placement of monthly cost - product page', 'klarna' ), 
								'type' => 'select',
								'options' => array('4'=>__( 'Above Title', 'klarna' ), '7'=>__( 'Between Title and Price', 'klarna'), '15'=>__( 'Between Price and Excerpt', 'klarna'), '25'=>__( 'Between Excerpt and Add to cart-button', 'klarna'), '35'=>__( 'Between Add to cart-button and Product meta', 'klarna'), '45'=>__( 'Between Product meta and Product sharing-buttons', 'klarna'), '55'=>__( 'After Product sharing-buttons', 'klarna' )),
								'description' => __( 'Select where on the products page the Monthly cost information should be displayed.', 'klarna' ), 
								'default' => '15'
							),
			'lower_threshold_monthly_cost' => array(
							'title' => __( 'Lower threshold for monthly cost', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Disable the monthly cost feature if <i>Product price</i> is lower than the specified value. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'upper_threshold_monthly_cost' => array(
							'title' => __( 'Upper threshold for monthly cost', 'klarna' ), 
							'type' => 'text', 
							'description' => __( 'Disable the monthly cost feature if <i>Product price</i> is higher than the specified value. Leave blank to disable.', 'klarna' ), 
							'default' => ''
						),
			'ship_to_billing_address' => array(
							'title' => __( 'Send billing address as shipping address', 'klarna' ), 
							'type' => 'checkbox', 
							'label' => __( 'Send the entered billing address in WooCommerce checkout as shipping address to Klarna.', 'klarna' ), 
							'default' => 'no'
						),
			'de_consent_terms' => array(
							'title' => __( 'Klarna consent terms (DE & AT only)', 'klarna' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Klarna consent terms checkbox in checkout. This only apply to German and Austrian merchants.', 'klarna' ), 
							'default' => 'no'
						),
			'testmode' => array(
							'title' => __( 'Test Mode', 'klarna' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable Klarna Test Mode. This will only work if you have a Klarna test account. For test purchases with a live account, <a href="http://integration.klarna.com/en/testing/test-persons" target="_blank">follow these instructions</a>.', 'klarna' ), 
							'default' => 'no'
						)
		);
	    
	} // End init_form_fields()
	
	
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

    	?>
    	<h3><?php _e('Klarna Account', 'klarna'); ?></h3>
	    	<p><?php printf(__('With Klarna your customers can pay by invoice. Klarna works by adding extra personal information fields and then sending the details to Klarna for verification. Documentation <a href="%s" target="_blank">can be found here</a>.', 'klarna'), 'http://docs.woothemes.com/document/klarna/' ); ?></p>
	    	
		    <?php
		    // Get PClasses so that the we can see what classes are active for the merchant.
			require_once(KLARNA_LIB . 'Klarna.php');
			require_once(KLARNA_LIB . 'pclasses/storage.intf.php');
			
			if(!function_exists('xmlrpc_encode_entitites') && !class_exists('xmlrpcresp')) {
				require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
				require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
			}
			
			if( !empty($this->authorized_countries) && $this->enabled == 'yes' ) {
				echo '<h4>' . __('Active PClasses', 'klarna') . '</h4>';
			    foreach($this->authorized_countries as $key=>$country) {
				    $pclasses = $this->fetch_pclasses( $country );
				    if( $pclasses ) {
				    	echo '<p>' . $country . '</p>';
					    foreach( $pclasses as $pclass ) {
					    	if ( $pclass->getType() == 0 || $pclass->getType() == 1 ) {
						    	echo $pclass->getDescription() . ', ';
						    }
					    }
					    
					    echo '<br/>';
				    }   
			    }
			} 
		    ?>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
		
    	<?php
    } // End admin_options()
	
	
	
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
		
	function is_available() {
		global $woocommerce;
		
		if ($this->enabled=="yes") :
		
			// Required fields check
			if (!$this->get_eid() || !$this->get_secret()) return false;
			
			// PClass check
			$pclasses = $this->fetch_pclasses( $this->get_klarna_country() );
			if( empty( $pclasses ) ) return false;
			
			// Checkout form check
			if (isset($woocommerce->cart->total)) {
			
				// Cart totals check - Lower threshold
				if ( $this->lower_threshold !== '' ) {
					if ( $woocommerce->cart->total < $this->lower_threshold ) return false;
				}
			
				// Cart totals check - Upper threshold
				if ( $this->upper_threshold !== '' ) {
					if ( $woocommerce->cart->total > $this->upper_threshold ) return false;
				}
				
				// Don't allow orders over the amount of €250 for Dutch customers
				if ( ($woocommerce->customer->get_country() == true && $woocommerce->customer->get_country() == 'NL') && $woocommerce->cart->total >= 251 ) return false;
				
			
				// Only activate the payment gateway if the customers country is the same as the filtered shop country ($this->klarna_country)
				if ( $woocommerce->customer->get_country() == true && !in_array($woocommerce->customer->get_country(), $this->authorized_countries) ) return false;
				
				// Currency check
				$currency_for_country = $this->get_currency_for_country($woocommerce->customer->get_country());
				if( !empty($currency_for_country) && $currency_for_country !== $this->selected_currency ) return false;
			
			} // End Checkout form check
			
			return true;
					
		endif;	
	
		return false;
	}
	
	
	
	/**
	 * Payment form on checkout page
	 */
	
	function payment_fields() {
	   	global $woocommerce;
	   		   	
	   	// Get PClasses so that the customer can chose between different payment plans.
	  	require_once(KLARNA_LIB . 'Klarna.php');
		require_once(KLARNA_LIB . 'pclasses/storage.intf.php');
		
		if(!function_exists('xmlrpc_encode_entitites') && !class_exists('xmlrpcresp')) {
			require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
			require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
		}

$klarna = new Klarna();
$config = new KlarnaConfig();

// Default required options
$config['mode'] = Klarna::BETA;
$config['pcStorage'] = 'json';
$config['pcURI'] = './pclasses.json';

// Configuration needed for the checkout service
$config['eid'] = $this->get_eid();
$config['secret'] = $this->get_secret();

$klarna->setConfig($config);

try {
    $response = $klarna->checkoutService(
        $woocommerce->cart->total, // Total price of the checkout including VAT
        'SEK', // Currency used by the checkout
        'sv_SE' // Locale used by the checkout
    );
} catch (KlarnaException $e) {
    // cURL exception
    throw $e;
}

$data = $response->getData();

if ($response->getStatus() >= 400) {
    // server responded with error
    echo '<pre>';
    throw new Exception(print_r($data, true));
    echo '</pre>';
}
echo '<pre>';
print_r($data);
echo '</pre>';		

		// Test mode or Live mode		
		if ( $this->testmode == 'yes' ):
			// Disable SSL if in testmode
			$klarna_ssl = 'false';
			$klarna_mode = Klarna::BETA;
		else :
			// Set SSL if used in webshop
			if (is_ssl()) {
				$klarna_ssl = 'true';
			} else {
				$klarna_ssl = 'false';
			}
			$klarna_mode = Klarna::LIVE;
		endif;
	   	
  		$k = new Klarna();
		
		$k->config(
		    $this->get_eid(), 												// EID
		    $this->get_secret(), 											// Secret
		    $this->get_klarna_country(), 									// Country
		    $this->get_klarna_language($this->get_klarna_country()), 		// Language
		    $this->selected_currency, 										// Currency
		    $klarna_mode, 													// Live or test
		    $pcStorage = 'jsondb', 											// PClass storage
		    $pcURI = 'klarna_pclasses_' . $this->get_klarna_country()		// PClass storage URI path
		);
		
		
		Klarna::$xmlrpcDebug = false;
		Klarna::$debug = false;
		
		// apply_filters to cart total so we can filter this if needed
		$klarna_cart_total = $woocommerce->cart->total;
		$sum = apply_filters( 'klarna_cart_total', $klarna_cart_total ); // Cart total.
		$flag = KlarnaFlags::CHECKOUT_PAGE; //or KlarnaFlags::PRODUCT_PAGE, if you want to do it for one item.
	   	
	   	?>
	   	
	   	<?php if ($this->testmode=='yes') : ?><p><?php _e('TEST MODE ENABLED', 'klarna'); ?></p><?php endif; ?>
		<?php
		
		// Description
		if ($this->description) :
			// apply_filters to the description so we can filter this if needed
			$klarna_description = $this->description;
			echo '<p>' . apply_filters( 'klarna_account_description', $klarna_description ) . '</p>';
		endif; 
		
		// Show klarna_warning_banner if NL
		if ( $this->get_klarna_country() == 'NL' ) {
			echo '<p><img src="' . $this->klarna_wb_img_checkout . '" class="klarna-wb" style="max-width: 100%;"/></p>';	
		}
		
		// Mobile or desktop browser
		if (wp_is_mobile() ) {
			$klarna_layout = 'mobile';
		 } else {
		 	$klarna_layout = 'desktop';
		 }
		
		// Script for displaying the terms link
		?>
		
		<script type="text/javascript">
		
			// Document ready
			jQuery(document).ready(function($) {
				
				var klarna_account_selected_country = $( "#billing_country" ).val();
				
				// If no Billing Country is set in the checkout form, use the default shop country
				if( !klarna_account_selected_country ) {
					var klarna_account_selected_country = '<?php echo $this->shop_country;?>';
				}
				
				if( klarna_account_selected_country == 'SE' ) {
				
					var klarna_account_current_locale = 'sv_SE';
				
				} else if( klarna_account_selected_country == 'NO' ) {

					var klarna_account_current_locale = 'nb_NO';
				
				} else if( klarna_account_selected_country == 'DK' ) {

					var klarna_account_current_locale = 'da_DK';
				
				} else if( klarna_account_selected_country == 'FI' ) {

					var klarna_account_current_locale = 'fi_FI';
					
				} else if( klarna_account_selected_country == 'DE' ) {

					var klarna_account_current_locale = 'de_DE';
				
				}  else if( klarna_account_selected_country == 'NL' ) {

					var klarna_account_current_locale = 'nl_NL';
				
				} else if( klarna_account_selected_country == 'AT' ) {

					var klarna_account_current_locale = 'de_AT';
				} else {
					
				}
				
				new Klarna.Terms.Account({
				    el: 'klarna-account-terms',
				    eid: '<?php echo $this->get_eid(); ?>',
				    locale: klarna_account_current_locale,
				    type: '<?php echo $klarna_layout;?>',
				});
				
			});
			
		</script>
		<span id="klarna-account-terms"></span>
		
		<div class="clear"></div>
			
			
		<fieldset>
			<p class="form-row form-row-first">
			
				<?php
				// Check if we have any PClasses
				// TODO Deactivate this gateway if the file pclasses.json doesn't exist
				$pclasses = $this->fetch_pclasses( $this->get_klarna_country() );
				if($pclasses) {
				
				?>
					<label for="klarna_account_pclass"><?php echo __("Payment plan", 'klarna') ?> <span class="required">*</span></label>
					<select id="klarna_account_pclass" name="klarna_account_pclass" class="woocommerce-select">
						
					<?php
				   	// Loop through the available PClasses stored in the file srv/pclasses.json
					foreach ($pclasses as $pclass) {
						
						if ( $pclass->getType() == 0 || $pclass->getType() == 1 ) {
						
							// Get monthly cost for current pclass
							$monthly_cost = KlarnaCalc::calc_monthly_cost(
    	    									$sum,
    	    									$pclass,
    	    									$flag
    										);
    										
    						// Get total credit purchase cost for current pclass
    						// Only required in Norway
							$total_credit_purchase_cost = KlarnaCalc::total_credit_purchase_cost(
    	    									$sum,
    	    									$pclass,
    	    									$flag
    										);
    						
    						// Check that Cart total is larger than min amount for current PClass				
			   				if($sum > $pclass->getMinAmount()) {
			   				
			   					echo '<option value="' . $pclass->getId() . '">';
			   					if ($this->get_klarna_country() == 'NO') {
									if ( $pclass->getType() == 1 ) {
										//If Account - Do not show startfee. This is always 0.
										echo sprintf(__('%s - %s %s/month - %s%s', 'klarna'), $pclass->getDescription(), $monthly_cost, $this->selected_currency, $pclass->getInterestRate(), '%');
										} else {
											// Norway - Show total cost
											echo sprintf(__('%s - %s %s/month - %s%s - Start %s - Tot %s %s', 'klarna'), $pclass->getDescription(), $monthly_cost, $this->selected_currency, $pclass->getInterestRate(), '%', $pclass->getStartFee(), $total_credit_purchase_cost, $this->klarna_currency );
										}
									} else {
										if ( $pclass->getType() == 1 ) {
											//If Account - Do not show startfee. This is always 0.
											echo sprintf(__('%s - %s %s/month - %s%s', 'klarna'), $pclass->getDescription(), $monthly_cost, $this->selected_currency, $pclass->getInterestRate(), '%');
										} else {
											// Sweden, Denmark, Finland, Germany & Netherlands - Don't show total cost
											echo sprintf(__('%s - %s %s/month - %s%s - Start %s', 'klarna'), $pclass->getDescription(), $monthly_cost, $this->selected_currency, $pclass->getInterestRate(), '%', $pclass->getStartFee() );
										}
									}
								echo '</option>';
							
							} // End if ($sum > $pclass->getMinAmount())
							
			   			} // End if $pclass->getType() == 0 or 1
					
					} // End foreach
					?>
						
					</select>
				
					<?php
				} else {
					echo __('Klarna PClasses seem to be missing. Klarna Account does not work.', 'klarna');
				}
				?>				
				
			</p>
			<?php
			// Calculate lowest monthly cost and display it
			/*
			$pclass = $k->getCheapestPClass($sum, $flag);
	
			//Did we get a PClass? (it is false if we didn't)
			if($pclass) {
	    		//Here we reuse the same values as above:
    			$value = KlarnaCalc::calc_monthly_cost(
    	    	$sum,
    	    	$pclass,
    	    	$flag
    			);
	*/
	    		/* $value is now a rounded monthly cost amount to be displayed to the customer. */
	    		// apply_filters to the monthly cost message so we can filter this if needed
	  /*  		
	    		$klarna_account_monthly_cost_message = sprintf(__('From %s %s/month', 'klarna'), $value, $this->klarna_currency );
	    		echo '<p class="form-row form-row-last klarna-monthly-cost">' . apply_filters( 'klarna_account_monthly_cost_message', $klarna_account_monthly_cost_message ) . '</p>';
	    		
			
			}*/
			?>
			<div class="clear"></div>
			
			<p class="form-row form-row-first">
				<?php if ( $this->get_klarna_country() == 'NL' || $this->get_klarna_country() == 'DE' ) : ?>
				
				<label for="klarna_pno"><?php echo __("Date of Birth", 'klarna') ?> <span class="required">*</span></label>
                    <select class="dob_select dob_day" name="date_of_birth_day" style="width:60px;">
                        <option value="">
                        <?php echo __("Day", 'klarna') ?>
                        </option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>
                    <select class="dob_select dob_month" name="date_of_birth_month" style="width:80px;">
                        <option value="">
                        <?php echo __("Month", 'klarna') ?>
                        </option>
                        <option value="01"><?php echo __("Jan", 'klarna') ?></option>
                        <option value="02"><?php echo __("Feb", 'klarna') ?></option>
                        <option value="03"><?php echo __("Mar", 'klarna') ?></option>
                        <option value="04"><?php echo __("Apr", 'klarna') ?></option>
                        <option value="05"><?php echo __("May", 'klarna') ?></option>
                        <option value="06"><?php echo __("Jun", 'klarna') ?></option>
                        <option value="07"><?php echo __("Jul", 'klarna') ?></option>
                        <option value="08"><?php echo __("Aug", 'klarna') ?></option>
                        <option value="09"><?php echo __("Sep", 'klarna') ?></option>
                        <option value="10"><?php echo __("Oct", 'klarna') ?></option>
                        <option value="11"><?php echo __("Nov", 'klarna') ?></option>
                        <option value="12"><?php echo __("Dec", 'klarna') ?></option>
                    </select>
                    <select class="dob_select dob_year" name="date_of_birth_year" style="width:60px;">
                        <option value="">
                        <?php echo __("Year", 'klarna') ?>
                        </option>
                        <option value="1920">1920</option>
                        <option value="1921">1921</option>
                        <option value="1922">1922</option>
                        <option value="1923">1923</option>
                        <option value="1924">1924</option>
                        <option value="1925">1925</option>
                        <option value="1926">1926</option>
                        <option value="1927">1927</option>
                        <option value="1928">1928</option>
                        <option value="1929">1929</option>
                        <option value="1930">1930</option>
                        <option value="1931">1931</option>
                        <option value="1932">1932</option>
                        <option value="1933">1933</option>
                        <option value="1934">1934</option>
                        <option value="1935">1935</option>
                        <option value="1936">1936</option>
                        <option value="1937">1937</option>
                        <option value="1938">1938</option>
                        <option value="1939">1939</option>
                        <option value="1940">1940</option>
                        <option value="1941">1941</option>
                        <option value="1942">1942</option>
                        <option value="1943">1943</option>
                        <option value="1944">1944</option>
                        <option value="1945">1945</option>
                        <option value="1946">1946</option>
                        <option value="1947">1947</option>
                        <option value="1948">1948</option>
                        <option value="1949">1949</option>
                        <option value="1950">1950</option>
                        <option value="1951">1951</option>
                        <option value="1952">1952</option>
                        <option value="1953">1953</option>
                        <option value="1954">1954</option>
                        <option value="1955">1955</option>
                        <option value="1956">1956</option>
                        <option value="1957">1957</option>
                        <option value="1958">1958</option>
                        <option value="1959">1959</option>
                        <option value="1960">1960</option>
                        <option value="1961">1961</option>
                        <option value="1962">1962</option>
                        <option value="1963">1963</option>
                        <option value="1964">1964</option>
                        <option value="1965">1965</option>
                        <option value="1966">1966</option>
                        <option value="1967">1967</option>
                        <option value="1968">1968</option>
                        <option value="1969">1969</option>
                        <option value="1970">1970</option>
                        <option value="1971">1971</option>
                        <option value="1972">1972</option>
                        <option value="1973">1973</option>
                        <option value="1974">1974</option>
                        <option value="1975">1975</option>
                        <option value="1976">1976</option>
                        <option value="1977">1977</option>
                        <option value="1978">1978</option>
                        <option value="1979">1979</option>
                        <option value="1980">1980</option>
                        <option value="1981">1981</option>
                        <option value="1982">1982</option>
                        <option value="1983">1983</option>
                        <option value="1984">1984</option>
                        <option value="1985">1985</option>
                        <option value="1986">1986</option>
                        <option value="1987">1987</option>
                        <option value="1988">1988</option>
                        <option value="1989">1989</option>
                        <option value="1990">1990</option>
                        <option value="1991">1991</option>
                        <option value="1992">1992</option>
                        <option value="1993">1993</option>
                        <option value="1994">1994</option>
                        <option value="1995">1995</option>
                        <option value="1996">1996</option>
                        <option value="1997">1997</option>
                        <option value="1998">1998</option>
                        <option value="1999">1999</option>
                        <option value="2000">2000</option>
                    </select>
					
				<?php else : ?>
					<label for="klarna_pno"><?php echo __("Date of Birth", 'klarna') ?> <span class="required">*</span></label>
					<input type="text" class="input-text" id="klarna_pno" name="klarna_pno" />
				<?php endif; 
					
				// Button/form for getAddress
				$data = new WC_Klarna_Get_Address;
				echo $data->get_address_button();
				?>
			</p>
			
			<?php if ( $this->get_klarna_country() == 'NL' || $this->get_klarna_country() == 'DE' ) : ?>
				<p class="form-row form-row-last">
					<label for="klarna_account_gender"><?php echo __("Gender", 'klarna') ?> <span class="required">*</span></label>
					<select id="klarna_account_gender" name="klarna_account_gender" class="woocommerce-select" style="width:120px;">
						<option value=""><?php echo __("Select gender", 'klarna') ?></option>
						<option value="f"><?php echo __("Female", 'klarna') ?></option>
						<option value="m"><?php echo __("Male", 'klarna') ?></option>
					</select>
				</p>
			<?php endif; ?>
						
			<div class="clear"></div>
		
			
							
			<?php 
			// Consent terms for German & Austrian shops
			if ( ( $this->get_klarna_country() == 'DE' || $this->get_klarna_country() == 'AT' ) && $this->de_consent_terms == 'yes' ) : ?>
				<p class="form-row">
					<label for="klarna_de_terms"></label>
					<input type="checkbox" class="input-checkbox" value="yes" name="klarna_de_consent_terms" />
					<?php echo sprintf(__('Mit der Übermittlung der für die Abwicklungdes Rechnungskaufes und einer Identitäts-und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. Meine <a href="%s" target="_blank">Einwilligung</a> kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.', 'klarna'), 'https://online.klarna.com/consent_de.yaws') ?>
					
				</p>
			<?php endif; ?>
			<div class="clear"></div>
		
		</fieldset>
		<?php	
	}
	
	
	/**
 	 * Process the gateway specific checkout form fields
 	**/
	function klarna_account_checkout_field_process() {
    	global $woocommerce;
    	
 		
 		// Only run this if Klarna account is the choosen payment method
 		if ($_POST['payment_method'] == 'klarna_account') {
 		
 			// SE, NO, DK & FI
 			if ( $_POST['billing_country'] == 'SE' || $_POST['billing_country'] == 'NO' || $_POST['billing_country'] == 'DK' || $_POST['billing_country'] == 'FI' ){
 			
    			// Check if set, if its not set add an error.
    			if (!$_POST['klarna_pno'])
        		 	wc_add_notice(__('<strong>Date of birth</strong> is a required field', 'klarna'), 'error');
        	 	
			}
			
			// NL & DE
	 		if ( $_POST['billing_country'] == 'NL' || $_POST['billing_country'] == 'DE' ){
	    		// Check if set, if its not set add an error.
	    		
	    		// Gender
	    		if (empty($_POST['klarna_account_gender']))
	        	 	wc_add_notice(__('<strong>Gender</strong> is a required field', 'klarna'), 'error');
	         	
	         	// Date of birth
				if (!$_POST['date_of_birth_day'] || !$_POST['date_of_birth_month'] || !$_POST['date_of_birth_year'])
	         		wc_add_notice(__('<strong>Date of birth</strong> is a required field', 'klarna'), 'error');
	         	
	         	
	         	// Shipping and billing address must be the same
	         	$compare_billing_and_shipping = 0;
	         	
	         	if( isset( $_POST['ship_to_different_address'] ) && $_POST['ship_to_different_address']=1 ) {
	         		$compare_billing_and_shipping = 1;	
			 	}
				
	         	
	         	if ($compare_billing_and_shipping==1 && isset($_POST['shipping_first_name']) && $_POST['shipping_first_name'] !== $_POST['billing_first_name'])
	        	 	wc_add_notice(__('Shipping and billing address must be the same when paying via Klarna.', 'klarna'), 'error');
	        	 
	        	 if ($compare_billing_and_shipping==1 && isset($_POST['shipping_last_name']) && $_POST['shipping_last_name'] !== $_POST['billing_last_name'])
	        	 	wc_add_notice(__('Shipping and billing address must be the same when paying via Klarna.', 'klarna'), 'error');
	        	 
	        	 if ($compare_billing_and_shipping==1 && isset($_POST['shipping_address_1']) && $_POST['shipping_address_1'] !== $_POST['billing_address_1'])
	        	 	wc_add_notice(__('Shipping and billing address must be the same when paying via Klarna.', 'klarna'), 'error');
	        	 
	        	 if ($compare_billing_and_shipping==1 && isset($_POST['shipping_postcode']) && $_POST['shipping_postcode'] !== $_POST['billing_postcode'])
	        	 	wc_add_notice(__('Shipping and billing address must be the same when paying via Klarna.', 'klarna'), 'error');
	        	 	
	        	 if ($compare_billing_and_shipping==1 && isset($_POST['shipping_city']) && $_POST['shipping_city'] !== $_POST['billing_city'])
	        	 	wc_add_notice(__('Shipping and billing address must be the same when paying via Klarna.', 'klarna'), 'error');
			}
			
			// DE & AT
			if ( ( $this->shop_country == 'DE' || $this->shop_country == 'AT' ) && $this->de_consent_terms == 'yes') {
	    		// Check if set, if its not set add an error.
	    		if (!isset($_POST['klarna_de_consent_terms']))
	        	 	wc_add_notice(__('You must accept the Klarna consent terms.', 'klarna'), 'error');
			}
		}
	}
	
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		global $woocommerce;
		
		$order = WC_Klarna_Compatibility::wc_get_order( $order_id );
		
		require_once(KLARNA_LIB . 'Klarna.php');
		require_once(KLARNA_LIB . 'pclasses/storage.intf.php');
		
		if(!function_exists('xmlrpc_encode_entitites') && !class_exists('xmlrpcresp')) {
			require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
			require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
		}
		
		// Get values from klarna form on checkout page
		
		// Collect the dob different depending on country
		if ( $_POST['billing_country'] == 'NL' || $_POST['billing_country'] == 'DE' ) :
			$klarna_pno_day 			= isset($_POST['date_of_birth_day']) ? woocommerce_clean($_POST['date_of_birth_day']) : '';
			$klarna_pno_month 			= isset($_POST['date_of_birth_month']) ? woocommerce_clean($_POST['date_of_birth_month']) : '';
			$klarna_pno_year 			= isset($_POST['date_of_birth_year']) ? woocommerce_clean($_POST['date_of_birth_year']) : '';
			$klarna_pno 				= $klarna_pno_day . $klarna_pno_month . $klarna_pno_year;
		else :
			$klarna_pno 			= isset($_POST['klarna_pno']) ? woocommerce_clean($_POST['klarna_pno']) : '';
		endif;
		
		$klarna_pclass 				= isset($_POST['klarna_account_pclass']) ? woocommerce_clean($_POST['klarna_account_pclass']) : '';
		$klarna_gender 				= isset($_POST['klarna_account_gender']) ? woocommerce_clean($_POST['klarna_account_gender']) : '';
		
		$klarna_de_consent_terms	= isset($_POST['klarna_de_consent_terms']) ? woocommerce_clean($_POST['klarna_de_consent_terms']) : '';
		
		
		// Split address into House number and House extension for NL & DE customers
		if ( $_POST['billing_country'] == 'NL' || $_POST['billing_country'] == 'DE' ) :
		
			require_once(KLARNA_DIR . 'split-address.php');
			
			$klarna_billing_address				= $order->billing_address_1;
			$splitted_address 					= splitAddress($klarna_billing_address);
			
			$klarna_billing_address				= $splitted_address[0];
			$klarna_billing_house_number		= $splitted_address[1];
			$klarna_billing_house_extension		= $splitted_address[2];
			
			$klarna_shipping_address			= $order->shipping_address_1;
			$splitted_address 					= splitAddress($klarna_shipping_address);
			
			$klarna_shipping_address			= $splitted_address[0];
			$klarna_shipping_house_number		= $splitted_address[1];
			$klarna_shipping_house_extension	= $splitted_address[2];
		
		else :
			
			$klarna_billing_address				= $order->billing_address_1;
			$klarna_billing_house_number		= '';
			$klarna_billing_house_extension		= '';
			
			$klarna_shipping_address			= $order->shipping_address_1;
			$klarna_shipping_house_number		= '';
			$klarna_shipping_house_extension	= '';
			
		endif;
				
		// Store Klarna specific form values in order as post meta
		update_post_meta( $order_id, 'klarna_pno', $klarna_pno);
		
		
		// Test mode or Live mode		
		if ( $this->testmode == 'yes' ):
			// Disable SSL if in testmode
			$klarna_ssl = 'false';
			$klarna_mode = Klarna::BETA;
		else :
			// Set SSL if used in webshop
			if (is_ssl()) {
				$klarna_ssl = 'true';
			} else {
				$klarna_ssl = 'false';
			}
			$klarna_mode = Klarna::LIVE;
		endif;
			
		$k = new Klarna();
		
		$k->config(
		    $this->get_eid(), 												// EID
		    $this->get_secret(), 											// Secret
		    $this->get_klarna_country(), 									// Country
		    $this->get_klarna_language($this->get_klarna_country()), 		// Language
		    $this->selected_currency, 										// Currency
		    $klarna_mode, 													// Live or test
		    $pcStorage = 'jsondb', 											// PClass storage
		    $pcURI = 'klarna_pclasses_' . $this->get_klarna_country()		// PClass storage URI path
		);

		
		// Cart Contents
		if (sizeof($order->get_items())>0) : foreach ($order->get_items() as $item) :
			$_product = $order->get_product_from_item( $item );
			if ($_product->exists() && $item['qty']) :
			
				// We manually calculate the tax percentage here
				if ($order->get_line_tax($item) !==0) :
					// Calculate tax percentage
					$item_tax_percentage = @number_format( ( $order->get_line_tax($item) / $order->get_line_total( $item, false ) )*100, 2, '.', '');
				else :
					$item_tax_percentage = 0.00;
				endif;
				
				// apply_filters to item price so we can filter this if needed
				$klarna_item_price_including_tax = $order->get_item_total( $item, true );
				$item_price = apply_filters( 'klarna_item_price_including_tax', $klarna_item_price_including_tax );
					
					// Get SKU or product id
					$reference = '';
					if ( $_product->get_sku() ) {
						$reference = $_product->get_sku();
					} elseif ( $_product->variation_id ) {
						$reference = $_product->variation_id;
					} else {
						$reference = $_product->id;
					}
					
					$k->addArticle(
		    		$qty = $item['qty'], 					//Quantity
		    		$artNo = strval($reference),		 					//Article number
		    		$title = utf8_decode ($item['name']), 	//Article name/title
		    		$price = $item_price, 					// Price including tax
		    		$vat = round( $item_tax_percentage ),			// Tax
		    		$discount = 0, 
		    		$flags = KlarnaFlags::INC_VAT 			//Price is including VAT.
				);
									
			endif;
		endforeach; endif;
		 
		// Discount
		if ($order->order_discount>0) :
			
			// apply_filters to order discount so we can filter this if needed
			$klarna_order_discount = $order->order_discount;
			$order_discount = apply_filters( 'klarna_order_discount', $klarna_order_discount );
		
			$k->addArticle(
			    $qty = 1,
			    $artNo = "",
			    $title = __('Discount', 'klarna'),
			    $price = -$order_discount,
			    $vat = 0,
			    $discount = 0,
			    $flags = KlarnaFlags::INC_VAT //Price is including VAT
			);
		endif;
		
		
		
		// Fees
		if ( sizeof( $order->get_fees() ) > 0 ) {
			foreach ( $order->get_fees() as $item ) {
			
			
			// We manually calculate the tax percentage here
			if ($order->get_total_tax() >0) :
				// Calculate tax percentage
				$item_tax_percentage = number_format( ( $item['line_tax'] / $item['line_total'] )*100, 2, '.', '');
			else :
				$item_tax_percentage = 0.00;
			endif;
			
			
			// apply_filters to item price so we can filter this if needed
			$klarna_item_price_including_tax = $item['line_total'] + $item['line_tax'];
			$item_price = apply_filters( 'klarna_fee_price_including_tax', $klarna_item_price_including_tax );
			
				$item_loop++;
				
				$k->addArticle(
				    $qty = 1,
				    $artNo = "",
				    $title = $item['name'],
				    $price = $item_price,
				    $vat = round( $item_tax_percentage ),
				    $discount = 0,
			    	$flags = KlarnaFlags::INC_VAT
			    );
			    
			}
		}
		
		
		// Shipping
		if ($order->get_total_shipping()>0) :
			
			// We manually calculate the shipping tax percentage here
			$calculated_shipping_tax_percentage = ($order->order_shipping_tax/$order->get_total_shipping())*100; //25.00
			$calculated_shipping_tax_decimal = ($order->order_shipping_tax/$order->get_total_shipping())+1; //0.25
			
			// apply_filters to Shipping so we can filter this if needed
			$klarna_shipping_price_including_tax = $order->get_total_shipping()*$calculated_shipping_tax_decimal;
			$shipping_price = apply_filters( 'klarna_shipping_price_including_tax', $klarna_shipping_price_including_tax );
			
			$k->addArticle(
			    $qty = 1,
			    $artNo = "",
			    $title = __('Shipping cost', 'klarna'),
			    $price = $shipping_price,
			    $vat = round( $calculated_shipping_tax_percentage ),
			    $discount = 0,
			    $flags = KlarnaFlags::INC_VAT + KlarnaFlags::IS_SHIPMENT //Price is including VAT and is shipment fee
			);
		endif;
		
		
		
		//Create the address object and specify the values.
		
		// Billing address
		$addr_billing = new KlarnaAddr(
    		$email = $order->billing_email,
    		$telno = '', //We skip the normal land line phone, only one is needed.
    		$cellno = $order->billing_phone,
    		//$company = $order->billing_company,
    		$fname = utf8_decode ($order->billing_first_name),
    		$lname = utf8_decode ($order->billing_last_name),
    		$careof = utf8_decode ($order->billing_address_2),  //No care of, C/O.
    		$street = utf8_decode ($klarna_billing_address), //For DE and NL specify street number in houseNo.
    		$zip = utf8_decode ($order->billing_postcode),
    		$city = utf8_decode ($order->billing_city),
    		$country = utf8_decode ($order->billing_country),
    		$houseNo = utf8_decode ($klarna_billing_house_number), //For DE and NL we need to specify houseNo.
    		$houseExt = utf8_decode ($klarna_billing_house_extension) //Only required for NL.
		);
		
		
		// Shipping address
		if ( $order->get_shipping_method() == '' || $this->ship_to_billing_address == 'yes') {
			
			// Use billing address if Shipping is disabled in Woocommerce
			$addr_shipping = new KlarnaAddr(
    			$email = $order->billing_email,
    			$telno = '', //We skip the normal land line phone, only one is needed.
    			$cellno = $order->billing_phone,
    			//$company = $order->shipping_company,
    			$fname = utf8_decode ($order->billing_first_name),
    			$lname = utf8_decode ($order->billing_last_name),
    			$careof = utf8_decode ($order->billing_address_2),  //No care of, C/O.
    			$street = utf8_decode ($klarna_billing_address), //For DE and NL specify street number in houseNo.
    			$zip = utf8_decode ($order->billing_postcode),
    			$city = utf8_decode ($order->billing_city),
    			$country = utf8_decode ($order->billing_country),
    			$houseNo = utf8_decode ($klarna_billing_house_number), //For DE and NL we need to specify houseNo.
    			$houseExt = utf8_decode ($klarna_billing_house_extension) //Only required for NL.
			);
		
		} else {
		
			$addr_shipping = new KlarnaAddr(
    			$email = $order->billing_email,
    			$telno = '', //We skip the normal land line phone, only one is needed.
    			$cellno = $order->billing_phone,
    			//$company = $order->shipping_company,
    			$fname = utf8_decode ($order->shipping_first_name),
    			$lname = utf8_decode ($order->shipping_last_name),
    			$careof = utf8_decode ($order->shipping_address_2),  //No care of, C/O.
    			$street = utf8_decode ($klarna_shipping_address), //For DE and NL specify street number in houseNo.
    			$zip = utf8_decode ($order->shipping_postcode),
    			$city = utf8_decode ($order->shipping_city),
    			$country = utf8_decode ($order->shipping_country),
    			$houseNo = utf8_decode ($klarna_shipping_house_number), //For DE and NL we need to specify houseNo.
    			$houseExt = utf8_decode ($klarna_shipping_house_extension) //Only required for NL.
			);
		
		}

		
		//Next we tell the Klarna instance to use the address in the next order.
		$k->setAddress(KlarnaFlags::IS_BILLING, $addr_billing); //Billing / invoice address
		$k->setAddress(KlarnaFlags::IS_SHIPPING, $addr_shipping); //Shipping / delivery address

		//Set store specific information so you can e.g. search and associate invoices with order numbers.
		$k->setEstoreInfo(
		    $orderid1 = ltrim( $order->get_order_number(), '#'),
		    $orderid2 = $order_id,
		    $user = '' //Username, email or identifier for the user?
		);
		
		
		/** Shipment type? **/

		//Normal shipment is defaulted, delays the start of invoice expiration/due-date.
		// $k->setShipmentInfo('delay_adjust', KlarnaFlags::EXPRESS_SHIPMENT);		    
		try {
    		//Transmit all the specified data, from the steps above, to Klarna.
			$result = $k->reserveAmount(
				$klarna_pno, 			//Date of birth.
				$klarna_gender,			//Gender.
				-1, 					// Automatically calculate and reserve the cart total amount
				KlarnaFlags::NO_FLAG, 	//No specific behaviour like RETURN_OCR or TEST_MODE.
				$klarna_pclass 			// Get the pclass object that the customer has choosen.
    		);
    		
    		// Prepare redirect url
    		$redirect_url = $order->get_checkout_order_received_url();
    		
    		// Store the selected pclass in the order
    		update_post_meta( $order_id, '_klarna_order_pclass', $klarna_pclass );
    		
    		// Retreive response
    		$invno = $result[0];
    		switch($result[1]) {
            case KlarnaFlags::ACCEPTED:
                $order->add_order_note( __('Klarna payment completed. Klarna Invoice number: ', 'klarna') . $invno );
                update_post_meta( $order_id, '_klarna_order_reservation', $invno );
                
                // Payment complete
				$order->payment_complete();		
				
				// Remove cart
				$woocommerce->cart->empty_cart();			
				
				// Return thank you redirect
				return array(
						'result' 	=> 'success',
						'redirect'	=> $redirect_url
				);
						
                break;
            case KlarnaFlags::PENDING:
                $order->add_order_note( __('Order is PENDING APPROVAL by Klarna. Please visit Klarna Online for the latest status on this order. Klarna Invoice number: ', 'klarna') . $invno );
                
                // Payment complete
				$order->payment_complete();
				
				// Remove cart
				$woocommerce->cart->empty_cart();
				
				// Return thank you redirect
				return array(
						'result' 	=> 'success',
						'redirect'	=> $redirect_url
				);
				
                break;
            case KlarnaFlags::DENIED:
                //Order is denied, store it in a database.
				$order->add_order_note( __('Klarna payment denied.', 'klarna') );
				wc_add_notice(__('Klarna payment denied.', 'klarna'), 'error');
                return;
                break;
            default:
            	//Unknown response, store it in a database.
				$order->add_order_note( __('Unknown response from Klarna.', 'klarna') );
				wc_add_notice(__('Unknown response from Klarna.', 'klarna'), 'error');
                return;
                break;
        	}
 			
 	   		
			}
		
		catch(Exception $e) {
    		//The purchase was denied or something went wrong, print the message:
			wc_add_notice(sprintf(__('%s (Error code: %s)', 'klarna'), utf8_encode($e->getMessage()), $e->getCode() ), 'error');
			return;
		}

	
	}
	
	/**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order.', 'klarna').'</p>';
		
	}
	
	
	
		/**
		 * Retrieve the PClasses from Klarna and store it in a transient
		 */
		function fetch_pclasses( $country ) {
			
			global $woocommerce;

			// Get PClasses so that the customer can chose between different payment plans.
			require_once(KLARNA_LIB . 'Klarna.php');
			require_once(KLARNA_LIB . 'pclasses/storage.intf.php');
			
			if(!function_exists('xmlrpc_encode_entitites') && !class_exists('xmlrpcresp')) {
				require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
				require_once(KLARNA_LIB . '/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
			}
			
		// Test mode or Live mode		
		if ( $this->testmode == 'yes' ):
			// Disable SSL if in testmode
			$klarna_ssl = 'false';
			$klarna_mode = Klarna::BETA;
		else :
			// Set SSL if used in webshop
			if (is_ssl()) {
				$klarna_ssl = 'true';
			} else {
				$klarna_ssl = 'false';
			}
			$klarna_mode = Klarna::LIVE;
		endif;
   		
			$k = new Klarna();
		
		$k->config(
		    $this->get_eid(), 							// EID
		    $this->get_secret(), 						// Secret
		    $country, 									// Country
		    $this->get_klarna_language($country), 		// Language
		    $this->get_currency_for_country($country), 	// Currency
		    $klarna_mode, 								// Live or test
		    $pcStorage = 'jsondb', 						// PClass storage
		    $pcURI = 'klarna_pclasses_' . $country		// PClass storage URI path
		);
		
		if( $k->getPClasses() ) {
		
			return $k->getPClasses();
		
		} else {
				
			try {
			    $k->fetchPClasses($country); //You can specify country (and language, currency if you wish) if you don't want to use the configured country.
			    /* PClasses successfully fetched, now you can use getPClasses() to load them locally or getPClass to load a specific PClass locally. */
				// Redirect to settings page
				//wp_redirect(get_payment_gateway_configuration_url('WC_Gateway_Klarna_Account&klarna_error_status=0'));
				return $k->getPClasses();
			}
			catch(Exception $e) {
			    //Something went wrong, print the message: 
			    //$redirect_url = 'WC_Gateway_Klarna_Account&klarna_error_status=1&klarna_error_code=' . $e->getCode();
				    
			   //wp_redirect(admin_url($redirect_url));
			   //wp_redirect(get_payment_gateway_configuration_url($redirect_url));
			    return false;
			}
			
		} // End if $k->getPClasses
	} // End function


	/**
	 * Calc monthly cost on single Product page and print it out
	 **/
	 
	function print_product_monthly_cost() {
		
		if ( $this->enabled!="yes" || $this->get_klarna_locale(get_locale()) == 'nl_nl' ) return;
			
		global $woocommerce, $product, $klarna_account_shortcode_currency, $klarna_account_shortcode_price, $klarna_shortcode_img, $klarna_account_country;
		
		$klarna_product_total = $product->get_price();
		
		// Product with no price - do nothing
		if ( empty($klarna_product_total) ) return;
		
		$sum = apply_filters( 'klarna_product_total', $klarna_product_total ); // Product price.
		$sum = trim($sum);
		
	 	// Only execute this if the feature is activated in the gateway settings
		if ( $this->show_monthly_cost == 'yes' ) {

	    		
    		// Monthly cost threshold check. This is done after apply_filters to product price ($sum).
	    	if ( $this->lower_threshold_monthly_cost < $sum && $this->upper_threshold_monthly_cost > $sum ) {
	    		$data = new WC_Gateway_Klarna_Invoice;
	    		$invoice_fee = $data->get_invoice_fee_price();
	    		
	    		?>
				<div style="width:220px; height:70px" 
				     class="klarna-widget klarna-part-payment"
				     data-eid="<?php echo $this->get_eid();?>" 
				     data-locale="<?php echo $this->get_klarna_locale(get_locale());?>"
				     data-price="<?php echo $sum;?>"
				     data-layout="pale"
				     data-invoice-fee="<?php echo $invoice_fee;?>">
				</div>
		
				<?php
	    		
	    		// Show klarna_warning_banner if NL
				if ( $this->shop_country == 'NL' ) {
					echo '<img src="' . $this->klarna_wb_img_single_product . '" class="klarna-wb" style="max-width: 100%;"/>';	
				}
	    				    	
	    	} // End threshold check
		
		} // End show_monthly_cost check
		
	} // End function
	
	
	
	/**
	 * footer_scripts()
	 * Disable the radio button for the Klarna Account payment method if Company name is entered and the customer is from Germany or Austria
	 **/
	function footer_scripts () {
			
		if ( is_checkout() && $this->enabled=="yes" ) {
			?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ajaxComplete(function(){
		
				    if (jQuery.trim(jQuery('input[name=billing_company]').val()) && (jQuery( "#billing_country" ).val()=='DE' || jQuery( "#billing_country" ).val()=='AT')) {
				    	
				        jQuery('#payment_method_klarna_account').prop('disabled', true);
				        
				    } else jQuery('#payment_method_klarna_account').prop('disabled', false);
				    
					
				});
				
				jQuery(document).ready(function($){
											    
					$(window).load(function(){
						
						$('input[name=billing_company]').keyup(function() {
						    if ($.trim(this.value).length && ($( "#billing_country" ).val()=='DE' || $( "#billing_country" ).val()=='AT')) {
						    	
						        $('#payment_method_klarna_account').prop('disabled', true);
						        
						    } else $('#payment_method_klarna_account').prop('disabled', false);
						});
						
					});	
				});
				//]]>
			</script>
			<?php
		}
	
	} // end footer_scripts
	
	
	// Get Monthly cost prio - product page
	function get_monthly_cost_prio() {
		return $this->show_monthly_cost_prio;
	}
	
	// Get Monthly cost prio - shop base page (and archives)
	function get_monthly_cost_shop_prio() {
		return $this->show_monthly_cost_shop_prio;
	}
	
	// Helper function - get Enabled
	function get_enabled() {
		return $this->enabled;
	}
	
	// Helper function - get Klarna locale based on current locale
	function get_klarna_locale($locale) {
		switch ( $locale )
		{
		case 'da_DK':
			$klarna_locale = 'da_dk';
			break;
		case 'de_DE' :
			$klarna_locale = 'de_de';
			break;
		case 'no_NO' :
		case 'nb_NO' :
		case 'nn_NO' :
			$klarna_locale = 'nb_no';
			break;
		case 'nl_NL' :
			$klarna_locale = 'nl_nl';
			break;
		case 'fi_FI' :
		case 'fi' :
			$klarna_locale = 'fi_fi';
			break;
		case 'sv_SE' :
			$klarna_locale = 'sv_se';
			break;
		case 'de_AT' :
			$klarna_locale = 'de_at';
			break;
		case 'en_US' :
		case 'en_GB' :
			$klarna_locale = 'en_se';
			break;
		default:
			$klarna_locale = '';
		}
		
		return $klarna_locale;
	} // End function
	
	
	// Helper function - get eid
	function get_eid() {
		global $woocommerce;
		$country = ( isset( $woocommerce->customer->country ) ) ? $woocommerce->customer->country : '';
	
		if( empty($country) ) {
			$country = $this->shop_country;
		}
		
		$current_eid = '';
		
		switch ( $country )
		{
		case 'DK':
			$current_eid = $this->eid_dk;
			break;
		case 'DE' :
			$current_eid = $this->eid_de;
			break;
		case 'NL' :
			$current_eid = $this->eid_nl;
			break;
		case 'NO' :
			$current_eid = $this->eid_no;
			break;
		case 'FI' :
			$current_eid = $this->eid_fi;
			break;
		case 'SE' :
			$current_eid = $this->eid_se;
			break;
		case 'AT' :
			$current_eid = $this->eid_at;
			break;
		default:
			$current_eid = '';
		}
		
		return $current_eid;
	} // End function
	
	
	// Helper function - get secret
	function get_secret() {
		
		global $woocommerce;
		$country = ( isset( $woocommerce->customer->country ) ) ? $woocommerce->customer->country : '';
	
		if( empty($country) ) {
			$country = $this->shop_country;
		}
		
		$current_secret = '';
		
		switch ( $country )
		{
		case 'DK':
			$current_secret = $this->secret_dk;
			break;
		case 'DE' :
			$current_secret = $this->secret_de;
			break;
		case 'NL' :
			$current_secret = $this->secret_nl;
			break;
		case 'NO' :
			$current_secret = $this->secret_no;
			break;
		case 'FI' :
			$current_secret = $this->secret_fi;
			break;
		case 'SE' :
			$current_secret = $this->secret_se;
			break;
		case 'AT' :
			$current_secret = $this->secret_at;
			break;
		default:
			$current_secret = '';
		}
		
		return $current_secret;
	} // End function
	
	
	// Helper function - get correct currency for selected country
	function get_currency_for_country($country) {
				
		switch ( $country )
		{
		case 'DK':
			$currency = 'DKK';
			break;
		case 'DE' :
			$currency = 'EUR';
			break;
		case 'NL' :
			$currency = 'EUR';
			break;
		case 'NO' :
			$currency = 'NOK';
			break;
		case 'FI' :
			$currency = 'EUR';
			break;
		case 'SE' :
			$currency = 'SEK';
			break;
		case 'AT' :
			$currency = 'EUR';
			break;
		default:
			$currency = '';
		}
		
		return $currency;
	} // End function
	
	
	// Helper function - get correct language for selected country
	function get_klarna_language($country) {
				
		switch ( $country )
		{
		case 'DK':
			$language = 'DA';
			break;
		case 'DE' :
			$language = 'DE';
			break;
		case 'NL' :
			$language = 'NL';
			break;
		case 'NO' :
			$language = 'NB';
			break;
		case 'FI' :
			$language = 'FI';
			break;
		case 'SE' :
			$language = 'SV';
			break;
		case 'AT' :
			$language = 'DE';
			break;
		default:
			$language = '';
		}
		
		return $language;
	} // End function
	
	
	// Helper function - get Klarna country
	function get_klarna_country() {
		global $woocommerce;
		
		if ($woocommerce->customer->get_country()) {
			
			$klarna_country = $woocommerce->customer->get_country();
		
		} else {
		
			$klarna_country = $this->shop_language;
			
			switch ( $this->shop_country ) {
				case 'NB' :
					$klarna_country = 'NO';
					break;
				case 'SV' :
					$klarna_country = 'SE';
					break;
			}
		
		}
		
		// Check if $klarna_country exist among the authorized countries
		if(!in_array($klarna_country, $this->authorized_countries)) {
			return $this->shop_country;
		} else {
			return $klarna_country;
		}
	} // End function
	
	
	// Helper function - invoice icon
	function get_account_icon() {
		
		global $woocommerce;
		$country = ( isset( $woocommerce->customer->country ) ) ? $woocommerce->customer->country : '';
	
		if( empty($country) ) {
			$country = $this->shop_country;
		}
		
		$current_secret = '';
		
		switch ( $country )
		{
		case 'DK':
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/da_dk/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'DE' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/de_de/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'NL' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/nl_nl/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'NO' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/nb_no/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'FI' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/fi_fi/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'SE' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		case 'AT' :
			$klarna_account_icon = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/de_at/basic/blue-black.png?width=100&eid=' . $this->get_eid();
			break;
		default:
			$klarna_account_icon = '';
		}
		
		return $klarna_account_icon;
	} // End function	
			 
} // End class WC_Gateway_Klarna_Account