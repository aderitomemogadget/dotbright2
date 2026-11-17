<?php
// Dedicated bounded transport. Does not use or change the legacy email queue.
function store_withdrawal_deliver($email,$subject,$html)
{
    $base=dirname(__DIR__).'/external_api/PHPMailer-master/src/';
    foreach (array('Exception','PHPMailer','SMTP') as $file) require_once $base.$file.'.php';
    $class=class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'PHPMailer\PHPMailer\PHPMailer' : 'PHPMailer';
    $mail=new $class(true);
    $mail->isSMTP(); $mail->SMTPDebug=0; $mail->Timeout=20; $mail->Timelimit=20;
    $mail->Host=defined('SMTP_HOST') && SMTP_HOST!=='' ? SMTP_HOST : ($GLOBALS['Default_SMTP'] ?? '');
    $mail->Username=defined('SMTP_LOGIN') && SMTP_LOGIN!=='' ? SMTP_LOGIN : ($GLOBALS['Default_SMTP_login'] ?? '');
    $mail->Password=defined('SMTP_PASS') && SMTP_PASS!=='' ? SMTP_PASS : ($GLOBALS['Default_SMTP_pass'] ?? '');
    $from=defined('SMTP_EMAIL') && SMTP_EMAIL!=='' ? SMTP_EMAIL : ($GLOBALS['Default_SMTP_Email'] ?? '');
    $port=defined('SMTP_PORTA') && SMTP_PORTA ? (int)SMTP_PORTA : (int)($GLOBALS['Default_SMTP_porta'] ?? 587);
    $mail->Port=$port ?: 587; $mail->SMTPSecure=$mail->Port===465 ? 'ssl' : 'tls';
    $mail->SMTPAuth=$mail->Username!=='';
    if ($mail->Host==='' || !filter_var($from,FILTER_VALIDATE_EMAIL)) return false;
    $mail->CharSet='UTF-8'; $mail->setFrom($from,'Dotbright');
    $mail->addAddress($email); $mail->Subject=$subject; $mail->isHTML(true); $mail->Body=$html;
    $mail->AltBody=html_entity_decode(strip_tags(str_replace(array('</h1>','</pre>'),"\n",$html)),ENT_QUOTES,'UTF-8');
    return $mail->send();
}
