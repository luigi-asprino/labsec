<?php
include 'config.inc';
?>
<?php
/*
  * CONSTANTS
*/
define("BASEDIR",       "http://labsicurezza.nws.cs.unibo.it/");
define("CSSREF",        "labsic.css");
define("WP_CACHE_BASE", "/home/nws/labsicurezza/cgi-bin/esercitazione01/cache/");
define("LOGOREF",       "logo.gif");
define("STARTPAGEREF",  "cgi-bin/esercitazione01/index.php");
define("DEBUG", FALSE);

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

function build_mail_body($WP_CACHE, $param_username, $param_fullname, $param_number, $param_inputfile, $param_digest, $param_pkey, $boundary) {
  $mail_content = "Verifica del digest eseguita con successo.\r\n\r\n";
  $mail_content .= "In allegato ci sono i file su cui si e' eseguita la verifica:\r\n\r\n";
  $mail_content .= "    " . $param_inputfile['name'] . "\r\n";
  $mail_content .= "    " . $param_pkey['name'] . "\r\n";
  $mail_content .= "    " . $param_digest['name'] . "\r\n\r\n";
  $mail_content .= "Controllare che i file in allegato siano corretti. Se si dovessero riscontrare incongruenze con i file inviati contattare i tutor all'indirizzo\r\n\r\n";
  $mail_content .= "labsicurezza@cs.unibo.it\r\n\r\n";
  $mail_content .= "Copiare i sorgenti di questa mail nel report di consegna\r\n\r\n";
  $mail_content .= "Saluti";

  $attachInputFile = $WP_CACHE . $param_inputfile['name'];
  //$attachInputFile = $param_inputfile['tmp_name'];
  $inputFileName = $param_inputfile['name'];
  $inputFileType = $param_inputfile['type'];
  $inputFileSize = $param_inputfile['size'];
  $encodedInputFile = encodeAttachment($attachInputFile, $inputFileSize);
  if ($encodedInputFile === FALSE) {
    return FALSE;
  }

  $attachPublicKey = $WP_CACHE . $param_pkey['name'];
  //$attachPublicKey = $param_pkey['tmp_name'];
  $publicKeyName = $param_pkey['name'];
  $publicKeyType = $param_pkey['type'];
  $publicKeySize = $param_pkey['size'];
  $encodedPublicKey = encodeAttachment($attachPublicKey, $publicKeySize);
  if ($encodedPublicKey === FALSE) {
    return FALSE;
  }

  $attachDigest = $WP_CACHE . $param_digest['name'];
  //$attachDigest = $param_digest['tmp_name'];
  $digestName = $param_digest['name'];
  $digestType = $param_digest['type'];
  $digestSize = $param_digest['size'];
  $encodedDigest = encodeAttachment($attachDigest, $digestSize);
  if ($encodedDigest === FALSE) {
    return FALSE;
  }

  // MAIL BODY
  // insert the mail content and encode it as US-ASCII
  $mail_body  = "\r\n";
  $mail_body .= "This is a multi-part message in MIME format\r\n";
  $mail_body .= "--" . $boundary . "\r\n";
  $mail_body .= "Content-Type: text/plain; charset=us-ascii\r\n";
  $mail_body .= "Content-Transfer-Encoding: 7bit\r\n";
  $mail_body .= "\r\n" . $mail_content . "\r\n";

  // attach the PDF file
  $mail_body .= createAttachmentMIMEPart($inputFileType, $inputFileName, $encodedInputFile, $boundary);

  // attach the public key
  $mail_body .= createAttachmentMIMEPart($publicKeyType, $publicKeyName, $encodedPublicKey, $boundary);

  // attach the signed SHA1 digest
  $mail_body .= createAttachmentMIMEPart($digestType, $digestName, $encodedDigest, $boundary);

  // specify that there are no more parts
  $mail_body .= "--" . $boundary . "--\r\n";
  
  return $mail_body;
  
}

