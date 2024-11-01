<?php

if(!class_exists('Tipalti_Iframe_Shortcodes'))
{
    class Tipalti_Iframe_Shortcodes
    {
   

        public function __construct()
        {
            add_shortcode('tipalti-payee-iframe', array($this,'render_tipalti_payee_iframe'));
        } 

        public function render_tipalti_payee_iframe($options){
            
            // protected Area
            if ( ! is_user_logged_in()) {
                $this->render_login_form(); 
                return;
            }

            $is_payee = get_user_meta( get_current_user_id(), 'tipalti_is_payee', true);
            if (! $is_payee) {
                return "Access Denied.";
            }
    
             // continue for logged in user
            extract(shortcode_atts(array(
                'type' => 'account-settings'
            ), $options));

            $iframe_url = $this->get_iframe_url($type);
            $iframe_id = "tipaltiIframe-".$type;
            ob_start();
            include TIPALTI_PAYEE_IFRAME_BASE_PATH . "/includes/iframe.php";
            $html = ob_get_clean();
         
            return $html;
    
        }

        private function get_iframe_url($iframe){

            $options = get_option( 'tipalti_iframe' );
            if (isset($options['sandbox']) && $options['sandbox']== 1) $url = TIPALTI_SANDBOX_URL;
                else $url = TIPALTI_PRODUCTION_URL;

            switch ($iframe) {
                case 'account-settings':
                    $path = $url . "PayeeDashboard/home?";
                    break;
        
                case 'invoice-history':
                    $path = $url . "PayeeDashboard/Invoices?";
                    break;
        
                case 'payment-history':
                    $path = $url . "PayeeDashboard/PaymentsHistory?";
                    break;
        
                case 'submit-invoice':
                    $path = $url . "PayeeDashboard/SubmitInvoice?";
                    break;
        
            }
            
            // construct params
            $credentials = $this->get_credentials();
            if (!isset($credentials['payer_name']) || !isset($credentials['masterkey']) || !isset($credentials['idap'])){
                return;
            } 
   
            $payer = $credentials['payer_name'];
            $crypto = new BLING_Crypto();
            $masterKey = $crypto->dec($credentials['masterkey']);
            $idap = $credentials['idap'];
            $cskin = "TipaltiBrand";
            if (isset($credentials['skin_name']) && !empty($credentials['skin_name'])) $cskin=$credentials['skin_name'];
            $ts = time();
            $str = "idap=".$idap."&payer=".$payer."&cskin=".$cskin."&ts=".$ts;
            $encodedStr = utf8_encode($str); 
            $hashkey = $this->getHashkey($encodedStr, $masterKey);
            $params = $str."&hashkey=".$hashkey;
            $url = $path . $params;

            return $url;
  
  
        }
    
        private function render_login_form(){
            ?>
            <div class="login_container">
            <?php wp_login_form(); ?>
            </div>
            <?php
        }

        private function iframe_HTML(){

        }

        private function getHashkey($string,  $masterKey){
            return hash_hmac('sha256', $string, $masterKey);
        }

        private function get_credentials(){
            $options = get_option( 'tipalti_iframe' );
            $credentials = $options;
            $payee_identifier = get_the_author_meta( 'tipalti_payee_id', get_current_user_id() );
            if (!$payee_identifier) {
                $payee_identifier = $options['payer_name']."WPIFR_".get_current_user_id();
                update_user_meta( get_current_user_id(), 'tipalti_payee_id', $payee_identifier );
            }
            $idap = $payee_identifier;
            $credentials['idap'] = $idap;
            return $credentials;
        }
    
       
   
    } 
} 

