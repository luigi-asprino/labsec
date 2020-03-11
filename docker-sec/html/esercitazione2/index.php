<?php
/*
Questo script genera l'interfaccia da presentare allo studente 
*/

include 'config.inc';

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
define("BASE_REF",       "http://localhost/");
define("CSS_REF",        "./esercitazione2/css/labsic.css");
define("LOGO_REF",       "./esercitazione2/img/logo.gif");
define("START_PAGE_REF", "./esercitazione2/index.php");

/*
 * INIZIO SCRITTURA DEL DOCUMENTO
 */

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laboratorio Sicurezza - Esercitazione 2</title>
    <base href="<?=BASE_REF?>" />
		<link type="text/css" rel="stylesheet"  href="<?php echo CSS_REF ?>" />
	</head>
	<body>
    <div class="head-wrapper">
      <table>
        <tbody>
          <tr>
            <td><img alt="logo Unibo" src="<?php echo LOGO_REF ?>" /></td>
            <td><span class="titolo">University of Bologna</span><br />Dipartimento di Informatica - Scienze e Ingegneria<br />Sicurezza</SPAN></td>
	        </tr>
	      </tbody>
      </table>
    </div>
    <div class="content-wrapper">
      <h1>Esercitazione 2 - Certificati</h1>
      
      <div class="objective-description">
      
        <h2>Scopo dell'esercitazione</h2>
        
        <p>Scopo di questa esercitazione &egrave; di verificare che lo studente sappia 
        utilizzare alcune delle funzionalit&agrave; dei certificati.</p>
        
        <p>In particolare, l'esercitazione vuole verificare se lo studente &egrave; in grado di creare un 
        certificato e di scambiare mail cifrate e firmate.</p>
        
      </div>
      
      <div class="detailed-description">
      
        <h2>Descrizione</h2>
        
        <p class="warning">ATTENZIONE. Leggere attentamente le seguenti istruzioni.</p>
        
        <ul>
        
          <li>Lo studente crea il proprio certificato su 
          <a href="http://www.instantssl.com/ssl-certificate-products/free-email-certificate.html">Comodo's Free Email certificates</a> 
          e lo importa sul proprio client di posta.
            <ul>
              <li>Il certificato deve essere associato all'indirizzo di posta del dominio <tt>studio.unibo.it</tt>.</li>
              <li>&Egrave; sufficiente un certificato di tipo <i>Freemail</i>.</li>
            </ul>
          </li>
        
          <li>Lo studente invia quindi una mail firmata con il proprio certificato (ma non cifrata) al tutor del corso: Andrea Nuzzolese 
          (<tt>andrea.nuzzolese2@unibo.it</tt>).
            <ul>
              <li>Il subject della mail deve essere 
              <tt>[Laboratorio Sicurezza: Esercitazione 2]: Mail firmata da COGNOME NOME MATRICOLA</tt>, 
              dove <tt>COGNOME</tt>, <tt>NOME</tt> e <tt>MATRICOLA</tt> sono rispettivamente il cognome, 
              il nome ed il numero di matricola dello studente.</li>
            
              <li>Se lo studente utilizza un subject diverso, la prova non &egrave; considerata superata.</li>
            </ul>
          </li>
        
          <li>Entro i 10 giorni successivi alla data in cui ha ricevuto la mail firmata, il tutor invia allo studente 
          una mail firmata con il proprio certificato 
          <a href="http://www.instantssl.com/ssl-certificate-products/free-email-certificate.html">Comodo's Free Email certificates</a>
          e cifrata per lo studente. 
            <ul>
              <li>Il contenuto di questa mail cambia da studente a studente.</li>
            
              <li>La mail inviata dal tutor &egrave; in risposta alla mail firmata dallo studente. Dunque, 
              il subject contiene (ma non &egrave; detto che sia identico a) il subject della mail 
              firmata inviata dallo studente (vedi sopra).</li>
            </ul>
          </li>
          
          <li>Lo studente deve quindi essere in grado di decifrare la mail ricevuta dal tutor.</li>
          
          <li>Lo studente supera la prova se nel report di consegna finale indica il messaggio in chiaro spedito dal tutor.</li>
          
          <li>L'uso del Laborotorio Virtuale &egrave; <b>obbligatorio</b> per lo svolgimento dell'esercitazione. Il pacchetto da scaricare è <i>babaoglu-security-lab2</i>.<br/> Prima di installare il pacchetto occorre aggiornare gli indici APT sulla vostra macchina virtuale con i comandi:
                <ul>
                    <li>sudo apt-get update</li>
                    <li>sudo update-apt-xapian-index</li>
                </ul>
          </li>
        </ul>

      </div>

    </div>
	</body>
</html>
