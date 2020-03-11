<?php session_start(); ?>
<?php include 'config.inc'; ?>
<?php echo '<?xml version=\"1.0\" encoding=\"UTF-8\"?>'; ?>
<?php echo '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
'?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
<title>Labratorio Sicurezza - Esercitazione 1</title>
<base href="http://localhost/"></base>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="./esercitazione1/css/labsic.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">

	

</script>
</head>
<body>
<table>
  <tbody>
  <tr>
    <td><img alt="logo Unibo" src="./esercitazione1/img/logo.gif" /></td>
    <td><span class="titolo">University of Bologna 
      <br/>Department of Computer Science<br/>Security Systems</span></td>
	  </tr>
	</tbody>
</table>
<div class="description">
  <h2>Esercitazione 1: crittografia simmetrica</h2>
  <p>Lo scopo di questa esercitazione &egrave; quello di verificare che lo studente sia in grado di
  utilizzare algoritmi di crittografia simmetrica per decifrare e cifrare messaggi.</p>
  <p>L'esercitazione consiste nel decifrare un messaggio crittato utilizzando Triple-DES e cifrare un messaggio
  utilizzando AES-256.</p>
  <p>La procedura è la seguente:</p>
  <ol>
  <li>lo studente crea una chiave segreta <i>key</i> costituita da una stringa di suo piacimento;</li>
  <li>lo studente compila una form (FORM 1) e scambia la chiave con il tutor;
  <li>in risposta lo studente riceve via email dal tutor il messaggio cifrato <i>des3_message</i> utilizzando l'algoritmo Triple-DES e salting con la chiave 
  <i>key</i>;</li>
  <li>lo studente decifra il messaggio <i>des3_message</i> in un messaggio in chiaro <i>plain_message</i>;</li>
  <li>lo studente cifra il messaggio in chiaro <i>plain_message</i> in un messaggio cifrato con AES-256 <i>aes256_message</i> utilizzando la chiave segreta <i>key</i>;</li>
  <li>lo studente invia il messaggio cifrato <i>aes256_message</i> utilizzando una seconda form (FORM 2);</li>
  </ol>
<p>
<span class="warning">ATTENZIONE: assicurarsi che il messaggio venga decifrato con l'algoritmo Triple-DES e cifrato con l'algoritmo AES-256. 

<p>La prima form (FORM 1) richiede le seguenti informazioni:</p>
  <ol>
    <li><b>Email</b>. &Egrave; il tuo indirizzo email di <tt>studio.unibo.it</tt>.</li>
    <li><b>Password</b>. &Egrave; la tua password per accedere all'email di <tt>studio.unibo.it</tt>;</li>
    <li><b>Nome</b>. &Egrave; il tuo nome, e.g., Mario;</li>
    <li><b>Cognome</b>. &Egrave; il tuo cognome, e.g., Rossi;</li>
    <li><b>Numero di matricola</b>. &Egrave; il tuo numero di matricola a 10 cifre. Eventualmente, 
aggiungere in testa tanti zeri quanti necessari per raggiungere le 10 cifre;</li>
    <li><b>Chiave segreta</b>. &Egrave; la chiave segreta generata dallo studente e costituita da una stringa di suo piacimento;</li>
  </ol>
  <p>Una volta sottomesse tutte le informazioni richieste, il sistema procederà a cifrare il messaggio utilizzando la chiave
  segreta fornita e a inviare per email il messaggio cifrato.</p> 
  <p>In caso di esito negativo, il sistema visualizzer&agrave; 
un messaggio di errore e dovrai ripetere la prova.</p>
  <p>In caso di esito positivo, il sistema visualizzer&agrave; 
la seconda form (FORM 2) che richiederà le seguenti informazioni:.</p>
  <ol>
    <li><b>Email</b>. &Egrave; il tuo indirizzo email di <tt>studio.unibo.it</tt>.</li>
    <li><b>Password</b>. &Egrave; la tua password per accedere all'email di <tt>studio.unibo.it</tt>;</li>
    <li><b>Nome</b>. &Egrave; il tuo nome, e.g., Mario;</li>
    <li><b>Cognome</b>. &Egrave; il tuo cognome, e.g., Rossi;</li>
    <li><b>Numero di matricola</b>. &Egrave; il tuo numero di matricola a 10 cifre. Eventualmente, 
aggiungere in testa tanti zeri quanti necessari per raggiungere le 10 cifre;</li>
    <li><b>Messaggio cifrato</b>. &Egrave; il file di testo contenente il messaggio cifrato <i>aes256_message</i> utilizzando l'algoritmo AES-256 e la chiave segreta <i>key_2</i>;</li>
  </ol>
  <p><span class="warning">ATTENZIONE. In caso di problemi, contattare i tutor del laboratorio di 
sicurezza all'indirizzo <a href="mailto:labsicurezza@cs.unibo.it">labsicurezza@cs.unibo.it</a></span></p>
</div>
<hr />
<div class="form">
<form action="./esercitazione1/chiper.php" method="post" enctype="multipart/form-data">
<table>
<tr>
<td>
<span class="email">Indirizzo email (accettato solo quello del dominio studio.unibo.it): </span></td><td><span><input type="text" name="email" 
value="" size="80"/></span>
</td>
</tr>
<tr>
<tr>
<td>
<span class="password">Password (la passoword utilizzata per accedere all'acount studio.unibo.it): </span></td><td><span><input type="password" name="password" 
value="" size="80"/></span>
</td>
</tr>
<tr>
<td><span class="first_name">Nome: </span></td><td><input type="text" name="first_name" value="" size="80" /></td>
</tr>
<tr>
<td><span class="first_name">Cognome: </span></td><td><input type="text" name="last_name" value="" size="80" /></td>
</tr>
<tr>
<td><span class="number">Numero di matricola (10 cifre)</span></td><td><input type="text" name="matricola" value="" maxlength="10" size="80" /></td>
</tr>
<tr>
<td><span class="number">Chiave segreta</span></td><td><input type="password" name="pkey" value="" size="80" /></td>
</tr>
<tr>
<td colspan="2">
<input type="submit" name="submit"/><input type="reset" name="reset"/>
</td>
</tr>
</table>
</form>
<p>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
  </p>
</div>
</body>
</html>
