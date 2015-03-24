<?php
/* ************************************************ *
 *   Envoi de commande ou demande d'info Systéme    *
 * ************************************************ */
	
class cmd extends wspi {
	
	/*    Envoi d'une commande Shell au Raspberry       */
	/* ************************************************ */
	function setCommande($commande='', $cle='') {
		// Variables de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : setCommande()');
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Envoi de la commande
		$varRetour = shell_exec("sudo -n $commande");
	
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->log->write('Commande "'.$commande.'" lancée.');
		return $varRetour; 
	}
	
	/*    Envoi d'une commande PsUtil au Raspberry      */
	/* ************************************************ */
	function setPsutil($commande='', $cle='') {
		// Variables de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : setPsutil()');
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Envoi de la commande
		$varRetour = shell_exec('sudo python '.getcwd().'/scripts/cdpsutil.py "'.$commande.'"');

		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Commande "'.$commande.'" lancée.');
			$this->log->write('Valeur retournée : '.str_replace(array("\r\n", "\r", "\n", PHP_EOL, chr(10), chr(13), chr(10).chr(13)),"",$varRetour));
		}
		return $varRetour; 
	}

    /*  Envoi d'une commande Shell RF433 au Raspberry   */
	/* ************************************************ */
	function setRF433dec($commande='', $cle='') {
		// Variables de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : setRF433dec()');
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Envoi de la commande
		$varRetour = shell_exec('sudo '.getcwd().'/lib/433Utils/codesend '.$commande);
	
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->log->write('Commande setRF433dec "'.$commande.'" lancée.');
		return $varRetour; 
	}

}

/* ************************************************ *
 *    FIN de commande ou demande d'info Systéme     *
 * ************************************************ */
?>
