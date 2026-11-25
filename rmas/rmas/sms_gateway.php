<?php
/** Load the optional private SMS transport only when an SMS is actually requested. */
function rma_send_sms_notice($phone, $phone2, $message)
{
    try {
        if (!function_exists('send_sms_nos_detalhado')) {
            $configured = getenv('DOTBRIGHT_NOS_SMS_LIBRARY');
            $paths = array_filter(array($configured ?: null,
                dirname(__DIR__, 3).'/private/lib/nos-sms-secure/funcoes_nos_sms.inc.php',
                '/home/dotbright/private/lib/nos-sms-secure/funcoes_nos_sms.inc.php'));
            foreach (array_unique($paths) as $library) {
                if (is_file($library) && is_readable($library)) { require_once $library; break; }
            }
        }
        if (!function_exists('send_sms_nos_detalhado')) return array('success'=>false,'status'=>'transport_unavailable');
        $result = send_sms_nos_detalhado($phone, $phone2, $message);
        return is_array($result) ? $result : array('success'=>false,'status'=>'invalid_response');
    } catch (Throwable $e) {
        error_log('[RMA SMS] transport_failed');
        return array('success'=>false,'status'=>'transport_failed');
    }
}
