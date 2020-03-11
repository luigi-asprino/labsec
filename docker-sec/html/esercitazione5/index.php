<?php
/*
Questo script genera l'interfaccia da presentare allo studente 
*/

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
include 'url-constants.php';

?>
<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\r\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laboratorio Sicurezza - Esercitazione 05</title>
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
            <td><span class="titolo">University of Bologna</span><br />Department of Computer Science and Engineering<br />Computer System Security</SPAN></td>
	        </tr>
	      </tbody>
      </table>
    </div>
    <div class="content-wrapper">
      <h1>Esercitazione 5 - Packet filtering</h1>
      <div class="objective-description">
        <h2>Scopo dell'esercitazione</h2>
        <p>Lo scopo di questa esercitazione &egrave; quello di verificare che lo studente sia in grado di configurare un firewall di tipo packet filtering.</p>
      </div>
      <div class="detailed-description">
        <h2>Descrizione</h2>
        <p class="warning">ATTENZIONE. Prima di procedere con la compilazione della form sottostante, leggere attentamente le seguenti istruzioni.</p>

        <ul>
          <li>Lo studente compila la form sottostante riempendo correttamente tutti i campi.</li>
          <li><b>IMPORTANTE:</b> l'indirizzo email e la password devono essere quelle usate per accedere a <tt>studio.unibo.it</tt>.</li>
          <li>In risposta, lo studente riceve all'indirizzo indicato una mail di conferma contenente due indirizzi IP della rete 130.136.4.0</li>
          <li>Lo studente applica sulla propria macchina di <em>Laboratorio Virtuale</em> dei filtri tali per cui
            <ul>
              <li>tutti i pacchetti con protocollo ICMP verso uno degli indirizzi siano scartati</li>
              <li>tutti i pacchetti con protocollo ICMP verso l'altro indirizzo siano lasciati passare</li>
            </ul>
          </li>
          <li>Lo studente avvia il packet sniffer Wireshark.</li>
          <li>Lo studente lancia dalla propria macchina dei paccheti ICMP (es. tramite ping) verso i due indirizzi indicati nella mail di conferma.</li>
        </ul>

        <p>La prova &egrave; considerata superata se nel report finale sono presenti:</p>
        <ul>
          <li>uno screenshot del packet sniffer che mostra i pacchetti ICMP spediti</li>
          <li>i log del firewall che riportano sia i pacchetti scartati che quelli accettati.</li>
          <li>le regole di configurazione del firewall</li>
          <li><em>Screenshot e log devono includere i timestamp</em>.</li>
        </ul>

      <div class="input-form">
        <h2>Form di input</h2>
        <form action="<?=ACTION_REF?>" method="post" enctype="multipart/form-data">
          <fieldset>
            <table>
              <tbody>
              	<tr>
                  <th><p class="input-number">Indirizzo mail (del dominio studio.unibo): </p></th>
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
                
<!--                
<tr>
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
