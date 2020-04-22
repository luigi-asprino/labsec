<?php
/*
Questo script genera un messaggio casuale, lo firma con la chiave primaria del tutor e lo 
cifra con la chiave pubblica dello studente. Quindi spedisce il messaggio cifrato e 
firmato all'indirizzo dello studente ed in copia al tutor. 
*/

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
define("BASE_REF",       "http://localhost/");
define("CSS_REF",        "./esercitazione3/css/labsic.css");
define("LOGO_REF",       "./esercitazione3/img/logo.gif");
define("START_PAGE_REF", "./esercitazione3/index.php");
define("ACTION_REF",     "./esercitazione3/confirmation-mail.php");

// ALTRE COSTANTI
define("LABSICUREZZA_MAIL", "labsicurezza@cs.unibo.it");
define("TUTORSICUREZZA_MAIL", "tutorsicurezza@cs.unibo.it");
define("TUTOR_KEY_PATH",    "/var/www/html/esercitazione3/data/tutorpubkey.asc");
define("TUTOR_KEY_NAME", "tutorpubkey.asc");
define("TUTOR_KEY_SIZE", 1734);
define("IMPORT_KEY_SCRIPT", "/var/www/html/esercitazione3/gpg-import-key.sh");
define("DELETE_KEY_SCRIPT", "/var/www/html/esercitazione3/gpg-delete-key.sh");
define("SE_SCRIPT", "/var/www/html/esercitazione3/gpg-se.sh");
define("LIST_KEYS_SCRIPT", "/var/www/html/esercitazione3/gpg-list-keys.sh");

define("WP_CACHE_BASE", "/var/www/html/esercitazione3/cache");

define("DEBUG", FALSE);


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
  if (strcasecmp(trim($param), "labsicurezza") === 0)
    return "username non valido";
  return TRUE;
}

function validate_param_email($param) {
  if (!(isset($param))) {
    return "email assente";
  }
  if (trim($param) === '') {
    return "email vuota";
  }

  if(!preg_match("/[A-Z0-9._%+-]+\.[A-Z0-9._%+-]+@studio.unibo.it/i", $param)) {
	return "Formato email non corretto. La email deve essere del tipo nome.cognome@studio.unibo.it. <br/> $param";
  }

  return TRUE;
}

