<?php
include 'config.inc';
?>
<?php
/*
  * CONSTANTS
*/
define("BASEDIR",       "http://localhost/");
define("CSSREF",        "./esercitazione1/css/labsic.css");
define("WP_CACHE_BASE", "cache/");
define("LOGOREF",       "./esercitazione1/img/logo.gif");
define("STARTPAGEREF",  "./esercitazione1/index.php");
define("DEBUG", TRUE);

/*
 * UTILITY FUNCTIONS
*/

function init_cache_dir($WP_CACHE, $inputfile, $digest, $pubkey) {

  if (!is_dir($WP_CACHE)) {
    if (!(@mkdir($WP_CACHE, 0770))) {
      if (DEBUG)
        echo "init_cache_dir: errore durante la creazione della dir: " . $WP_CACHE;
      return FALSE;
    }
  } else {
    if (file_exists($WP_CACHE.$pubkey['name'])) {
	    if (@unlink($WP_CACHE.$pubkey['name']) === FALSE) {
              if (DEBUG)
                echo "init_cache_dir: errore durante l'unlink della chiave pubblica: " . $WP_CACHE.$pubkey['name'];
	      return FALSE;
	    }
	  }
	
	  if (file_exists($WP_CACHE.$digest['name'])) {
	    if (@unlink($WP_CACHE.$digest['name']) === FALSE) {
              if (DEBUG)
                echo "init_cache_dir: errore durante l'unlink del digest: " . $WP_CACHE.$digest['name'];
	      return FALSE;
	    }
	  }
	
    if (file_exists($WP_CACHE.$inputfile['name'])) {
	    if (@unlink($WP_CACHE.$inputfile['name']) === FALSE) {
              if (DEBUG)
                echo "init_cache_dir: errore durante l'unlink del file di input: " . $WP_CACHE.$inputfile['name'];
	      return FALSE;
	    }
	  }
  }                    
 
  if (@move_uploaded_file($pubkey['tmp_name'],$WP_CACHE.$pubkey['name']) === FALSE) {
    if (DEBUG)
      echo "init_cache_dir: errore durante lo spostamento da " . $pubkey['tmp_name'] . " a " . $WP_CACHE.$pubkey['name'];
    return FALSE;
  }
  if (@move_uploaded_file($digest['tmp_name'],$WP_CACHE.$digest['name']) === FALSE) {
    if (DEBUG)
      echo "init_cache_dir: errore durante lo spostamento da " . $digest['tmp_name'] . " a " . $WP_CACHE.$digest['name'];
    return FALSE;
  }
  if (@move_uploaded_file($inputfile['tmp_name'],$WP_CACHE.$inputfile['name']) === FALSE) {
    if (DEBUG)
      echo "init_cache_dir: errore durante lo spostamento da " . $inputfile['tmp_name'] . " a " . $WP_CACHE.$inputfile['name'];
    return FALSE;
  }
  return TRUE;
}

