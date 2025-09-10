enviar_email_validacion($destinatario, $codigo);

<?php

require ("script.php");

$subject = "Test";
$message = "Test message";
$email = "emyluhmann@gmail.com";

echo sendMail($email, $subject, $message);

?>