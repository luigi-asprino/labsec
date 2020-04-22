<?php
/*
QUESTO SCRIPT GENERA UNA COPPIA USERNAME-PASSWORD CASUALE CHE VA SPEDITA ALLO STUDENTE ED 
AI TUTOR DEL CORSO. QUESTA COPPIA DOVRA' ESSERE QUELLA UTILIZZATA DALLO STUDENTE 
PER FARE AUTENTICAZIONE SUL PROPRIO SERVER OTP-ENABLED. I TUTOR POSSONO UTILIZZARE 
LE INFORMAZIONI PER EFFETTUARE DEI CONTROLLI.
*/

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
include 'url-constants.php';

// ALTRE COSTANTI
include 'mail-constants.php';


/************************************************************/
/* DEFINIZIONE DELLE FUNZIONI PER VALIDARE I PARAMETRI */
/************************************************************/

function validate_param_firstname($param) {
  if (!(isset($param))) {
    return "nome assente";
  }
  if (trim($param) === '') {
    return "nome vuoto";
  }
  return TRUE;
}

function validate_param_lastname($param) {
  if (!(isset($param))) {
    return "cognome assente";
  }
  if (trim($param) === '') {
    return "cognome vuoto";
  }
  return TRUE;
}

function validate_param_number($param) {
  if (!(isset($param))) {
    return "numero di matricola assente";
  }
  if (strlen($param) < 10) {
    return "numero di matricola inferiore a 10 cifre";
  }
  if (!(ctype_digit($param))) {
    return "numero di matricola composto di caratteri non numerici";
  }
  return TRUE;
}

function validate_param_username($param) {
  if (!(isset($param))) {
    return "username assente";
  }
  if (trim($param) === '') {
    return "username vuoto";
  }
  return TRUE;
}

function validate_param_email($param) {
  if (!(isset($param))) {
    return "email assente";
  }
  if (trim($param) === '') {
    return "email vuota";
  }

  if(!preg_match("/^[A-Z0-9._%+-]+\.[A-Z0-9._%+-]+@studio.unibo.it$/i", $param)) {

    return "Formato email non corretto. La email deve essere del tipo nome.cognome@studio.unibo.it.";
  }

  return TRUE;
}

function validate_param_password($param) {
  if (!(isset($param))) {
    return "password assente";
  }
  if (trim($param) === '') {
    return "password vuota";
  }
  
  return TRUE;
}


/***********************************************************/
/* DEFINIZIONE DI FUNZIONI PER LA SCRITTURA DI PAGINE */
/***********************************************************/

function write_page_heading($base_ref, $css_ref, $logo_ref) {
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
  echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\r\n";
  echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n";
  echo "  <head>\r\n";
  echo "    <title>Labratorio Sicurezza </title>\r\n";
  echo "    <base href=\"" . $base_ref . "\"></base>";
  echo "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n";
  echo "    <LINK href=\"" . $css_ref . "\" rel=\"stylesheet\">\r\n";
  echo "  </head>\r\n";
  echo "  <body>\r\n";
  echo "    <table>\r\n";
  echo "      <tbody>\r\n";
  echo "        <tr>\r\n";
  echo "          <td><img alt=\"logo Unibo\" src=\"" . $logo_ref . "\"></td>\r\n";
  echo "          <td><span class=\"titolo\">University of Bologna \r\n";
  echo "          <br />Department of Computer Science and Engineering<br />Computer System Security</span></td>\r\n";
  echo "	      </tr>\r\n";
  echo "	    </tbody>\r\n";
  echo "    </table>\r\n";
}

function write_page_closing() {
  echo "  </body>\r\n";
  echo "</html>\r\n";
}

function write_invalid_param_page_content($param_name, $msg) {
  echo "<h1>Parametri non valid</h1>\r\n";
  echo "<p>Il parametro " . $param_name . " non &egrave; valido: " . $msg . "</p>\r\n";
}

function write_invalid_param_page($base_ref, $css_ref, $logo_ref, $param_name, $msg) {
  write_page_heading($base_ref, $css_ref, $logo_ref);
  write_invalid_param_page_content($param_name, $msg);
  write_page_closing();
}