function encodeAttachment($attach, $fileSize) {
  if ($file = @fopen($attach, 'r')) {
    if ($contents = @fread($file, $fileSize)) {
      $encodedAttach = chunk_split(base64_encode($contents));
      if (@fclose($file)) {
        return $encodedAttach;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  } else {
    return FALSE;
  }
}

function createAttachmentMIMEPart($file_type, $file_name, $encoded_attach, $boundary) {
  $part = "--" . $boundary . "\r\n";
  $part .= "Content-Type: " . $file_type . "; name=\"" . $file_name . "\"\r\n";
  $part .= "Content-Transfer-Encoding: base64\r\n";
  $part .= "Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
  $part .= "$encoded_attach";
  
  error_log($part);
  return $part;
}

function build_mail_headers($from, $cc, $bcc, $boundary) {

  $mail_headers = "";

  $mail_headers .= "MIME-Version: 1.0\r\n";
  $mail_headers .= "From: " . $from . "\r\n";
  $mail_headers .= "CC: " . $cc . "\r\n";
  $mail_headers .= "BCC: " . $bcc . "\r\n";
  $mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
  $mail_headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";

  return $mail_headers;

}

function build_mail_body($param_email, $param_nome, $param_cognome, $param_matricola, $chiper_file, $boundary) {
  $mail_content = "Decifratura del messaggio avvenuta con successo.\r\n\r\n";
  $mail_content .= "Se si dovessero incontrare problemi contattare i tutor all'indirizzo\r\n\r\n";
  $mail_content .= "labsicurezza@cs.unibo.it\r\n\r\n";
  $mail_content .= "Copiare i sorgenti di questa mail nel report di consegna.\r\n\r\n";
  $mail_content .= "Saluti";

  

  return $mail_content;
  
}

function write_page_heading($basedir, $cssref, $logoref) {
  echo "<html>\r\n";
  echo "  <head>\r\n";
  echo "    <title>Labratorio Sicurezza</title>\r\n";
  echo "    <base href=\"" . $basedir . "\"></base>";
  echo "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n";
  echo "    <LINK href=\"" . $cssref . "\" rel=\"stylesheet\">\r\n";
  echo "  </head>\r\n";
  echo "  <body>\r\n";
  echo "    <TABLE>\r\n";
  echo "      <TBODY>\r\n";
  echo "        <TR>\r\n";
  echo "          <TD><IMG alt=\"logo Unibo\" src=\"" . $logoref . "\"></TD>\r\n";
  echo "          <TD><SPAN class=titolo>University of Bologna \r\n";
  echo "          <BR>Department of Computer Science<BR>Security Systems</SPAN></TD>\r\n";
  echo "	    </TR>\r\n";
  echo "	  </TBODY>\r\n";
  echo "    </TABLE>\r\n";
}

function write_page_closing() {
  echo "  </body>\r\n";
  echo "</html>\r\n";
}

function write_success_content($studentAddress, $subject, $cssref) {
  echo "  <p>Congratulazioni il messaggio è stato decifrato con successo!</p>\r\n";
  echo "  <p>&Egrave; stata inviata una mail al tuo indirizzo " . $studentAddress . ", ed in 'Cc' anche ai tutor del corso.</p>\r\n";
  echo "  <p>La mail ha il seguente Subject: " . $subject . "</p>\r\n";
  echo "  <p>Se entro breve non ricevi nessuna mail di conferma, contatta i tutor all'indirizzo <a href=\"mailto: labsicurezza@cs.unibo.it\">labsicurezza@cs.unibo.it</a></p>\r\n";
  echo "  <p class=\"warning\">CONDIZIONE NECESSARIA AFFINCH&Egrave; L'ESERCITAZIONE SIA CONSIDERATA SUPERATA, &Egrave; CHE NEL TUO REPORT DI CONSEGNA CI SIA UNA COPIA DELLA MAIL DI CONFERMA COMPRENSIVA DI HEADER.</p>\r\n";
  
}

function write_entire_success_page($studentAddress, $subject, $basedir, $cssref, $logoref) {
  write_page_heading($basedir, $cssref, $logoref);
  write_success_content($studentAddress, $subject, $cssref);
  write_page_closing();
}

function write_failed_verification_content($startpageref) {
  echo "    <p><b>VERIFICA NON RIUSCITA</b></p>\r\n";
  echo "    <p>Assicurati che:</p>\r\n";
  echo "    <ol>\r\n";
  echo "      <li>l'algoritmo di digest usato sia SHA1</li>\r\n";
  echo "      <li>il digest fornito sia effettivamente associato al file PDF spedito</li>\r\n";
  echo "      <li>il digest sia firmato</li>\r\n";
  echo "      <li>la chiave fornita sia una chiave pubblica RSA in formato PEM</li>\r\n";
  echo "    </ol>\r\n";
  echo "    <p>Se non hai effettuato i controlli di cui sopra, <a href=\"" . $startpageref . "\">Torna indietro e prova ancora</a></p>\r\n";
  echo "    <p>Altrimenti, se sei sicuro di aver eseguito tutti i passi correttamente, contatta i tutor all'indirizo <a href=\"mailto:labsicurezza@cs.unibo.it\">labsicurezza@cs.unibo.it</a></p>\r\n";
}

function write_entire_failed_verification_page($basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  write_failed_verification_content($startpageref);
  write_page_closing();
}

function write_internal_error_content($errmsg, $startpageref) {
  echo "  <p><b>Errore di sistema</b></p>\r\n";
  echo "  <p>Non &egrave; stato possibile eseguire la verifica per un errore del sistema: " . $errmsg . "</p>\r\n";
  echo "  <p><a href=\"" . $startpageref . "\">Torna indietro</a> e ripeti l'esercitazione. Se il problema persiste, contattare i tutor del corso all'indirizzo <a href=\"mailto:labsicurezza@cs.unibo.it\">labsicurezza@cs.unibo.it</a>.</p>\r\n";
}

function write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  write_internal_error_content($errmsg, $startpageref);
  write_page_closing();
}

function write_invalid_param_content($param, $startpageref) {
  echo "<p><b>Parametro " . $param . " non valido o assente</b></p>\r\n";
  echo "<p><a href=\"" . $startpageref . "\">Torna indietro</a> e prova ancora.</p>\r\n";
}

function write_entire_invalid_param_page($param, $basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  write_invalid_param_content($param, $startpageref);
  write_page_closing();
}

function write_entire_invalid_captcha_page($captchavalue, $basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  echo "<p><strong>Errore</strong></p>";
  echo "<p>Il valore di controllo $captchavalue non corrisponde al testo visualizzato nell'immagine.</p>";
  echo "<p><a href=\"" . $startpageref . "\">Torna indietro</a> e prova ancora.</p>\r\n";
  write_page_closing();
}

function validate_param_email($email) {
  if (!(isset($email))) {
    return FALSE;
  }
  if (trim($email) === '') {
    return FALSE;
  }

  if(eregi("^[A-Z0-9._%+-]+\.[A-Z0-9._%+-]+@studio.unibo.it$", $email)===FALSE) {
    // il parametro email non e' un indirizzo del dominio studio.unibo.it
    return FALSE;
  }
  return TRUE;
}

function validate_param_fullname($fullname) {
  if (!(isset($fullname))) {
    return FALSE;
  }
  if (trim($fullname) === '') {
    return FALSE;
  }
  return TRUE;
}

function validate_param_number($number) {
  if (!(isset($number))) {
    return FALSE;
  }
  if (strlen($number) < 10) {
    return FALSE;
  }
  // CONTROLLARE CHE CI SIANO SOLAMENTE CIFRE E NON ANCHE ALTRI TIPI DI SIMBOLI
  return TRUE;
}

function validate_param_inputfile($inputfile, $number) {
  if (!(isset($inputfile))) {
      return FALSE;
  }
  if ($inputfile['size'] === 0) {

phpinfo();

return FALSE;
  }
  $file_name = $inputfile['name'];
  if ($file_name != ($number . ".pdf")) {
echo "3";    
return FALSE;
  }
  // CONTROLLARE CHE SIA EFFETTIVAMENTE UN PDF ANDANDO A GUARDARE $inputfile['type']
  return TRUE;
}

function validate_param_digest($digest) {
  if (!(isset($digest))) {
    return FALSE;
  }
  if ($digest['size'] === 0) {
    return FALSE;
  }
  return TRUE;
}

function validate_param_pkey($pkey) {
  if (!(isset($pkey))) {
    return FALSE;
  }
  if ($pkey['size'] === 0) {
    return FALSE;
  }
  return TRUE;
}

function create_content($firstName, $lastName, $id){
	
	include "inc/rain.tpl.class.php"; //include Rain TPL
	raintpl::$tpl_dir = "tpl/"; // template directory
	raintpl::$cache_dir = "tpl/tmp/"; // cache directory
	
	
	$template_vars = array( 	
			'first_name'=>$firstName,
			'last_name'=>$lastName,
			'time'=>microtime()
	);
		
	$tpl = new raintpl();
	$tpl->assign( $template_vars );
	$content = $tpl->draw("content", $return_string = true);
	
	file_put_contents(WP_CACHE_BASE . $id . ".txt", $content);
	
	return $id . ".txt";
}

function validate_parameters($param_email, 
		$param_password, 
		$param_firstname, 
		$param_lastname, 
		$param_matricola,
		$message){
		
	if (!(isset($param_firstname))) {
		return 1;
	}
	if (trim($param_firstname) === '') {
		return 1;
	}
	if (!(isset($param_lastname))) {
		return 2;
	}
	if (trim($param_lastname) === '') {
		return 2;
	}
	if (!(isset($param_password))) {
		return 3;
	}
	if (trim($param_password) === '') {
		return 3;
	}
	if (!filter_var($param_email, FILTER_VALIDATE_EMAIL)) {
		return 4;
	}
	if (!(isset($param_matricola))) {
		return 5;
	}
	if (strlen($param_matricola) < 10) {
		return 5;
	}
	if (!(isset($message))) {
    	return FALSE;
  	}
  	if ($message['size'] === 0) {
    	return FALSE;
  	}
	
	return 0;
}

function process($email, $password, $firstName, $lastName, $matricola, $ecryptedfile){
	if(!file_exists(WP_CACHE_BASE)) mkdir(WP_CACHE_BASE, 0700);
	
	$crypt_folder = WP_CACHE_BASE . "crypt/";
	if(!file_exists($crypt_folder)) mkdir($crypt_folder, 0700);
	
	$crypt_file = $crypt_folder.$ecryptedfile['name'];
	
	move_uploaded_file($ecryptedfile['tmp_name'],$crypt_file);
	
	$decryptedfile = $ecryptedfile['name'] . "_plain.txt";
	$out_file = $crypt_folder . $decryptedfile;
	
	$secret_key = file_get_contents(WP_CACHE_BASE . "secret_key.txt");
	
	$v_command = "openssl aes256 -d -salt -in ". $crypt_file . " -out " . $out_file . " -k " . $secret_key;
	error_log("commnad " . $v_command);
	
	if (exec($v_command,$ret) === FALSE) {
		$errmsg = "errore durante l'esecuzione del comando di cifratura del messaggio";
		write_entire_internal_error_page($errmsg, BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
	}
	else {
		
		$plain_message = md5_file(WP_CACHE_BASE . $matricola . ".txt");
		$plain_message_dechipered = md5_file($out_file);
		
		if($plain_message != $plain_message_dechipered){
			$errmsg = "errore di decifratura";
			write_entire_internal_error_page($errmsg, BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		}
		else{
			require_once "Mail.php";
			
			$studentAddress = trim($email);
			$boundary = md5(uniqid(microtime()));
			$labsicurezzaAddress = "labsicurezza@cs.unibo.it";
			$from = $studentAddress;
			$to = $studentAddress . ", " . $labsicurezzaAddress;
			$subject = "[Laboratorio Sicurezza: Esercitazione 1]: " . trim($firstName) . " " . trim($lastName) . " " . trim($matricola);
			$mail_body = build_mail_body($email, $firstName, $lastName, $matricola, $ecryptedfile, $boundary);
			$tutorAddress = "tutorsicurezza@cs.unibo.it";
			 
			
			$cc = $labsicurezzaAddress;
			$bcc = $tutorAddress;
			 
			if ($mail_body === FALSE) {
				$errmsg = "errore durante la creazione della mail di conferma";
				write_entire_internal_error_page($errmsg, BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
			}
			else{
// 				$host = "pod51002.outlook.com";
			    $host = "smtp.office365.com";
			    
				$port = "587";
				$username = $studentAddress;
				
				$headers = array ('MIME-Version' => "1.0",
						'From' => $from,
						'To' => $to,
						'Cc' => $cc,
						'Bcc' => $bcc,
						'Subject' => $subject,
						'Content-Type' => 'multipart/mixed; boundary=' . $boundary);
					
				$smtp = Mail::factory('smtp',
						array ('host' => $host,
								'port' => $port,
								'auth' => true,
								'username' => $studentAddress,
								'password' => $password));
					
				$mail = $smtp->send($to, $headers, $mail_body);
					
				if (PEAR::isError($mail)) {
					if(DEBUG){
						echo $mail->getMessage();
						echo "<br>";
						echo $studentAddress . " - " . $password;
					}
					$errmsg = "Non &egrave; stato possibile spedire la mail di conferma.";
					write_entire_internal_error_page($errmsg, BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
				}
				else {
					write_entire_success_page($studentAddress, $subject, BASEDIR, CSSREF, LOGOREF);
				}
			}
		}
	}
}

/*
 MAIN CODE
*/

// able output buffering
ob_start();

// ini_set('SMTP', 'smtp.cs.unibo.it');

session_start();

/***  START CAPTCHA ***/
/*
include_once './securimage/securimage.php';

$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false) {

 write_entire_invalid_captcha_page("'".$_POST['captcha_code']."'", BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
 exit();
}
*/
/***  END CAPTCHA  ***/

$cssref = CSSREF; // css relative path
$logoref = LOGOREF; // logo relative path
$startpageref = STARTPAGEREF; // start page relative path
$basedir = BASEDIR; // absolute base dir. Used to construct output web pages


// CONTROLLO DEI PARAMETRI
$param_email = $_POST['email'];
$param_password = $_POST['password'];
$param_firstname = $_POST['first_name'];
$param_lastname = $_POST['last_name'];
$param_matricola = $_POST['matricola'];
$param_pkey = $_FILES['inputfile'];

$parameters_validation = validate_parameters($param_email, $param_password, $param_firstname, $param_lastname, $param_matricola, $param_pkey);
switch ($parameters_validation) {
	case 0:
		process($param_email, $param_password, $param_firstname, $param_lastname, $param_matricola, $param_pkey);
		break;
	case 2:
	case 1:
		write_entire_invalid_param_page('email', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	case 2:
		write_entire_invalid_param_page('password unibo', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	case 3:
		write_entire_invalid_param_page('nome', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	case 4:
		write_entire_invalid_param_page('cognome', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	case 5:
		write_entire_invalid_param_page('numero di matricola', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	case 6:
		write_entire_invalid_param_page('file cifrato', BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
		break;
	
}


// svuota il buffer di uscita
ob_flush();
?>