function write_page_heading($basedir, $cssref, $logoref) {
  echo "<html>\r\n";
  echo "  <head>\r\n";
  echo "    <title>Labratorio Sicurezza</title>\r\n";
  echo "    <base href=\"" . $basedir . "\"></base>";
  echo "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n";
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
  echo "  <p><b>Congratulazioni, il digest &egrave; stato verificato con successo.</b></p>\r\n";
  echo "  <p>&Egrave; stata inviata una mail di conferma al tuo indirizzo " . $studentAddress . ", ed in 'Cc' anche ai tutor del corso.</p>\r\n";
  echo "  <p>La mail ha il seguente Subject: " . $subject . "</p>\r\n";
  echo "  <p>Inoltre, la mail ha in allegato tutti i file da te 'uploadati' ed usati per eseguire la verifica. Controlla che i file siano consistenti con quelli da te forniti.</p>\r\n";
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

function validate_param_username($username) {
  if (!(isset($username))) {
    return FALSE;
  }
  if (trim($username) === '') {
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

/*
 MAIN CODE
*/

// able output buffering
ob_start();

ini_set('SMTP', 'smtp.cs.unibo.it');

session_start();

/***  START CAPTCHA ***/
include_once '../securimage/securimage.php';

$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false) {

 write_entire_invalid_captcha_page("'".$_POST['captcha_code']."'", BASEDIR, CSSREF, LOGOREF, STARTPAGEREF);
 exit();
}
/***  END CAPTCHA  ***/

$cssref = CSSREF; // css relative path
$logoref = LOGOREF; // logo relative path
$startpageref = STARTPAGEREF; // start page relative path
$basedir = BASEDIR; // absolute base dir. Used to construct output web pages


// CONTROLLO DEI PARAMETRI
$param_username = $_POST['username'];
$param_fullname = $_POST['fullname'];
$param_number = $_POST['number'];
$param_pkey = $_FILES['pkey'];
$param_digest = $_FILES['digest'];
$param_inputfile = $_FILES['inputfile'];

if (!(validate_param_username($param_username))) {
  write_entire_invalid_param_page('username', $basedir, $cssref, $logoref, $startpageref);
} elseif (!(validate_param_fullname($param_fullname))) {
  write_entire_invalid_param_page('fullname', $basedir, $cssref, $logoref, $startpageref);
} elseif (!(validate_param_number($param_number))) {
  write_entire_invalid_param_page('number', $basedir, $cssref, $logoref, $startpageref);
} elseif (!(validate_param_inputfile($param_inputfile, $param_number))) {
  write_entire_invalid_param_page('inputfile', $basedir, $cssref, $logoref, $startpageref);
} elseif (!(validate_param_digest($param_digest))) {
  write_entire_invalid_param_page('digest', $basedir, $cssref, $logoref, $startpageref);
} elseif (!(validate_param_pkey($param_pkey))) {
  write_entire_invalid_param_page('pkey', $basedir, $cssref, $logoref, $startpageref);
} else {
  // PARAMETERS VALIDATED. We can now verify the correctness of the posted data 

  $WP_CACHE = WP_CACHE_BASE . session_id() . "/";

  if (init_cache_dir($WP_CACHE, $param_inputfile, $param_digest, $param_pkey) === FALSE) {
    $errmsg = "errore durante l'inizializzazione della directory temporanea";
    write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
  } else {
    // temp dir has been correctly initialized. We can process user data

    /*
     * What we have to do is to:
     * 1. Verify the signed digest, obtaining the digest created by the user (userdigest)
     * 2. Put userdigest into a form understandable by sha1sum 
     * 3. Check userdigest against the input PDF file, using sha1sum. 
     */

    // name of the file that will contain the userdigest
    $userdigest_filename = "userdigest.verified";

    // 1. Verifiy the signed digest. 
    $v_command = "openssl rsautl -verify -in " . $WP_CACHE.$param_digest['name'] . " -out " . $WP_CACHE.$userdigest_filename . " -pubin -inkey " . $WP_CACHE.$param_pkey['name'];

    if (DEBUG)
      echo "Esecuzione del comando: " . $v_command . "\r\n";

    if (@exec($v_command,$ret) === FALSE) {
      $errmsg = "errore durante l'esecuzione del comando di verifica del digest firmato";
      write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
    } else {

      if (DEBUG) 
        echo "comando di verifica della firma eseguito\r\n";

      if (DEBUG)
        echo "prima riga ritornata dal comando: " . $ret[0] . "\r\n";

      if ($ret[0]=="") {
        // signed digest successfully verified.

        if (DEBUG)
          echo "Comando di verifica della firma eseguito con successo\r\n";

        // 2. Adjust userdigest unto a form understandable by sha1sum
        $userdigest_handle = @fopen($WP_CACHE.$userdigest_filename, "r");
        $userdigest = @fread($userdigest_handle, @filesize($WP_CACHE.$userdigest_filename));
        @fclose($WP_CACHE.$userdigest_filename);

        if ($userdigest === FALSE) {
          // error while reading the userdigest
          $errmsg = "Il digest firmato &egrave; stato decifrato, ma si &egrave; verificato un errore durante la lettura del digest.";
          write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
        } else {
          // digest correctly read. Now we have to actually adjust it
          $userdigest = trim($userdigest) . "  " . $WP_CACHE . $param_inputfile['name'];

          if (DEBUG)
            echo "Adjusted userdigest: " . $userdigest;

          // write the new content within userdigest
          $userdigest_handle = @fopen($WP_CACHE.$userdigest_filename, "w+");
          $adjusted = @fwrite($userdigest_handle, $userdigest);
          @fclose($userdigest_handle);
          if ($adjusted === FALSE) {
            // an error occured while writing the new content of the userdigest
            $errmsg = "Errore durante il processing del digest";
            write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
          } else {
            // userdigest successfully adjustd. 

            // 3. Now we can actually check the digest
            $c_command = "sha1sum -c --status " . $WP_CACHE.$userdigest_filename;

            if (DEBUG)
              echo "Comando di check del digest: " . $c_command;

            @exec($c_command, $c_output, $c_ret);
            if (DEBUG)
              echo "comando di check del digest eseguito";

            if (DEBUG)
              echo "Valore di stato ritornato dal comando di check: " . $c_ret;

            if ($c_ret !== 0) {
              // an error occured whie checking the digest
              if (DEBUG)
                echo "errore di sha1sum";
              write_entire_failed_verification_page($basedir, $cssref, $logoref, $startpageref);
            } else {
              // everything OK. We send the email and write the success page

              // MAIL HEADERS
              // boundary for muti-part message
              $boundary = md5(uniqid(microtime()));
              $labsicurezzaAddress = "labsicurezza@cs.unibo.it";
              $tutorAddress = "tutorsicurezza@cs.unibo.it";
              $studentAddress = trim($param_username) . "@cs.unibo.it";
              $to = $studentAddress;
              $from = $labsicurezzaAddress;
              $cc = $labsicurezzaAddress;
              $bcc = $tutorAddress;
              $mail_headers = build_mail_headers($from, $cc, $bcc, $boundary);
		    
              // MAIL SUBJECT
              $mail_subject = "[Laboratorio Sicurezza $AA: Esercitazione 01]: " . trim($param_fullname) . " " . trim($param_number);
		
              // MAIL BODY
              $mail_body = build_mail_body($WP_CACHE, $param_username, $param_fullname, $param_number, $param_inputfile, $param_digest, $param_pkey, $boundary);
              if ($mail_body === FALSE) {
                $errmsg = "errore durante la creazione della mail di conferma";
                write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
              } else {
                // Send
                if (@mail($to, $mail_subject, $mail_body, $mail_headers)) {
                  write_entire_success_page($studentAddress, $mail_subject, $basedir, $cssref, $logoref);
                } else {
                  $errmsg = "Non &egrave; stato possibile spedire la mail di conferma.";
                  write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
                }
              }
            }
          }
        }
      } else {
        write_entire_failed_verification_page($basedir, $cssref, $logoref, $startpageref);
      }
    }
  }
}

// svuota il buffer di uscita
ob_flush();
?>
