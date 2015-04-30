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
			$this->log->write('Commande "'.$commande.'" exécutée.');
			$this->log->write('Valeur retournée : '.str_replace(array("\r\n", "\r", "\n", PHP_EOL, chr(10), chr(13), chr(10).chr(13)),"",$varRetour));
		}
		return $varRetour; 
	}

  /**
	 * Envoi d'une commande Shell RF433 au Raspberry 
	 * *********************************************
	 * @param type string $commande		= Code décimal de la Commande à envoyer
	 * @param type string $cle				= Clé de sécurité
	 * @param type string $equipement = Facultatif. Si utilisation de la base de donnée cliente, renseigner ici le "eq_code_equip" de l'équipement.
	 * @return type string						= Chaine retourné par la commande (exemple de retour : "sending code[1310740]")
	 */
	function setRF433dec($commande='', $cle='', $equipement = '') {
		/* Variables de débogage */
		if ($this->debug == true) $this->log->write('Debug en cours : setRF433dec()');
	
		/* Vérification de la clé de sécurité */
		if ($this->verifcle($cle) != 1) return 9999;
	
		/* Envoi de la commande */
		$varRetour = shell_exec('sudo '.getcwd().'/lib/433Utils/codesend '.$commande);
		
		/* Convertion de la commande en Hexa afin de vérifier si Cmd "on" (termine par 15) ou "Off" (termine par 14) */
		$hexaequip = base_convert($commande, 10, 16);
		if (substr($hexaequip,-2) == '15') $val = 'on'; else $val = 'off';
			
		/* On vérifie si on doit mettre à jour l'a base de données'équipement */
		if ($equipement != '') {
			/* Mise à jour des données */
			$this->req->majValeurEq($equipement, $val);
		}
	
		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Commande setRF433dec "sudo '.getcwd().'/lib/433Utils/codesend '.$commande.'" executée.');
			$this->log->write('Valeur retournée : '.str_replace(array("\r\n", "\r", "\n", PHP_EOL, chr(10), chr(13), chr(10).chr(13)),"",$varRetour));
		}
		
		/* On retourne le résultat */
		return $varRetour.' = '.$val; 
	}

}

/* ************************************************ *
 *    FIN de commande ou demande d'info Systéme     *
 * ************************************************ */
?>
