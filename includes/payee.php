<?php
if(!class_exists('Tipalti_Iframe_Payee'))
{
    class Tipalti_Iframe_Payee
    {

        /**
         * Construct the plugin object
         */
        public function __construct()
        {   
            add_action( 'user_profile_update_errors', array(&$this,'user_profile_update_error' ), 10, 3 );
            add_action( 'edit_user_profile', array(&$this,'render_payee_fields' ));
            add_action( 'edit_user_profile_update', array(&$this,'save_payee_fields' ));
         
        } // END public function __construct

        /*
        * Validation
        */
        public function user_profile_update_error( $errors, $update, $user ){
            $error_array = $this->get_payee_errors($user->ID);

            if ($error_array) {
                $errstr = implode('<br/>',$error_array);
                $errors->add( 'tipalti_payee_id_error', __( '<strong>ERROR</strong>:<br/>'.$errstr ) );
            } 

            return $errors;
                
        }

        private function get_payee_errors($user){
           
            $payee_errors = [];
            $ids_array = $this->get_payee_ids($user);
            if (strlen($_POST['tipalti_payee_id']) > 64 ) {
                array_push($payee_errors, "Payee Id must be no longer than 64 characters.");
            }

            if (!preg_match("/^[A-Za-z0-9,.\-_]*$/", $_POST['tipalti_payee_id']) ) {
                array_push($payee_errors, "Payee Id may only contain: <br/>numbers, letters, commas, periods, underscores, or dashes.");
            }

            if (in_array( $_POST['tipalti_payee_id'], $ids_array) && !empty($_POST['tipalti_payee_id'])) {
              array_push($payee_errors, "Payee Id already exists.");
            }

           return $payee_errors;

        }

        private function get_payee_ids($profile_user_id ){
            $ids_array = [];
            $users = get_users( array( 'fields' => array( 'ID' ) ) );

            foreach($users as $user){
                if ( $profile_user_id != $user->ID) {
                    $id = get_user_meta($user->ID,'tipalti_payee_id',true);
                    array_push($ids_array, $id);
                }
            }
            return $ids_array;
        }

        private function has_payee_errors( $user_id ){
           
            $has_error = false;
            $ids_array = $this->get_payee_ids( $user_id );
            if (strlen($_POST['tipalti_payee_id']) > 64 ) {
                $has_error = true;
                return $has_error;
            }

            if (!preg_match("/^[A-Za-z0-9,.\-_]*$/", $_POST['tipalti_payee_id']) ) {
                $has_error = true;
                return $has_error;
            }

            if (in_array( $_POST['tipalti_payee_id'], $ids_array) && !empty($_POST['tipalti_payee_id'])) {
                $has_error = true;
                return $has_error;
            }

        }



        /*
        * Sanitize and Save Tipalti Payee Identifier data
        *
        */

        public function save_payee_fields( $user_id ) {
            $payee_id = sanitize_text_field($_POST['tipalti_payee_id']);
            $is_payee = sanitize_text_field($_POST['tipalti_is_payee']);

            //check for errors before saving
            if (! $this->has_payee_errors( $user_id ) ) {
                update_user_meta( $user_id, 'tipalti_payee_id', $payee_id  );
                update_user_meta( $user_id, 'tipalti_is_payee', $is_payee  );
            }
            
        }


        public function render_payee_fields( $user ) { 
            $value = esc_attr(get_user_meta( $user->ID, 'tipalti_is_payee', true));
            if (!$value) $value = "";
        
            ?>

            <table class="form-table">
            <tr class="tipalti_payee">
            <th><label for="payee">Payee</label></th>
            <td>
            Allow Tipalti Payee IFRAME: <input type="checkbox" name="tipalti_is_payee" id="tipalti_is_payee" value="1" <?php checked($value);?>/>
            <td>
            Tipalti Payee Id : <input type="text" name="tipalti_payee_id" id="tipalti_payee_id" value="<?php echo esc_attr( get_the_author_meta( 'tipalti_payee_id', $user->ID ) ); ?>" class="regular-text" /><br/>
            </td>
            </tr>
            </table>
        <?php }


    }
}