function validate_param_key($param) {
  if (!(isset($param))) {
    return "chiave primaria assente";
  }
  if ($param['size'] === 0) {
    return "chaive primaria vuota";
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
  echo "    <title>Labratorio Sicurezza 2013/2014</title>\r\n";
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
  echo "          <br />Department of Computer Science<br />Security Systems</span></td>\r\n";
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

function write_error_page_content($msg) {
  echo "<h1>Errore</h1>\r\n";
  echo $msg;
}

function write_error_page($base_ref, $css_ref, $logo_ref, $msg) {
  write_page_heading($base_ref, $css_ref, $logo_ref);
  write_error_page_content($msg);
  write_page_closing();
}

function write_entire_invalid_captcha_page($captchavalue, $basedir, $cssref, $logoref, $startpageref) {
  write_page_heading($basedir, $cssref, $logoref);
  echo "<p><strong>Errore</strong></p>";
  echo "<p>Il valore di controllo $captchavalue non corrisponde al testo visualizzato nell'immagine.</p>";
  echo "<p><a href=\"" . $startpageref . "\">Torna indietro</a> e prova ancora.</p>\r\n";
  write_page_closing();
}


function write_success_page_content($student_info) {
  echo "<h1>Messaggio firmato e cifrato spedito</h1>\r\n";
  echo "<p>&Egrave; stato spedito un messaggio all'indirizzo " . $student_info['student_cs_email'] . " con la chiave pubblica primaria del tutor e con il messaggio da decifrare</p>\r\n";
  echo "<p>La prova si considera superata se nel report di consegna finale vengono riportati gli header della mail ed il messaggio decifrato.</p>\r\n";
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

function build_confirmation_mail_subject($firstname, $lastname, $number) {
  $subject = "";
  $subject .= "[Laboratorio Sicurezza: Esercitazione 3]: Messaggio firmato e cifrato per ";
  $subject .= $lastname . " " . $firstname . " " . $number;
  return $subject;
}

function build_confirmation_mail_headers($from, $to, $subject, $boundary) {

	$headers = array ('MIME-Version' => "1.0",
				   'From' => $from,
				   'To' => $to,
				   'Subject' => $subject,
				   'X-Mailer' => 'PHP/' . phpversion(),
				   'Content-Type' => 'multipart/mixed; boundary=' . $boundary);
	
  

  	return $headers;
}

function build_confirmation_mail_body($student_info, $boundary) {

  // COSTRUIAMO IL CONTENUTO DEL MESSAGGIO 
  $mail_content = "I tuoi dati sono stati ricevuti e sono: .\r\n\r\n";
  $mail_content .= "    Nome: " . $student_info['student_firstname'] . "\r\n";
  $mail_content .= "    Cognome: " . $student_info['student_lastname'] . "\r\n";
  $mail_content .= "    Matricola: " . $student_info['student_number'] . "\r\n";
  $mail_content .= "    Username: " . $student_info['student_username'] . "\r\n";
  $mail_content .= "    E-mail CS: " . $student_info['student_cs_email'] . "\r\n";
  $mail_content .= "    Public key: " . "in allegato" . "\r\n\r\n";
  $mail_content .= "La chiave pubblica primaria del tutor si trova in allegato \r\n\r\n";
  $mail_content .= "Il messaggio firmato e cifrato si trova in allegato. \r\n\r\n";
  $mail_content .= "Saluti";
  
  // COSTRUIAMO L'ALLEGATO PER LA CHIAVE DELLO STUDENTE
  $student_key_path = $student_info['student_key']['tmp_name'];
  $student_key_name = "student-key.asc";
  $student_key_type = $student_info['student_key']['type'];
  $student_key_size = $student_info['student_key']['size'];
  
  $encoded_student_key = encode_attachment($student_key_path, $student_key_size);
  if ($encoded_student_key === FALSE) {
    return FALSE;
  }
  
  // COSTRUIRAMO L'ALLEGATO PER LA CHIAVE DEL TUTOR
  $tutor_key_path = TUTOR_KEY_PATH;
  $tutor_key_name = TUTOR_KEY_NAME;
  $tutor_key_size = TUTOR_KEY_SIZE;
  $tutor_key_type = "application/octet-stream";
  
  $encoded_tutor_key = encode_attachment($tutor_key_path, $tutor_key_size);
  if ($encoded_tutor_key === FALSE) {
    return FALSE;
  }

  // COSTRUIAMO L'ALLEGATO PER LA IL MESSGGIO FIRMATO E CIFRATO
  $str_msg_path = $student_info['student_se_msg'];
  $str_msg_name = substr(strrchr($str_msg_path, "/"), 1);
  $str_msg_type = "application/octet-stream";
  $int_msg_size = @filesize($str_msg_path);
  if ($int_msg_size === FALSE) {
    if (DEBUG)
      echo "Errore durante la lettura della grandezza del messaggio firmato e cifrato";
    return FALSE;
  }

  $encoded_msg = encode_attachment($str_msg_path, $int_msg_size);
  if ($encoded_msg === FALSE) {
    if (DEBUG)
      echo "Errore durante l'encoding del messaggio firmato e cifrato";
    return FALSE;
  }
  
  
  // METTIAMO INSIEME TUTTI I PEZZI
  $mail_body  = "\r\n";
  $mail_body .= "This is a multi-part message in MIME format\r\n";
  $mail_body .= "--" . $boundary . "\r\n";
  $mail_body .= "Content-Type: text/plain; charset=us-ascii\r\n";
  $mail_body .= "Content-Transfer-Encoding: 7bit\r\n";
  $mail_body .= "\r\n" . $mail_content . "\r\n";

  // allega la chiave dello student
  $mail_body .= build_attachment_mime_part($student_key_type, $student_key_name, $encoded_student_key, $boundary);

  // allega la chiave del tutor
  $mail_body .= build_attachment_mime_part($tutor_key_type, $tutor_key_name, $encoded_tutor_key, $boundary);

  // allega il messaggio firmato e cifrato
  $mail_body .= build_attachment_mime_part($str_msg_type, $str_msg_name, $encoded_msg, $boundary);

  // specifica che non ci sono piu' MIME part
  $mail_body .= "--" . $boundary . "--\r\n";
  
  return $mail_body;
  
}

// FUNZIONI PER LA CREAZIONE DELLA MAIL DI VERIFICA

function build_verification_mail_headers($from, $to, $subject) {

	
	$headers = array ('MIME-Version' => "1.0",
				   'From' => $from,
				   'To' => $to,
				   'X-Mailer' => 'PHP/' . phpversion(),
	               'Subject' => $subject,
				   'Content-Type' => 'text/plain; charset=us-ascii',
				   'Content-Transfer-Encoding' => '7bit');
	
  

  	return $headers;
  
}

function build_verification_mail_subject($student_info) {
  $subject = "";
  $subject .= "[Laboratorio Sicurezza: Esercitazione 3]: Mail di verifica per ";
  $subject .= $student_info['student_lastname'] . " " . $student_info['student_firstname'] . " " . $student_info['student_number'];
  
  return $subject;
}

function build_verification_mail_body($student_info, $random_msg) {
  $mail_content = $random_msg;
  
  $mail_body = "";
  $mail_body .= "\r\n" . $mail_content . "\r\n";
  
  return $mail_body;
}

/******************************************************************/
/* DEFINIZIONE DELLE FUNZIONI PER LA GESTIONE DEI FILE TEMPORANEI */
/******************************************************************/

function prepare_temp_dir($str_base, $str_folder) {
  if (file_exists($str_base) === FALSE) {
        if (DEBUG) {
            echo "<br/>" .$str_base . "<br/>non esiste!<br/>";
        }
          
    if (@mkdir($str_base) === FALSE) {
      return FALSE;
    }
  }

  chmod($str_base, 0777);
      
  $str_path = $str_base . "/" . $str_folder;

  if (DEBUG)
    echo "Directory temporanea di lavoro: " . $str_path;

  // se la directory temporanea esiste gia', la cancelliamo
  if (file_exists($str_path) === TRUE) {
    unlinkRecursive($str_path, TRUE);
  }

  // ora creiamo la directory temporanea di lavoro

  if (@mkdir($str_path) === FALSE) {
    return FALSE;
  }

  chmod($str_path, 0777);
  return $str_path;

}

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 */
function unlinkRecursive($dir, $deleteRootToo)
{
    if(!$dh = @opendir($dir))
    {
        return;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        {
            continue;
        }

        if (!@unlink($dir . '/' . $obj))
        {
            unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);
   
    if ($deleteRootToo)
    {
        @rmdir($dir);
    }
   
    return;
} 


/**************************************************************/
/* DEFINIZIONE DELLE FUNZIONI PER LA LOGICA DELLO SCRIPT */
/**************************************************************/

// ASSIGN A TUTOR TO THE INPUT STUDENT
function get_tutor_info_by_student($student_info) {
  return array("tutor_name" => PAOLO_NAME, "tutor_email" => PAOLO_EMAIL, "tutor_key_path" => PAOLO_KEY_PATH, "tutor_key_size" => PAOLO_KEY_SIZE, "tutor_key_type" => PAOLO_KEY_TYPE);
}

// GENERATE A RANDOM MESSAGE TO BE SIGNED AND CRYPTED FOR THE STUDENT
function generate_random_message() {
  // generiamo un numero casuale tra 20 e 30 che rappresenta la lunghezza del messaggio 
  $msg_length = rand(20, 30);
  
  // creiamo un carattere ASCII casuale per ogni carattere del messaggio
  $msg = "";
  for ($i = 0; $i < $msg_length; $i++) {
    $c = chr(rand(65, 90)); // sono le lettere maiscule ASCII
    $msg .= $c;
  }
  
  return $msg;
}

// importa da file una chiave pubblica
function import_student_key($student_info, $array_output) {
  $str_cmd = IMPORT_KEY_SCRIPT . " " . $student_info["student_key"]["tmp_name"];
  $int_status = 0;
  if (DEBUG)
    echo "<br/><b>Comando:</b> " . $str_cmd."<br/>";
  $str_cmd_ret = exec($str_cmd, $array_output, $int_status);
  if (DEBUG) {
    echo "Status: " . $int_status . "\r\n";
    echo "Last line: " . $str_cmd_ret . "\r\n";
    print_r($array_output);
  }
  return ($int_status === 0);
}

// firma e cifra un messaggio. Ritorna un booleano che indica se e' stato 
// possibile firmare e cifrare il messaggio. Nel caso di errore, l'array 
// array_output viene riempito con dei messaggi che dovrebbero dare qualche feedback. 
function sign_encrypt_message($student_info, $array_output) {
  if (DEBUG) {
    echo "valore di student_info dentro la sign_encrypt_message\r\n";
    print_r($student_info);
  }
  $str_cmd = SE_SCRIPT . " " . $student_info['student_cs_email'] . " " . $student_info['student_msg'] . " ".  $student_info['student_se_msg'];
  if (DEBUG)
    echo "<br/>Comando per firmare e cifrare: <br/>" . $str_cmd . "<br/>";
  $int_status = 0;
  @exec($str_cmd, $array_output, $int_status);
  if (DEBUG){
    echo "Risultato: comando: " . $int_status;
    print_r($array_output);
  } 
 return ($int_status === 0);
}

function delete_key($str_keyid) {
  $str_cmd = DELETE_KEY_SCRIPT . " " . $str_keyid;
  if (DEBUG)
    echo "Comando per cancellare la chiave: " . $str_cmd;
  $array_output = array();
  $int_status = 0;
  @exec($str_cmd, $array_output, $int_status);
  return ($int_status === 0);
}

function chmod_r($dir, $perm) {
  if($objs = glob($dir."/*")) {        
    foreach($objs as $obj) {
      chmod($obj, $perm);
      if(is_dir($obj)) 
        chmod_r($obj, $perm);
    }
  }

  return chmod($dir, $perm);
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

// Setto i permessi per la cartella data
chmod_r("/var/www/html/esercitazione3/data", 0777);


// VALIDO I PARAMETRI
$param_firstname = $_POST['firstname'];
$param_lastname = $_POST['lastname'];
$param_number = $_POST['number'];
$param_username = $_POST['username'];
$param_password = $_POST['password'];
$param_email = $_POST['email'];
$param_key = $_FILES['key'];


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
} elseif (($msg = validate_param_key($param_key)) !== TRUE) {
  write_invalid_param_page(BASE_REF, CSS_REF, LOGO_REF, "key", $msg);
} else { // PARAMETRI VALIDATI

  // ORA CREIAMO DAI PARAMETRI DELLE VARIABILI UTILI
  $student_firstname = trim($param_firstname);
  $student_lastname = trim($param_lastname);
  $student_number = $param_number;
  $student_email = trim($param_email);

  $student_username = substr($student_email,0, strpos($student_email, "@"));
  $student_key = $param_key;
  $student_cs_email = $student_email;
  
  // COLLEZIONIAMO TUTTE LE INFO SULLO STUDENTE IN UN ARRAY
  $student_info = array("student_firstname" => $student_firstname, "student_lastname" => $student_lastname, "student_number" => $student_number, "student_username" => $student_username, "student_cs_email" => $student_cs_email,  "student_key" => $student_key, "student_msg" => "", "student_se_msg" => "");

  if (DEBUG) {
    echo "Informazioni sullo studente: \r\n";
    print_r($student_info);
  }

  if (DEBUG) {
    $str_user = shell_exec("whoami");
    echo "user: " . $str_user;
  }

  // CREIAMO LA DIRECTROY TEMPORANEA DI LAVORO
  $session_id = session_id();
  if (DEBUG)
    echo "Session id: " . $session_id;
   
  $str_tmp_dir = prepare_temp_dir(WP_CACHE_BASE, $session_id);
  if ($str_tmp_dir === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore durante l'inizializzazione della directory temporanea di lavoro</p>");
    exit;
  }

  // COSTRUIAMO IL MESSAGGIO CASUALE DA FIRMARE E CIFRARE 
  $random_msg = generate_random_message();
  if (DEBUG)
    echo "Mssaggio random: " . $random_msg;

  // SALVIAMO IL MESSAGGIO CASUALE DA FIRMARE NELLA DIRECTORY TEMPORANE
  // creaiamo il file
  $str_random_msg_file = $str_tmp_dir . "/" . $student_info['student_username'] . ".txt";
  if (DEBUG)
    echo "File con msg random: " . $str_random_msg_file;
  $resource_random_msg_handle = @fopen($str_random_msg_file, "w+");
  if ($resource_random_msg_handle === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore durante la creazione del file contenente il messaggio random</p>");
    exit;
  }
  // chiudiamo il file prima di settare i permessi
  if (@fclose($resource_random_msg_handle) === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore durante la creazione del file con il msg random</p>");
    exit;
  }
  // settiamo i permessi in modo che  il file non sia leggibile da tutti
  if (@chmod($str_random_msg_file, 0777) === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore mentre si stavano settando i permessi per il file contenente il msg random</p>");
    exit;
  }
  // scriviamo il contenuto del file 
  $resource_random_msg_handle = @fopen($str_random_msg_file, "w+");
  if ($resource_random_msg_handle === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore durante la scrittura del file con il msg random</p>");
    exit;
  }
  // Commento magic_quotes per php7
  //$bool_orig_value = get_magic_quotes_runtime();
  //set_magic_quotes_runtime(FALSE);
  if (@fwrite($resource_random_msg_handle, $random_msg) === FALSE) {
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, "<p>Errore durante la scrittura del file con il msg random</p>");
    @fclose($resource_random_msg_handle);
    exit;
  }
  //set_magic_quotes_runtime($bool_orig_value);
  @fclose($resource_random_msg_handle);

  $student_info['student_msg'] = $str_random_msg_file;
  $student_info['student_se_msg'] = $str_random_msg_file . ".signed.encrypted";


  // IMPORTIAMO LA CHIAVE PUBBLICA DELLO STUDENTE
  $array_cmd_output = array();
  if (import_student_key($student_info, $array_cmd_output) === FALSE) {
    $str_msg = "<p>Errore durante l'importazione della chiave pubblica dello studente:<br/>";
    for ($i=0; $i<count($array_cmd_output); $i++) {
      $str_msg = $str_msg . $array_cmd_output[$i] . "<br />";
    }
    $str_msg = $str_msg . "</p>";
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, $str_msg);
    if (DEBUG)
      print_r($array_cmd_output);
    exit;
  }
  unset($array_cmd_output);

  // FIRMIAMO E CIFRIAMO IL MESSAGGIO

  chmod("/var/www/html/esercitazione3/data/gnupg/secring.gpg", 0777);

  if (sign_encrypt_message($student_info, $array_cmd_output) === FALSE) {
    $str_msg = "<p>Errore durante la firma e cifratura del messaggio:<br/>";
    for ($i=0; $i<count($array_cmd_output); $i++) {
      $str_msg = $str_msg . $array_cmd_output[$i] . "<br />";
    }
    $str_msg = $str_msg . "</p>";
    write_error_page(BASE_REF, CSS_REF, LOGO_REF, $str_msg);
    exit;
  }

  
  require_once "Mail.php";
  
  // ORA SPEDIAMO LA MAIL AL TUTOR CON IL MESSAGGIO IN CHIARO
  
  $from = $student_info['student_cs_email'];
  $to = LABSICUREZZA_MAIL;
  $bcc = TUTORSICUREZZA_MAIL;
      
  $verification_mail_subject = build_verification_mail_subject($student_info);
  $verification_mail_headers = build_verification_mail_headers($from, $to, $verification_mail_subject);
  $verification_mail_body = build_verification_mail_body($student_info, $random_msg);
      
  
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
							   'password' => $password));
				 
	$mail = $smtp->send($to, $verification_mail_headers, $verification_mail_body);
			
	if (PEAR::isError($mail)) {
		if(DEBUG){
			echo $mail->getMessage();
		}
		$errmsg = "Si &egrave; verificato un errore interno inatteso, durante l'invio della mail di verifica al tutor. Per favore, ripetere l'esercitazione";
    	write_internal_error_page(BASE_REF, CSS_REF, LOGO_REF, $errmsg);
    	exit;
	} 

  // ORA SPEDIAMO LA MAIL ALLO STUDENTE METTENDO IN COPIA IL TUTOR
  
  
  	$from = $student_info['student_cs_email'];
  	$to = $from . ", " . LABSICUREZZA_MAIL;

	$boundary = md5(time());
	$mail_subject = build_confirmation_mail_subject($student_info['student_firstname'], $student_info['student_lastname'], $student_info['student_number']);
	$mail_headers = build_confirmation_mail_headers($from, $to, $mail_subject, $boundary);
    $mail_body = build_confirmation_mail_body($student_info, $boundary);
             
	if ($mail_body === FALSE) {
		$errmsg = "errore durante la creazione della mail di conferma";
			write_entire_internal_error_page($errmsg, $basedir, $cssref, $logoref, $startpageref);
	} 
	else{
		$host = "pod51002.outlook.com";
		$port = "587";
		$username = $from;
		$password = $param_password;
				 
		$smtp = Mail::factory('smtp',
			array ('host' => $host,
				   'port' => $port,
				   'auth' => true,
				   'username' => $from,
				   'password' => $password));
				 
		$mail = $smtp->send($to, $mail_headers, $mail_body);
			
		if (PEAR::isError($mail)) {
			if(DEBUG){
				echo $mail->getMessage();
			}
			$errmsg = "Non &egrave; stato possibile spedire la mail di conferma.";
			write_internal_error_page(BASE_REF, CSS_REF, LOGO_REF, $errmsg);
		} 
		else {
			
			// cancelliamo i file temporanei
  			unlinkRecursive($str_tmp_dir, TRUE);

  			// cancelliamo la chiave dello studente dal db
  			delete_key($student_info['student_cs_email']);
 		 	// tutte le operazioni sono state eseguite con successo. Visualizzare una pagina in cui si scrive che entro breve verra' spedita una mail da parte dei tutor
			write_success_page(BASE_REF, CSS_REF, LOGO_REF, $student_info);
		}
	}
   
  
}

// FINISCE QUI IL MAIN
ob_flush();
?>
