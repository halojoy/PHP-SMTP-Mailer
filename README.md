### PHP-SMTP-Mailer
This is a lightweight SMTP PHPMailer.<br>
Updated to work with PHP 8.0.<br>
The PHP Class supports TLS, SSL and File Attachments in mail.<br>
Simple, powerful and easy to use.

##### Features:
* Sends mail using one SMTP Server like 'smtp.gmail.com'.
* Auth login with username and password.
* Uses security protocols TLS and SSL.
* Supports 'text/html' or 'text/plain' messages.
* Supports any number of file attachments.
* Default Charset is 'UTF-8' but can be changed.
* 8bit, 7bit, Binary or Quoted-Printable transfer encoding.
* Logging of the transaction for debug.

##### Email Headers:
* From     - one email
* Reply-To - multiple possible
* To  - multiple possible
* Cc  - multiple possible
* Bcc - multiple possible

### Usage
Set your config variables in your calling script.<br>
Here is a basic example:
```php
<?php

require '/path/to/SMTPMailer.php';

// Instantiation.
$mail = new SMTPMailer;

$mail->SMTPHost = 'mail.server.com';
$mail->Username = 'user@server.com';  // SMTP username.
$mail->Password = 'password';         // SMTP password.

$mail->setFrom('me@server.com');
$mail->addAddress('someone@destonation.com');

$mail->Subject = 'Greetings';

$mail->bodyPlain = <<<"PLAIN"
	This is a test from {$mail->SMTPHost} on port {$mail->Port}.
	Greetings!
	PLAIN;

echo PHP_EOL;
if ($mail->Send()) { echo 'Mail was sent successfully!'. PHP_EOL; }
else               { echo 'Mail failure!!!'. PHP_EOL; }

?>
```
