<?php
function SendPulseInstallStep2(){
    $plugin_extra_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'extra' . DIRECTORY_SEPARATOR;
    $ptn = "$plugin_extra_dir*";
    $dest_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
    $notices= get_option('send_pulse_deferred_admin_notices', array());
    foreach (glob($ptn) as $filename) {
        if (!copy($filename, $dest_dir . basename($filename))) {
            $notices[]= "Failed to copy plugin files, check permition on dir '$dest_dir'...\n";
        }
    }
    update_option('send_pulse_deferred_admin_notices', $notices);
}
function SendPulseDeinstallStep2(){
    $plugin_extra_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'extra' . DIRECTORY_SEPARATOR;
    $ptn = "$plugin_extra_dir*";
    $dest_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
    
    foreach (glob($ptn) as $filename) {
        @unlink($dest_dir . basename($filename));
    }
    
    delete_option('sendpulse_code');
    delete_option('sendpulse_active');
    delete_option('sendpulse_addinfo');
}
?>