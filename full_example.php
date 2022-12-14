<?php

require '/path/to/SMTPMailer.php';

// Instantiation.
$mail = new SMTPMailer;

$mail->SMTPHost = 'mail.server.com';
$mail->Port     = 465;
$mail->SMTPSecure = 'SSL';
$mail->Username = 'user@server.com';  // SMTP username.
$mail->Password = 'password';         // SMTP password.
$mail->transfer_encoding = '7bit';

$mail->setFrom('me@server.com');
$mail->addAddress('someone@destination.com');
$mail->addAddress('another@person.com');
$mail->addCC('reply@my-server.com');
$mail->addBCC('secret@invisible.com');

$mail->Subject = 'Greetings';

$mail->bodyHTML = <<<"HTML"
	This is a test from {$mail->SMTPHost} on port {$mail->Port}
	<br>
	<b>Greetings!</b>
	HTML;

$mail->bodyPlain = <<<"PLAIN"
	This is a test from {$mail->SMTPHost} on port {$mail->Port}.
	Greetings!
	PLAIN;

// Attachments (use an array).
$mail->addAttachment(att_path: ['/path/to/attachment.jpg'],
                     att_encoding: 'base64',
					           att_type: 'image/jpg',
					);

echo PHP_EOL;
if ($mail->Send()) { echo 'Mail was sent successfully!'. PHP_EOL; }
else               { echo 'Mail failure!!!'. PHP_EOL; }
