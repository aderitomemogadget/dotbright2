<?php

if (!function_exists('rma_notification_policy')) {
    /**
     * Decide os canais da notificação sem misturar a opção de e-mail com o SMS.
     *
     * @return array{email:bool,sms:bool}
     */
    function rma_notification_policy($fake, $nao_envio_email, $envia_mail, $texto, $envia_sms, $texto_sms)
    {
        if ((int) $fake === 1) {
            return array('email' => false, 'sms' => false);
        }

        return array(
            'email' => (int) $nao_envio_email !== 1 && (int) $envia_mail === 1 && trim((string) $texto) !== '',
            'sms' => (int) $envia_sms === 1 && trim((string) $texto_sms) !== '',
        );
    }
}
