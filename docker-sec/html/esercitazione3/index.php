<?php
/*
Questo script genera l'interfaccia da presentare allo studente 
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
define("CAPTCHA_REF",    "./esercitazione3/securimage/securimage_show.php");
?>
<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\r\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laboratorio Sicurezza - Esercitazione 3</title>
    <base href="<?=BASE_REF?>" />
		<link type="text/css" rel="stylesheet"  href="<?=CSS_REF?>" />
		<style type="text/css">
          .input-form table th {
            text-align: right;
          }

          .input-form table td {
            text-align: left;
          }

          p.input-submit {
            text-align: center;
          }
		</style>
	</head>
	<body>
    <div class="head-wrapper">
      <table>
        <tbody>
          <tr>
            <td><img alt="logo Unibo" src="<?=LOGO_REF?>" /></td>
            <td><span class="titolo">University of Bologna</span><br />Dipartimento di Scienze dell'Informazione<br />Sicurezza</SPAN></td>
	        </tr>
	      </tbody>
      </table>
    </div>
    <div class="content-wrapper">
      <h1>Esercitazione 3 - GPG</h1>
      <div class="objective-description">
        <h2>Scopo dell'esercitazione</h2>
        <p>Scopo di questa esercitazione &egrave; di verificare che sappiate 
        utilizzare alcune delle funzionalit&agrave; di <b>GPG</b>.</p>
        <p>In particolare, l'esercitazione vuole verificare se siete in grado di leggere un messaggio cifrato e firmato dal tutor del corso.</p>
      </div>
      <div class="detailed-description">
        <h2>Descrizione</h2>
        <p class="warning">ATTENZIONE. Prima di procedere con la compilazione della form sottostante, leggere attentamente le 
        seguenti istruzioni.</p>
        <p>Lo studente deve riempire la form sottostante con i seguenti dati personali:</p>
        <ol>
	  <li><b>Indirizzo e-mail</b>. L'indirizzo deve essere del dominio <tt>studio.unibo.it</tt></li>
          <li><b>Password</b>. &Egrave; la password del proprio account <tt>studio.unibo.it</tt> (serve solo ad inviare la mail di verifica)
          <li><b>Nome</b></li>
          <li><b>Cognome</b></li>
          <li><b>Matricola</b>. A dieci cifre. Inserire eventualmente degli zeri in testa per arrivare a 10 cifre.</li>
          <li><b>public key</b>. Un file contenente la chiave (pubblica) primaria in formato "armor".
          Senza questo file non sar&agrave; possibile spedire nessun messaggio cifrato verso di voi e voi non potete superare la prova.</li>
        </ol>
        <p>Sottomettendo le informazioni specificate, verr&agrave; spedita automaticamente una mail all'indirizzo specificato ed in copia al tutor del corso. Tale mail avr&agrave; in allegato:</p>
        <ul>
          <li>La chiave pubblica primaria del tutor in formato armor.</li>
          <li>Un messaggio firmato con la chiave primaria del tutor e cifrato con la vostra chiave pubblica. Il messaggio viene generato casualmente per ogni studente.</li>
        </ul>
        <p>L'esercitazione &egrave; considerata superata se il report di 
consegna contiene il messaggio decifrato.</p>
      </div>
      <div class="input-form">
        <h2>Form di input</h2>
        <form action="<?=ACTION_REF?>" method="post" enctype="multipart/form-data">
          <fieldset>
            <table>
              <tbody>
              	<tr>
              		<th><p class="input-number">Indirizzo mail (del dominio studio.unibo.it): </p></th>
              		<td><input type="text" name="email" size="50" maxlength="50" value="" /></td>
				</tr>
				<tr>
              		<th><p class="input-number">Password (la passoword utilizzata per accedere all'acount studio.unibo.it): </p></th>
              		<td><input type="password" name="password" size="50" maxlength="50" value="" /></td>
				</tr>
                <tr>
                  <th><p class="input-first-name">Nome: </p></th>
                  <td><input type="text" name="firstname" size="30" value="" /></td>
                </tr>
                <tr>
                  <th><p class="input-last-name">Cognome: </p></th>
                  <td><input type="text" name="lastname" size="50" value="" /></td>
                </tr>
                <tr>
                  <th><p class="input-number">Matricola (10 cifre): </p></th>
                  <td><input type="text" name="number" size="10" maxlength="10" value="" /></td>
                </tr>
                <tr>
                  <th><p class="input-key">Chiave pubblica primaria (formato "armor"): </p></th>
                  <td><input type="file" name="key" size="40" value="" /></td>
                </tr>
<!-- <tr>
  <td><b>Prima di inviare i dati,<br/> inserire il testo visualizzato di fianco:<br/>
    <input type="text" name="captcha_code" size="10" maxlength="6" />
  </td>
  <td>
    <img id="captcha" src="<?=CAPTCHA_REF?>" alt="CAPTCHA Image" />
  </td>
</tr> -->
                <tr>
                  <td colspan="2">
                    <p class="input-submit">
                      <input type="submit" name="submit" value="Invia dati"/><input type="reset" name="reset" value="Azzera campi"/>
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </form>
      </div>
    </div>
	</body>
</html>
