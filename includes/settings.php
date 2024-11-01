<?php
if(!class_exists('Tipalti_Iframe_Settings'))
{
    class Tipalti_Iframe_Settings
    {
        /**
         * Construct the plugin object
         */

        function __construct()
        {
            // register actions
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'add_menu'));
            add_filter('pre_update_option_tipalti_iframe', array($this, 'handle_encryption'), 10, 2);
 
        } 
    
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
            register_setting(
                'tipalti_iframe',  // Option group
                'tipalti_iframe', // Option name
                array( &$this, 'sanitize' ) // Sanitize
            );


            // add your settings section
            add_settings_section(
                'tipalti_payee_iframe_section', // section ID
                'Tipalti Payee Iframe',  // section title
                array(&$this, 'render_section_help_html'),   // settings callback function
                'tipalti_iframe' //page
            );
        
            /* Field: Masterkey */
            add_settings_field(
                'tipalti_payee_iframe_masterkey', // field ID
                'API Key/Payer Masterkey', // field title 
                array( &$this, 'render_masterkey_html' ), // field callback function
                "tipalti_iframe", // settings page slug
                'tipalti_payee_iframe_section' // section ID
            );

            /* Field: Payer Name */
            add_settings_field(
                'tipalti_payee_iframe_payer_name', // field ID
                'Payer Assigned Name', // field title 
                array( &$this, 'render_payername_html' ), // field callback function
                "tipalti_iframe", // settings page slug
                'tipalti_payee_iframe_section' // section ID
            );

             /* Field: Skin Name */
             add_settings_field(
                'tipalti_payee_iframe_skin_name', // field ID
                'Preferred Skin (optional)', // field title 
                array( &$this, 'render_skinname_html' ), // field callback function
                "tipalti_iframe", // settings page slug
                'tipalti_payee_iframe_section' // section ID
            );

             /* Selection: Sandbox / Production */
             add_settings_field(
                'tipalti_payee_iframe_sandbox', // field ID
                'Sandbox', // field title 
                array( &$this, 'render_sandbox_switch_html' ), // field callback function
                "tipalti_iframe", // settings page slug
                'tipalti_payee_iframe_section' // section ID
            );
            // Possibly do additional admin_init tasks
        } // END public static function activate

         function render_sandbox_switch_html()
        {
            $options = get_option( 'tipalti_iframe' );
            if( isset($options['sandbox'] )) {$value = esc_attr($options['sandbox']);}
                else {$value="";}
            echo "
            <input id='tipalti_frame_sandbox' name='tipalti_iframe[sandbox]' type='checkbox' value='1'".checked( 1, $value, false ) ."/>
            ";

        }
    
         function render_section_help_html()
        {
            ?>
            <p style="padding-right: 20px;">This plugin allows Tipalti payers to embed payee portal IFRAME onto their Wordpress site so that payees can add and view their information. The portal elements can be embedded into any post or page and website owners can essentially define their own menu structure. They can easily make the IFRAME experience a part of the rest of their Wordpress site. The plugin also directly links Wordpress Users with Payee IDs so users can securely log into Wordpress and be associated with a Payee ID.
</p>
            
            <?php
        }
    
        /**
         * This functions provide text inputs for settings fields
         */
          function render_masterkey_html(){
            $options = get_option( 'tipalti_iframe' );
            if($options) $value = esc_attr($options['masterkey']);
                else $value = "";  
            echo "<input id='tipalti_frame_masterkey' name='tipalti_iframe[masterkey]' type='password' size='70' value='$value' />";
    
        }
    
        function render_payername_html(){
            $options = get_option( 'tipalti_iframe' );
            if($options) $value = esc_attr($options['payer_name']);
                else $value = "";
            echo "<input id='tipalti_frame_payer_name' name='tipalti_iframe[payer_name]' type='text' size='70' value='$value'/>";
    
        }
        function render_skinname_html(){
            $options = get_option( 'tipalti_iframe' );
            if($options) $value = esc_attr($options['skin_name']);
                else $value = "";
            echo "<input id='tipalti_frame_skin_name' name='tipalti_iframe[skin_name]' type='text' size='70' value='$value'/>";
    
        }
    
        /**
         * add a menu
         */     
         public function add_menu()
        {
            // Add a page to manage this plugin's settings
            add_options_page(
                'Tipalti Payee Iframe', //title of the options page
                'Tipalti Payee Iframe', // label for the admin panel
                'manage_options', // capabilities
                'tipalti_iframe', // slug to identify the menu
                array(&$this, 'render_settings_page') // function to output HTML 
            );
        } // END public function add_menu()

         /**
         * Menu Callback
         */ 

         public function render_settings_page(){
            if(!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            ?>
            <form action="options.php" method="post">
                <?php 
                settings_fields( 'tipalti_iframe' );
                do_settings_sections( 'tipalti_iframe' ); ?>
                <input name="submit" class="button" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
            </form>
            <?php
    
        }// END public function render_settings_page()

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
          function sanitize( $input )
        {
            $new_input = array();
            if( isset( $input['masterkey'] ) )
                $new_input['masterkey'] = sanitize_text_field( $input['masterkey'] );

            if( isset( $input['payer_name'] ) )
                $new_input['payer_name'] = sanitize_text_field( $input['payer_name'] );
            
            if( isset( $input['skin_name'] ) )
                $new_input['skin_name'] = sanitize_text_field( $input['skin_name'] );
            
            if( isset( $input['sandbox'] ) )
                $new_input['sandbox'] = sanitize_text_field( $input['sandbox'] );

            return $new_input;
        }


         function handle_encryption($new, $old){

            $new_input = array();
            // check if string is encrypted
            if( isset( $new['masterkey'] ) && $this->is_text_string($new['masterkey'])){
                
                $new_input['masterkey'] = sanitize_text_field( $new['masterkey'] );
                $crypto = new BLING_Crypto();
                $option = $new_input['masterkey'];
                $encryption = $crypto->enc($option);
                $new_input['masterkey']  = $encryption;
            } else $new_input['masterkey'] = $new['masterkey'];

            if( isset( $new['payer_name'] ) )
                $new_input['payer_name'] = sanitize_text_field( $new['payer_name'] );
            
            if( isset( $new['skin_name'] ) )
                $new_input['skin_name'] = sanitize_text_field( $new['skin_name'] );
            
            if( isset( $new['sandbox'] ) )
                $new_input['sandbox'] = sanitize_text_field( $new['sandbox'] );

            return $new_input;

        }

        private function is_text_string($string){
            if(strncmp($string, "def", 3) === 0) return false;
            else return true;
        }

        private function log($msg){
            $msg = $msg . "\n";
            file_put_contents($this->log_file(), $msg, FILE_APPEND);
        }
    
        private function log_file(){
            return TIPALTI_PAYEE_IFRAME_BASE_PATH ."/log.txt";
        }
   
    } 
} 