function write_internal_error_page_content($msg) {
  echo "<h1>Errore interno</h1>\r\n";
  echo "<p>" . $msg . "Errore interno</p>\r\n";
}

function write_internal_error_page($base_ref, $css_ref, $logo_ref, $msg) {
  write_page_heading($base_ref, $css_ref, $logo_ref);
  write_internal_error_page_content($msg);
  write_page_closing();
}

function write_success_page_content($student_info) {
  echo "<h1>Dati acquisiti</h1>\r\n";
  echo "<p>I dati inseriti sono stati correttamente acquisiti. &Egrave; stata spedita una mail di conferma contenente i dati inseriti all'indirizzo " . $student_info["student_cs_email"] . " ed in copia ai tutor del corso. Tale mail contiene gli indirizzi IP verso cui spedire pacchetti ICMP, una volta impostate le regole del firewall.</p>\r\nSe entro breve non si riceve la mail di conferma, contattare i tutor all'indirizzo <a href=\"mailto:labsicurezza@cs.unibo.it\">labsicurezza@cs.unibo.it</a>.</p>\r\n";
}

function write_entire_invalid_captcha_page($captchavalue, $basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  echo "<p><strong>Errore</strong></p>";
  echo "<p>Il valore di controllo $captchavalue non corrisponde al testo visualizzato nell'immagine.</p>";
  echo "<p><a href=\"" . $startpageref . "\">Torna indietro</a> e prova ancora.</p>\r\n";
  write_page_closing();
}


function write_success_page($base_ref, $css_ref, $logo_ref, $student_info) {
  write_page_heading($base_ref, $css_ref, $logo_ref);
  write_success_page_content($student_info);
  write_page_closing();
}

/************************************************************/
/* DEFINIZIONE DI FUNZIONI PER LA COSTRUZIONE DI MAIL */
/************************************************************/

