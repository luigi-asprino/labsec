<?php
/*
Questo script genera l'interfaccia da presentare allo studente 
*/

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
define("BASE_REF",       "http://labsicurezza.nws.cs.unibo.it/");
define("CSS_REF",        "labsic.css");
define("LOGO_REF",       "logo.gif");
define("START_PAGE_REF", "cgi-bin/esercitazione03/index-test.php");
define("ACTION_REF",     "cgi-bin/esercitazione03/confirmation-mail-test.php");

?>
<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\r\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laboratorio Sicurezza 2008/2009 - Esercitazione 03</title>
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
      <h1>Esercitazione 03 - GPG</h1>
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
          <li><b>Nome</b></li>
          <li><b>Cognome</b></li>
          <li><b>Matricola</b>. A dieci cifre. Inserire eventualmente degli zeri in testa per arrivare a 10 cifre.</li>
          <li><b>Username</b>. Lo username del dominio <tt>cs.unibo.it</tt></li>
          <li><b>public key</b>. Un file contenente la chiave (pubblica) primaria in formato "armor".
          Senza questo file non sar&agrave; possibile spedire nessun messaggio cifrato verso di voi e voi non potete superare la prova.</li>
        </ol>
        <p>Sottomettendo le informazioni specificate, verr&agrave; spedita automaticamente una mail all'indirizzo di 
        dominio <tt>cs.unibo.it</tt> ed in copia al tutor del corso. Tale mail avr&agrave; in allegato:</p>
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
                  <th><p class="input-number">Username (del dominio <tt>cs.unibo.it</tt>): </p></th>
                  <td><input type="text" name="username" size="10" maxlength="20" value="" /></td>
                </tr>
                <tr>
                  <th><p class="input-key">Chiave pubblica primaria (formato "armor"): </p></th>
                  <td><input type="file" name="key" size="40" value="" /></td>
                </tr>
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
