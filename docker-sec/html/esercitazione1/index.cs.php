<?php
include 'config.inc';
?>
<? echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; ?>
<? echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
"?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
<title>Labratorio Sicurezza <?php echo $AA ?> - Esercitazione 01</title>
<base href="http://labsicurezza.nws.cs.unibo.it/"></base>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="labsic.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">

function checkPost(form)
	{

	}	

</script>
</head>
<body>
<table>
  <tbody>
  <tr>
    <td><img alt="logo Unibo" src="logo.gif" /></td>
    <td><span class="titolo">University of Bologna 
      <br/>Department of Computer Science<br/>Security Systems</span></td>
	  </tr>
	</tbody>
</table>
<div class="description">
  <h2>Esercitazione 01: chiavi private/pubbliche e verifica dell'integrit&agrave;</h2>
  <p>Lo scopo di questa esercitazione &egrave; quello di verificare che sei in grado di 
creare e firmare correttamente il digest di un file, utilizzando una coppia di chiavi RSA.</p>
  <p class="warning">LEGGERE QUANTO SEGUE PRIMA DI RIEMPIRE LA FORM SOTTOSTANTE</p>
  <p>L'esercitazione consiste nel creare una una coppia di chiavi RSA 
pubblica/privata di 1024 bit e in formato PEM (se l'hai gi&agrave; puoi usare quella).</p>
  <p>Inoltre, devi creare un file PDF di nome <em><tt>&lt;tuo numero di matricola&gt;.pdf</tt></em> e 
contenente le seguenti informazioni:</p>
  <ol>
    <li>Nome</li>
    <li>Cognome</li>
    <li>Numero di matricola</li>
    <!--<li>Codice fiscale</li>-->
    <li>Indirizzo mail del dominio <tt>cs.unibo.it</tt></li>
  </ol>
  <p><span class="warning">ATTENZIONE: &egrave; importante che il file sia un PDF e non un semplice file di testo</span></p>
  <p>Infine, devi creare un digest SHA1	del file PDF sopra descritto, e quindi firmare il digest utilizzando la chiave privata RSA 
che hai creato.</p>
<p>
<span class="warning">ATTENZIONE: assicurarsi che il digest sia calcolato con l'algoritmo SHA1. Inoltre, utilizzando
utility come openssl o sha1sum per generare il digest si ottiene in output una sequenza di caratteri della forma
<em>SHA1(0000123456.pdf)= 314af4a687d351c1cec228d1c487cfaab0fc3ee6</em>. Quello che va firmato &egrave; un file di testo (il cui nome non &egrave; 
rilevante) che contiene solo il digest vero e proprio (<em>314af4a687d351c1cec228d1c487cfaab0fc3ee6</em>).</span>
</p>
  <p>Se hai gi&agrave; eseguito queste operazioni, sei in grado di riempire la form sottostante. Le informazioni 
richieste dalla form sono:</p>
  <ol>
    <li><b>Username</b>. &Egrave; il tuo username per il dominio <tt>cs.unibo.it</tt>.</li>
    <li><b>Nome completo</b>. &Egrave; il tuo nome completo, e.g., Mario Rossi</li>
    <li><b>Numero di matricola</b>. &Egrave; il tuo numero di matricola a 10 cifre. Eventualmente, 
aggiungere in testa tanti zeri quanti necessari per raggiungere le 10 cifre.</li>
    <li><b>File originale</b>. &Egrave; il file PDF contenente i tuoi dati e descritto precedentemente.</li>
    <li><b>Digest firmato</b>. &Egrave; il digest SHA1 del file PDF, firmato con la tua chiave privata RSA.</li> 
    <li><b>Chiave pubblica</b>. &Egrave; la tua chiave pubblica RSA in formato PEM. <span class="warning">ATTENZIONE, assicurarsi 
che la chiave specificata sia quella associata alla chiave privata usata per firmare il digest e che sia in formato PEM.</span></li>
  </ol>
  <p>Una volta sottomesse tutte le informazioni richieste, il sistema verificher&agrave; la correttezza 
della firma e del digest. In caso positivo, il sistema visualizzer&agrave; un messaggio di verifica 
effettuata con successo, e visualizzer&agrave; ulteriori istruzioni che sei tenuto a seguire per 
poter considerare l'esercitazione superata. In caso di esito negativo, il sistema visualizzer&agrave; 
un messaggio di errore e dovrai ripetere la prova.</p>
  <p><span class="warning">ATTENZIONE. In caso di problemi, contattare i tutor del laboratorio di 
sicurezza all'indirizzo <a href="mailto:labsicurezza@cs.unibo.it">labsicurezza@cs.unibo.it</a></span></p>
</div>
<hr />
<div class="form">
<form action="cgi-bin/esercitazione01/verify.php" method="post" enctype="multipart/form-data">
<table>
<tr>
<td>
<span class="username">Username su <tt>cs.unibo.it</tt>: </span></td><td><span><input type="text" name="username" 
value="" size="40"/></span>
</td>
</tr>
<tr>
<td><span class="fullname">Inserisci il nome completo: </span></td><td><input type="text" name="fullname" value="" size="80" /></td>
</tr>
<tr>
<td><span class="number">Inserisci il numero di matricola (10 cifre)</span></td><td><input type="text" name="number" value="" maxlength="10" size="10" /></td>
</tr>
<tr>
<td>
<span class="file">Upload del file PDF: </span></td><td><input type="file" name="inputfile" 
size="40"/>
</td>
</tr>
<tr>
<td>
<span class="digest">Upload del digest SHA1 firmato: </span></td><td><input type="file" name="digest" 
size="40"/>
</td>
</tr>
<tr>
<td>
<span>Upload della tua chiave pubblica RSA (formato PEM):</span></td><td>
<input type="file" name="pkey" size="40"/>
</td>
</tr>
                <tr>
<td><b>Prima di inviare i dati,<br/> inserire il testo visualizzato di fianco:<br/>
                <input type="text" name="captcha_code" size="10" maxlength="6" />
</td>
<td>
<img id="captcha" src="<?=CAPTCHA_REF?>" alt="CAPTCHA Image" />
</td>
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