function encode_attachment($path, $size) {
  if ($file = @fopen($path, 'r')) {
    if ($contents = @fread($file, $size)) {
      $encoded_contents = chunk_split(base64_encode($contents));
      if (@fclose($file)) {
        return $encoded_contents;
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

function build_attachment_mime_part($file_type, $file_name, $encoded_attach, $boundary) {
  $part = "--" . $boundary . "\r\n";
  $part .= "Content-Type: " . $file_type . "; name=\"" . $file_name . "\"\r\n";
  $part .= "Content-Transfer-Encoding: base64\r\n";
  $part .= "Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n\r\n";
  $part .= $encoded_attach;
  return $part;
}

// FUNZIONI PER LA CREAZIONE DELLA MAIL DI CONFERMA

function build_confirmation_mail_subject($student_info) {
  $subject = "";
  $subject .= "[Laboratorio Sicurezza: Esercitazione 5]: Mail di conferma per ";
  $subject .= $student_info['student_lastname'] . " " . $student_info['student_firstname'] . " " . $student_info['student_number'];
  return $subject;
}


function build_confirmation_mail_headers($from, $to, $cc, $subject, $boundary) {

  $headers = array ('MIME-Version' => "1.0",
				   'From' => $from,
				   'To' => $to,
				   'Cc' => $cc,
				   'Subject' => $subject,
				   'X-Mailer' => 'PHP/' . phpversion(),
				   'Content-Type' => 'multipart/mixed; boundary=' . $boundary);

  return $headers;
  
}

function build_confirmation_mail_body($student_info, $good_ip, $bad_ip, $boundary) {

  // COSTRUIAMO IL CONTENUTO DEL MESSAGGIO 
  $mail_content = "I tuoi dati sono stati ricevuti e sono: .\r\n\r\n";
  $mail_content .= "    Nome: " . $student_info['student_firstname'] . "\r\n";
  $mail_content .= "    Cognome: " . $student_info['student_lastname'] . "\r\n";
  $mail_content .= "    Matricola: " . $student_info['student_number'] . "\r\n";
  $mail_content .= "    Username: " . $student_info['student_username'] . "\r\n";
  $mail_content .= "    E-mail CS: " . $student_info['student_cs_email'] . "\r\n\r\n";
  $mail_content .= "Devi quindi impostare il tuo firewall in modo tale che:\r\n";
  $mail_content .= "    tutti i pacchetti ICMP verso " . $good_ip . " siano loggati e lasciati passare.\r\n";
  $mail_content .= "    tutti i pacchetti ICMP verso " . $bad_ip . " siano loggati e scartati.\r\n\r\n";
  $mail_content .= "Una volta configurato il firewall come descritto, lanciare un packet sniffer ed inviare pacchetti ICMP verso i due indirizzi " . $good_ip . " e " . $bad_ip . "\r\n\r\n";
  $mail_content .= "La prova si considera superata se nel report finale sono riportati:\r\n";
  $mail_content .= "    - uno screenshot del packet sniffer che mostri che i pacchetti verso " . $good_ip . " siano stati effetivametne inviati\r\n";
  $mail_content .= "    - i log del firewall che mostrino che i pacchetti ICMP verso " . $good_ip . " siano passati e che quelli verso " . $bad_ip . " siano stati scartati\r\n\r\n";
  $mail_content .= "Sia lo screenshot che i log devono riportare i timestamp.";
  $mail_content .= "\r\n\r\n";
  $mail_content .= "Saluti";
  
  // METTIAMO INSIEME TUTTI I PEZZI
  $mail_body  = "\r\n";
  $mail_body .= "This is a multi-part message in MIME format\r\n";
  $mail_body .= "--" . $boundary . "\r\n";
  $mail_body .= "Content-Type: text/plain; charset=us-ascii\r\n";
  $mail_body .= "Content-Transfer-Encoding: 7bit\r\n";
  $mail_body .= "\r\n" . $mail_content . "\r\n";

  // specifica che non ci sono piu' MIME part
  $mail_body .= "--" . $boundary . "--\r\n";
  
  return $mail_body;
  
}

/**************************************************************/
/* DEFINIZIONE DELLE FUNZIONI PER LA LOGICA DELLO SCRIPT */
/**************************************************************/

// array contenente gli indirizzi IP "buoni"
$good_ip_array = array(
"130.136.4.232",
"130.136.4.243",
"130.136.4.225",
"130.136.4.192",
"130.136.4.240",
"130.136.4.230",
"130.136.4.220",
"130.136.4.192",
"130.136.4.237",
"130.136.4.209"
);

// array contenente gli indirizzi IP "cattivi"
$bad_ip_array = array(
"130.136.4.244",
"130.136.4.207",
"130.136.4.239",
"130.136.4.228",
"130.136.4.227",
"130.136.4.126",
"130.136.4.242",
"130.136.4.208",
"130.136.4.241",
"130.136.4.216",
"130.136.4.215",
"130.136.4.211"
);

//$good_ip_array = array("130.136.4.134", "130.136.4.142", "130.136.4.82", "130.136.4.81", "130.136.4.132", "130.136.4.61", "130.136.4.77", "130.136.4.75", "130.136.4.68", "130.136.4.71", "130.136.4.62", "130.136.4.65", "130.136.4.63", "130.136.4.86", "130.136.4.88", "130.136.4.137", "130.136.4.136", "130.136.4.90", "130.136.4.87", "130.136.4.131", "130.136.4.79", "130.136.4.60");

//$bad_ip_array = array("130.136.4.80", "130.136.4.72", "130.136.4.66", "130.136.4.91", "130.136.4.144", "130.136.4.135", "130.136.4.141", "130.136.4.202", "130.136.4.139", "130.136.4.78", "130.136.4.76", "130.136.4.73", "130.136.4.70", "130.136.4.146", "130.136.4.64", "130.136.4.138", "130.136.4.84", "130.136.4.85");

// sceglie casualmente un elemento dall'array passato come parametro 
function pick_element($a) {
  $len = count($a);
  $i = rand(0, $len - 1);
  return $a[$i];
}

/********************/
/* INIZIO DEL MAIN */
/********************/
ob_start();

session_start();

/***  START CAPTCHA ***/
/*
include_once 'securimage/securimage.php';

$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false) {
 write_entire_invalid_captcha_page("'".$_POST['captcha_code']."'", BASE_REF, CSS_REF, LOGO_REF, START_PAGE_REF);
 exit();
}
*/
/***  END CAPTCHA  ***/


// VALIDO I PARAMETRI
$param_firstname = $_POST['firstname'];
$param_lastname = $_POST['lastname'];
$param_number = $_POST['number'];
//$param_username = $_POST['username'];
$param_email = $_POST['email'];
$param_password = $_POST['password'];


$msg = "";

if (($msg = validate_param_firstname($param_firstname)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "firstname", $msg);
} elseif (($msg = validate_param_lastname($param_lastname)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "lastname", $msg);
} elseif (($msg = validate_param_number($param_number)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "number", $msg);
//} elseif (($msg = validate_param_username($param_username)) !== TRUE) {
//  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "username", $msg);
} elseif (($msg = validate_param_email($param_email)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "email", $msg);
} elseif (($msg = validate_param_password($param_password)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "password", $msg);
} else { // PARAMETRI VALIDATI

  // ORA CREIAMO DAI PARAMETRI DELLE VARIABILI UTILI
  $student_firstname = trim($param_firstname);
  $student_lastname = trim($param_lastname);
  $student_number = $param_number;
  $student_email = trim($param_email);
  $student_username = substr($student_email,0, strpos($student_email, "@"));
  $student_cs_email = $student_email;


  // COLLEZIONIAMO TUTTE LE INFO SULLO STUDENTE IN UN ARRAY
  $student_info = array("student_firstname" => $student_firstname, "student_lastname" => $student_lastname, "student_number" => $student_number, "student_username" => $student_username, "student_cs_email" => $student_cs_email);
  
  /*
    ORA DOBBIAMO SPEDIRE UNA MAIL ALLO STUDENTE (AL SUO INDIRIZZO CS) CONTENENTE I DATI DA LUI INSERITI E CONTENENTE LO USERNAME E LA 
    PASSWORD CHE DOVRA' USARE PER IL SUO SERVER OTP-ENABLED. TALE MAIL DEVE ESSERE INVIATA IN CC A LABSICUREZZA ED IN BCC A TUTORSICUREZZA
     */
     
  // scegliamo l'indirizzo IP "buono"
  $good_ip = pick_element($good_ip_array);

  // scegliamo l'indirizzo IP "cattivo"
  $bad_ip = pick_element($bad_ip_array);
  
  // costruiamo il subject 
  $confirmation_mail_subject = build_confirmation_mail_subject($student_info);
  
  // boundary. usato sia negli header che nel body, Per questo lo dobbiamo mettere fuori dalle funzioni
  $boundary = md5(uniqid(microtime()));
  
  require_once "Mail.php";
  
  $to = $student_cs_email . ", " . LABSICUREZZA_MAIL;
  $from = $student_cs_email;
  $cc   = LABSICUREZZA_MAIL;
  
  $confirmation_mail_headers = build_confirmation_mail_headers($from, $to, $cc, $confirmation_mail_subject, $boundary);
  
  $confirmation_mail_body = build_confirmation_mail_body($student_info, $good_ip, $bad_ip, $boundary);
  if ($confirmation_mail_body === FALSE) {
    $errmsg = "Errore inaspettato durante la costruizione del corpo della mail";
    write_internal_error_page(BASE_REF, CSS_REF, LOGO_REF, $errmsg);
  } else {
  //$host = "pod51002.outlook.com";
      $host = "smtp.office365.com";
	  $port = "587";
	  $username = $from;
	  $password = $param_password;
				 
	  $smtp = Mail::factory('smtp',
							 array ('host' => $host,
							   		'port' => $port,
							   		'auth' => true,
							   		'username' => $from,
							   		'password' => $password)
							 );
				 
	  $mail = $smtp->send($to, $confirmation_mail_headers, $confirmation_mail_body);
  	
  	
	  if (PEAR::isError($mail)) {
	      $errmsg = "Non &egrave; stato possibile spedire la mail di conferma.";
      	  write_internal_error_page(BASE_REF, CSS_REF, LOGO_REF, $errmsg);
	  } 
      else {
          // tutte le operazioni sono state eseguite con successo. Visualizzare una pagina di successo
          write_success_page(BASE_REF, CSS_REF, LOGO_REF, $student_info);
      }
  }
}

// FINISCE QUI IL MAIN
ob_flush();
?>
