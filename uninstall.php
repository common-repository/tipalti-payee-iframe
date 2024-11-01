<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'tipalti_iframe';
 
delete_option($option_name);
 
// for site options in Multisite
delete_site_option($option_name);
 
// delete user meta tipalti_payee_id
$meta_type  = 'user';
$user_id    = 0; // This will be ignored, since we are deleting for all users.
$meta_key   = 'tipalti_payee_id';
$meta_value = ''; // Also ignored. The meta will be deleted regardless of value.
$delete_all = true;

delete_metadata( $meta_type, $user_id, $meta_key, $meta_value, $delete_all );