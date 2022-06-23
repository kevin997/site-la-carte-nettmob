<?php
header('Access-Control-Allow-Origin: *');

$isWP = false;
if (file_exists("../../../../../wp-load.php")) {
    include("../../../../../wp-load.php");
    $isWP = true;
}

$emailTo       = '';
$sender_email = 'contact@lacartenettmob.com';
$subject = 'Vous avez reçu un nouveau message';

$errors = array();
$data   = array();
$body    = '';
$email = '';
$name = '';
$domain = 'lacartenettmob.com';
if (isset($_POST['email'])) $domain = $_POST['domain'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arr = $_POST['values'];
    $sender_email = 'contact@' . $domain;
    $email = 'no-replay@' . $domain;
    $error = "Erreur. Le message n'a pas été envoyé.";

    if (isset($_POST['email']) && strlen($_POST['email']) > 0)  $emailTo = $_POST['email'];
    if (isset($_POST['subject_email']) && strlen($_POST['subject_email']) > 0) $subject = $_POST['subject_email'];
    else $subject = '[' . $domain . '] Nouveau message';

    foreach ($arr as $key => $value ) {
        $val =  stripslashes(trim($value[0]));
        if (!empty($val)) {
            $body .= ucfirst($key) . ': ' . $val . PHP_EOL . PHP_EOL;
            if ($key == "email"||$key == "Email"||$key == "E-mail"||$key == "e-mail"||strpos($key, "mail") > -1) $email = $val;
            if ($key == "name"||$key == "nome"||$key == "Nome") $name = $val;
        }
    }
    $body .= "-------------------------------------------------------------------------------------------" . PHP_EOL . PHP_EOL;
    $body .= "Nouveau message de " . $domain;

    if ($name == '') $name = $subject;
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        ini_set("sendmail_from", $email);
        $result = mail($emailTo, $subject, $body, $headers);
        
        if ($result) {
            $data['success'] = true;
            $data['message'] = 'Félicitations. Votre message a été envoyé avec succès.';
        } else {
            $data['success'] = false;
            $data['message'] = $error;
        }
    }
    echo json_encode($data);
}