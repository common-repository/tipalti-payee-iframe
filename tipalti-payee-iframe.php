<?php
/**
 * Plugin Name: Tipalti Payee Iframe
 * Description: This plugin allows Tipalti payers to embed payee portal IFRAME onto their Wordpress site so that payees can add and view their information.
 * Version: 1.0.5
 * Author: Tipalti
 * Author URI: https://tipalti.com/
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tipalti-payee-iframe
*/


// Defining main constants
define('TIPALTI_PAYEE_IFRAME_VERSION', '1.0.5' );
define('TIPALTI_PAYEE_IFRAME_MAIN_FILE_PATH', __FILE__ );
define('TIPALTI_PAYEE_IFRAME_BASE_PATH', dirname(TIPALTI_PAYEE_IFRAME_MAIN_FILE_PATH) );
define('TIPALTI_SANDBOX_URL', 'https://ui2.sandbox.tipalti.com/' );
define('TIPALTI_PRODUCTION_URL', 'https://ui2.tipalti.com/' );

class Tipalti_Payee_Iframe {

  
    public function __construct() {
     
        /* Load plugins files */
        add_action( 'plugins_loaded', array($this,'ini' ), -10);
       
    }

    function ini(){
        $this->check_version();
        $this->load_settings();
        $this->load_shortcodes();
        $this->load_payee();
        $this->load_encrypt();
    }

    private function check_version(){
        if(TIPALTI_PAYEE_IFRAME_VERSION !== get_option('tipalti_iframe_version'))
            update_option('tipalti_iframe_version', TIPALTI_PAYEE_IFRAME_VERSION);
    }

   
    private function load_settings(){
         if( is_admin() ){
            require_once( TIPALTI_PAYEE_IFRAME_BASE_PATH . '/includes/settings.php' );
            $tipalti_iframe_settings = new Tipalti_Iframe_Settings();
        }
    }

    private function load_shortcodes(){
        require_once( TIPALTI_PAYEE_IFRAME_BASE_PATH . '/includes/shortcodes.php' );
        $tipalti_iframe_shortcodes = new Tipalti_Iframe_Shortcodes();
    }

    private function load_payee(){
        require_once( TIPALTI_PAYEE_IFRAME_BASE_PATH . '/includes/payee.php' );
        $tipalti_iframe_payee= new Tipalti_Iframe_Payee();
    }

    private function load_encrypt(){
        require_once(TIPALTI_PAYEE_IFRAME_BASE_PATH . '/includes/encrypt.php' );
        $cypto = new BLING_Crypto();
    }

    // private function tipalti_iframe_activation_hook(){
    //    $this->load_encrypt();

    // }

    
}

new Tipalti_Payee_Iframe();


