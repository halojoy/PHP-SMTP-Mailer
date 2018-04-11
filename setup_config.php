<?php
/*************************************************************
 Description: Setup configuration for the SMTP Mail Class
 Author     : halojoy  https://github.com/halojoy
 Copyright  : 2018 halojoy
 License    : MIT License  https://opensource.org/licenses/MIT
 *************************************************************/
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {width: 600px; margin: 20px auto;}
    </style>
</head>
<body>
<?php

if (isset($_POST['submit'])) {

    $write = <<<EOF
<?php

\$cfg_server   = '{$_POST['server']}';
\$cfg_port     =  {$_POST['port']};
\$cfg_secure   = '{$_POST['secure']}';
\$cfg_username = '{$_POST['usernm']}';
\$cfg_password = '{$_POST['passwd']}';

EOF;
    file_put_contents('conf/config_smtp.php', $write);
    echo 'Success.<br>
    Your data has been put in <b>conf/config_smtp.php</b> file.<br>
    You can now use <b>SMTP Mailer</b>
    </body>
    </html>';
    exit();

}

?>
<form method="post">
<table>
    <tr>
    <td></td>
    <td><h3>Setup config for SMTP Mailer</h3></td></tr>
    <tr>
    <td></td>
    <td><b>SMTP Server Settings</b></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td valign="top">SMTP Server, Host</td>
    <td><input type="radio" name="server" value="smtp.gmail.com" checked> smtp.gmail.com<br>
        <input type="radio" name="server" value="smtp-mail.outlook.com"> smtp-mail.outlook.com<br>
        <input type="radio" name="server" value="smtp.live.com"> smtp.live.com<br>
        <input type="radio" name="server" value="smtp.mail.yahoo.com"> smtp.mail.yahoo.com<br>
        <input type="radio" name="server" value="smtp.aol.com"> smtp.aol.com<br>
        <input type="radio" name="server" value="smtp.gmx.com"> smtp.gmx.com<br>
        <input type="radio" name="server" value="smtp.zoho.com"> smtp.zoho.com<br>
        <input type="radio" name="server" value="smtp.yandex.com"> smtp.yandex.com<br>
        <input type="radio" name="server" value="smtp.mailjet.com"> smtp.mailjet.com<br>
        <input type="radio" name="server" value="in.mailjet.com"> in.mailjet.com<br>
        <input type="radio" name="server" value="in-v3.mailjet.com"> in-v3.mailjet.com<br>
        <input type="radio" name="server" value="smtp.lycos.com"> smtp.lycos.com<br>
        <input type="radio" name="server" value="mail.smtp2go.com"> mail.smtp2go.com<br>
        <input type="radio" name="server" value="pro.turbo-smtp.com"> pro.turbo-smtp.com

        </td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td valign="top">Security Protocol</td>
    <td><input type="radio" name="secure" value="tls" checked>TLS&nbsp;&nbsp;
        <input type="radio" name="secure" value="ssl">SSL&nbsp;&nbsp;
        <input type="radio" name="secure" value="">No secure</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td valign="top">Server Port Number</td>
    <td><input type="radio" name="port" value="587" checked> 587 (for TLS)<br>
        <input type="radio" name="port" value="465"> 465 (for SSL)<br>
        <input type="radio" name="port" value="25">&nbsp;&nbsp;&nbsp;25 (No secure)</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td></td>
    <td><b>SMTP Server Login</b></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td>Your Username</td>
    <td><input name="usernm" size="35" required></td></tr>
    <tr>
    <td>Your Password</td>
    <td><input name="passwd" size="35" required></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    <td></td>
    <td><input type="submit" name="submit"></td></tr>
</table>
</form>
</body>
</html>
