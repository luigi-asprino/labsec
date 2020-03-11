<?php
/*
Questo script genera l'interfaccia da presentare allo studente 
*/

/*********************************/
/* DEFINIZIONE DELLE COSTANTI */
/*********************************/

// COSTANTI PER URL
include 'url-constants.php';
include 'mail-constants.php';

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"\?>\r\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\r\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laboratorio Sicurezza - Esercitazione 4</title>
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
      	<h1>Esercitazione 4 - One-time-password con S/Key</h1>
      	<div class="objective-description">
        	<h2>Scopo dell'esercitazione</h2>
        	<p>Lo scopo di questa esercitazione &egrave; quello di verificare che lo studente sia in grado di 
        	utilizzare un sistema con autenticazione one-time-password S/Key.</p>
      	</div>
		<div class="detailed-description">
        	<h2>Descrizione</h2>
        	<p class="warning">ATTENZIONE. Prima di procedere con la compilazione della form sottostante, leggere attentamente le 
			seguenti istruzioni.</p>

        	<ul>
        
				<li>Lo studente installa il supporto S/Key sulla sua macchina virtuale che funger&agrave; da server. A lezione sono state spiegate alcune possibili soluzioni.</li>
          
				<li>Lo studente compila la form sottostante riempendo tutti i campi. </li>
				<li><b>IMPORTANTE:</b> l'indirizzo email deve appartenere al dominio <tt>studio.unibo.it</tt>.</li>

				<li>In risposta, lo studente riceve all'indirizzo indicato una mail di
				conferma contenente uno username e una password generati casualmente.
				</li>
          
				<li>Lo studente configura il server per accedere con questi dati usando S/KEY. In particolare:
					<ul>
						<li>Aggiunge l'utente alla lista di coloro che possono accedere al server. 
						<i>Nota:</i> l'account puo' essere cancellato dopo aver svolto l'esercizio, eventualmente off-line.</li>
						<li>Configura il server affinche' questo utente venga autenticato tramite lo schema S/Key.</li>
						<li>Per quell'utente, inizializza il sistema di autenticazione S/Key utilizzando la password ricevuta nella mail di conferma.</li>
					</ul>
				</li>
          
				<li>Lo studente avvia il server e si autentica usando i dati ricevuti via mail. In particolare:
            		<ul>
              			<li>Accede al server con un client testuale, e si logga usando lo username ricevuto nella mail di conferma</li>
              			<li>Ricevera' dal server una challenge</li>
              			<li>Calcola il relativo response (con MD5), usando la password ricevuta nella mail di conferma.</li>
              			<li>Usa il response per autenticarsi</li>
            		</ul>
          		</li>
          
          		<li>La prova si considera superata se nella relazione finale lo studente riporta correttamente:
            		<ul>
              			<li>una copia della mail ricevuta</li>
              			<li>i log del server che dimostrano la riuscita autenticazione S/Key
	                		<ul>
	                	  		<li>relativi allo username ottenuto nella mail di conferma</li>
	                  			<li>comprensiva della challenge fornita dal server (se presente nei log)</li>
                			</ul>
              			</li>
              			<li>Uno screenshot relativo all'autenticazione challenge-response</li>
              			<li>il response usato per accedere al server</li>
            		</ul>
          		</li>
        	</ul>
        
        
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
