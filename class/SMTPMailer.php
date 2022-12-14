<?php
/*************************************************************
 Description : PHP Class for sending SMTP Mail
 Orig Author : halojoy  https://github.com/halojoy/PHP-SMTP-Mailer
 Updated by  : s22_tech  https://github.com/s22_tech/PHP-SMTP-Mailer
 *************************************************************/

Class SMTPMailer
{
	public  $SMTPHost;
	public  $Port       = 465;
	public  $SMTPSecure = 'SSL';
	public  $Username   = '';
	public  $Password   = '';
	public  $SMTPDebug  = false;
	public  $Subject    = 'No subject';
	public  $bodyHTML   = '';
	public  $bodyPlain  = '';
	public  $to         = [];
	public  $from       = [];
	public  $cc         = [];
	public  $bcc        = [];
	public  $reply_to   = [];
	public  $transfer_encoding = '7bit';
	public  $charset = 'UTF-8';
	private $headers;
	private $ahead;
	private $sock;
	private $hostname;
	private $local;
	private $log = [];


	public function __construct() {
		if (!empty($_SERVER['HTTP_HOST'])) {
			$this->local = $_SERVER['HTTP_HOST'];
		}
		elseif (!empty($_SERVER['SERVER_NAME'])) {
			$this->local = $_SERVER['SERVER_NAME'];
		}
		else {
			$this->local = @$_SERVER['SERVER_ADDR'];
		}
		if ($this->Username) {
			$this->from = [$this->Username, ''];
		}
	}


  // Set from email address.
	public function setFrom($address, $name = '') {
		$this->from = [$address, $name];
	}


  // Add email reply to address.
	public function addReplyTo($address, $name = '') {
		$this->reply_to[] = [$address, $name];
	}


  // Add recipient email address.
	public function addAddress($address, $name = '') {
		$this->to[] = [$address, $name];
	}


  // Remove all email address.
	public function clearAddresses() {
		$this->to = [];
	}


  // Add carbon copy email address.
	public function addCC($address, $name = '') {
		$this->cc[] = [$address, $name];
	}


  // Add blind carbon copy email address.
	public function addBCC($address, $name = '') {
		$this->bcc[] = [$address, $name];
	}


  // Add attachment file.
	public function addAttachment(array $att_path, $att_encoding, $att_type) {
		if (!is_array($att_path)) {
			throw new Exception('$att_path must be an array in '.__FUNCTION__.'()');
		}
		$this->attachment   = $att_path;
		$this->att_encoding = $att_encoding;
		$this->att_type     = $att_type;
	}


  // Set charset. Default 'UTF-8'.
	public function Charset($charset) {
		$this->charset = $charset;
	}


  // Display current log file.
	public function show_log() {
		if ($this->SMTPDebug === true) {
			echo 'SMTP Mail Transaction Log' . PHP_EOL;
			print_r($this->log);
		}
	}


  // Display current headers.
	public function show_headers() {
		echo 'SMTP Mail Headers' . PHP_EOL;
		echo htmlspecialchars($this->do_headers(false));
	}


  // Send the SMTP Mail.
	public function Send() {
	  // Prepare data for sending.
		$this->headers = $this->do_headers();
		$user64 = base64_encode($this->Username);
		$pass64 = base64_encode($this->Password);
		$mailfrom = '<'.$this->from[0].'>';
		foreach (array_merge($this->to, $this->cc, $this->bcc) as $address) {
			$mailto[] = '<'.$address[0].'>';
		}

	  // Define connection hostname.
		$this->hostname = $this->SMTPHost;
		$this->SMTPSecure = strtolower($this->SMTPSecure);
		if ($this->SMTPSecure === 'tls') {
			$this->hostname = 'tcp://'.$this->SMTPHost;
		}
		if ($this->SMTPSecure === 'ssl') {
			$this->hostname = 'ssl://'.$this->SMTPHost;
		}
		echo 'SMTPHost: ' . $this->SMTPHost   . PHP_EOL;
		echo 'secure: '   . $this->SMTPSecure . PHP_EOL;
		echo 'hostname: ' . $this->hostname   . PHP_EOL;
		echo 'port: '     . $this->Port       . PHP_EOL;

	  // Open server connection and run transfers.
		$this->sock = fsockopen($this->hostname, $this->Port, $enum, $estr, 30);
		if (!$this->sock) exit('Socket connection error: '.$this->hostname);
		$this->log[] = 'CONNECTION: fsockopen('.$this->hostname.')';
		$this->response('220');
		$this->log_request('EHLO '.$this->local, '250');

		if ($this->SMTPSecure == 'tls') {
			$this->log_request('STARTTLS', '220');
			stream_socket_enable_crypto($this->sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
			$this->log_request('EHLO '.$this->local, '250');
		}

		$this->log_request('AUTH LOGIN', '334');
		$this->log_request($user64, '334');
		$this->log_request($pass64, '235');

		$this->log_request('MAIL FROM: '.$mailfrom, '250');
		foreach ($mailto as $address) {
			$this->log_request('RCPT TO: '.$address, '250');
		}

		$this->log_request('DATA', '354');
		$this->log[] = htmlspecialchars($this->do_headers(false));
		$this->request($this->headers, '250');

		$this->log_request('QUIT', '221');
		fclose($this->sock);

		$this->show_log();

		return true;
	}


  // Log command and do request.
	private function log_request($cmd, $code) {
		$this->log[] = htmlspecialchars($cmd);
		$this->request($cmd, $code);
		return;
	}


  // Send one command and test response.
	private function request($cmd, $code) {
		fwrite($this->sock, $cmd."\r\n");
		$this->response($code);
		return;
	}


  // Read and verify response code.
	private function response($code) {
		stream_set_timeout($this->sock, 8);
		$result = fread($this->sock, 768);
		$meta = stream_get_meta_data($this->sock);
		if ($meta['timed_out'] === true) {
            fclose($this->sock);
            $this->log[] = 'There was a timeout in the Server response.';
            $this->show_log();
            print_r($meta);
            exit();
        }
        $this->log[] = $result;
        if (substr($result, 0, 3) == $code)
            return;
        fclose($this->sock);
        $this->log[] = 'SMTP Server response Error';
        $this->show_log();
        exit();
    }


  // Do create headers after precheck.
	private function do_headers($filedata = true) {
	  // Precheck. Test if we have the necessary data.
		if (empty($this->Username) || empty($this->Password)) {
			exit('We need the username and password for: '. $this->SMTPHost . PHP_EOL);
		}
		if (empty($this->from)) $this->from = [$this->Username, ''];
		if (empty($this->to) || !filter_var($this->to[0][0], FILTER_VALIDATE_EMAIL)) {
			exit('We need a valid email address to send to.' . PHP_EOL);
		}
		if (strlen(trim($this->bodyHTML)) < 3 && strlen(trim($this->bodyPlain)) < 3) {
			exit('There was no message to send.' . PHP_EOL);
		}

	  // Create Headers.
		$headerstring = '';
		$this->create_headers($filedata);
		foreach ($this->ahead as $val) {
			$headerstring .= $val."\r\n";
		}
		return rtrim($headerstring);
	}


  // Headers.
	private function create_headers($filedata) {
	  // Add space between body and attachments.
		if ($this->bodyHTML)  $this->bodyHTML  .= '<br>'.PHP_EOL.'<br>'.PHP_EOL;
		if ($this->bodyPlain) $this->bodyPlain .= PHP_EOL . PHP_EOL;

		$this->ahead   = [];
		$this->ahead[] = 'Date: '.date('r');
		$this->ahead[] = 'To: '.$this->format_address_list($this->to);
		$this->ahead[] = 'From: '.$this->format_address($this->from);
		if (!empty($this->cc)) {
			$this->ahead[] = 'Cc: '.$this->format_address_list($this->cc);
		}
		if (!empty($this->bcc)) {
			$this->ahead[] = 'Bcc: '.$this->format_address_list($this->bcc);
		}
		if (!empty($this->reply_to)) {
			$this->ahead[] = 'Reply-To: '.$this->format_address_list($this->reply_to);
		}
		$this->ahead[] = 'Subject: '.'=?UTF-8?B?'.base64_encode($this->Subject).'?=';
		$this->ahead[] = 'Message-ID: '.$this->generate_message_id();
		$this->ahead[] = 'X-Mailer: '.'PHP/'.phpversion();
		$this->ahead[] = 'MIME-Version: '.'1.0';

		$boundary = md5(uniqid());
	  // Email contents.
		if (empty($this->attachment) || !file_exists($this->attachment[0])) {
			if ($this->bodyPlain && $this->bodyHTML) {
			  // Add multipart.
				$this->ahead[] = 'Content-Type: multipart/alternative; boundary="'.$boundary.'"';
				$this->ahead[] = '';
				$this->ahead[] = 'This is a multi-part message in MIME format.' . PHP_EOL;
				$this->ahead[] = '--'.$boundary;
			  // Add text.
				$this->define_content('plain', 'bodyPlain');
				$this->ahead[] = '--'.$boundary;
			  // Add html.
				$this->define_content('html', 'bodyHTML');
				$this->ahead[] = '--'.$boundary.'--';
			}
			elseif ($this->bodyPlain) {
			  // Add text.
				$this->define_content('plain', 'bodyPlain');
			}
			else {
			  // Add html.
				$this->define_content('html', 'bodyHTML');
			}
		}
		else {
		  // Add multipart with attachment.
			$this->ahead[] = 'Content-Type: multipart/mixed; boundary="' .$boundary.'"';
			$this->ahead[] = '';
			$this->ahead[] = 'This is a multi-part message in MIME format.' . PHP_EOL;
			$this->ahead[] = 'Content-Type: multipart/alternative; boundary="'.'--'.$boundary.'"';
			if ($this->bodyPlain) {
			  // Add text.
				$this->define_content('plain', 'bodyPlain');
				$this->ahead[] = '--'.$boundary;
			}
			if ($this->bodyHTML) {
			  // Add html.
				$this->define_content('html', 'bodyHTML');
				$this->ahead[] = '--'.$boundary;
			}
		  // Loop thru attachments...
			foreach ($this->attachment as $path) {
			  // Add attachment.
				if (file_exists($path)) {
					$this->ahead[] = 'Content-Type: application/'.$this->att_type.'; name="'.basename($path).'"';
					$this->ahead[] = 'Content-Transfer-Encoding: '.$this->att_encoding;
					$this->ahead[] = 'Content-Disposition: attachment; filename="'.basename($path).'"';
					$this->ahead[] = '';
					if ($filedata) {
					  // Encode file contents.
						$contents = chunk_split(base64_encode(file_get_contents($path)));
						$this->ahead[] = $contents;
					}
					$this->ahead[] = '--'.$boundary;
				}
			}
		  // Add last "--".
			$this->ahead[count($this->ahead)-1] .= '--';
		}
	  // Final period.
		$this->ahead[] = '.';

		return;
	}


  // Define and code the contents.
	private function define_content($type, $msg) {
		$this->ahead[] = 'Content-Type: text/'.$type.'; charset="'.$this->charset.'"';
		$this->ahead[] = 'Content-Transfer-Encoding: '.$this->transfer_encoding;
		$this->ahead[] = '';
		if ($this->transfer_encoding == 'quoted-printable') {
			$this->ahead[] = quoted_printable_encode($this->$msg);
		}
		else {
			$this->ahead[] = $this->$msg;
		}
	}


  // Format email address (with name).
	private function format_address($address) {
      return ($address[1] == '') ? $address[0] : '"'.$address[1].'" <'.$address[0].'>';
	}


  // Format email address list.
	private function format_address_list($addresses) {
		$list = '';
		foreach ($addresses as $address) {
			if ($list) {
				$list .= ', '. "\r\n\t";
			}
			$list .= $this->format_address($address);
		}
		return $list;
	}


	private function generate_message_id() {
		return sprintf(
			"<%s.%s@%s>",
			base_convert(time(), 10, 36),
			base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
			$this->local
		);
	}

}

__halt_compiler();

////////////////////////////////////////////////////////////
/// NOTES //////////////////////////////////////////////////
////////////////////////////////////////////////////////////

Methods and properties that are capitalized are user supplier.  Lowercase one's are internal.
