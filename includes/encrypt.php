<?php
require_once( TIPALTI_PAYEE_IFRAME_BASE_PATH .'/includes/defuse/load.php' );
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
class BLING_Crypto{

    private function debug(){
        return false;
    }

    public function __construct(){
        $this->db_option_name = "_tip_enc_key";
        $this->encypt_key = $this->get_encypt_key();
        $error = $this->check_db_key_error();
        if ($error) $this->generate_key();
    }

    private function get_encypt_key(){
        if ($this->debug()) $this->log("get_encypt_key");
        global $wpdb;
      
        $key = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM " . $wpdb->options . "
                WHERE option_name = %s", $this->db_option_name
            )
        );
        return $key;
    }

    private function generate_key(){
        // check if key exists, delete and generate a new set
        if ( isset($this->encypt_key)  && !empty($this->encypt_key)){
            if ($this->debug()) $this->log("key is set");
            if ($this->debug()) $this->log($this->encypt_key);
            $this->delete_key();
        }
        if ($this->debug()) $this->log("generate_key");
        $key = Key::createNewRandomKey();
        $key_string = $key->saveToAsciiSafeString();
        // save in database
        global $wpdb;
        $optionValue = $key_string;
        $wpdb->query(
            $wpdb->prepare(
               "
               INSERT INTO $wpdb->options
               ( option_name, option_value )
               VALUES ( %s, %s )
               ",
               $this->db_option_name,
               $optionValue
            )
         );

    }

    private function delete_key(){
        global $wpdb;
        if ($this->debug()) $this->log("delete_key");
        $sql = "DELETE FROM `". $wpdb->options."` WHERE `option_name` = '".$this->db_option_name."' ";
        $this->log($sql);
        
        try {
            $wpdb->query($wpdb->prepare($sql));
            return true;
        } catch (Exception $e) {
            return 'Error! '. $wpdb->last_error;
        }
    }
    

    private function check_db_key_error(){
        if ( !isset($this->encypt_key)  && empty($this->encypt_key)) return true;
        $value = "A COOL PLUGIN";
        $key = $this->encypt_key;
        $enc = $this->enc($value);
        $dec = $this->dec($enc);

        if ($value != $dec) return true;
        else return false;

    }

    private function log($msg){
        $msg = $msg . "\n";
        file_put_contents($this->log_file(), $msg, FILE_APPEND);
    }

    private function log_file(){
        return TIPALTI_PAYEE_IFRAME_BASE_PATH ."/log.txt";
    }

    private function key_file(){
        return TIPALTI_PAYEE_IFRAME_BASE_PATH ."/tipalti.txt";
    }
   
    public function enc($value){
        if (!$value) return;
        $key = $this->encypt_key;
        $key = Key::loadFromAsciiSafeString($key);
        if ($this->debug()) { $this->log("key : ".Crypto::encrypt( $value, $key)); }
        return Crypto::encrypt( $value, $key);
    }

    public function dec($value){
        if (!$value) return;
        $key = $this->encypt_key;
        $key = Key::loadFromAsciiSafeString($key);
        try {
            $value = Crypto::decrypt($value, $key );
            return $value;
        } catch ( WrongKeyOrModifiedCiphertextException $e) {
            return new WP_Error( $e->getCode(), $e->getMessage() );
        }

    }   
}



