<?php
/* *************************************************************** *
 *       Fonctions Publiques appelées par les Web service          *
 ***************************************************************** */
/*                 Contrôle des ports GPIO                         *
 * *************************************************************** */


class gpio extends wspi {
	

	/* Change la valeur du pin */
	/* *********************** */
	function setPin($pin=0, $valeur, $cle='') {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : setPin()');

		// Initialisation des variables.
		$varRetour = 0;
		
		/* charge la class d'enregistrement des données lues dans la base de données */
		$req = new majbd();
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		//Definis le PIN en tant que sortie
		shell_exec('/usr/local/bin/gpio mode '.$pin.' out');
		//Active/désactive le pin
		shell_exec('/usr/local/bin/gpio write '.$pin.' '.$valeur);
		//Lecture de la valeur du pin
		$varRetour = shell_exec('/usr/local/bin/gpio read '.$pin);
		
		/* On met à jour les données lues dans la base de données */
		if ((int)$varRetour == 1) $valEtat = 'on'; else $valEtat = 'off';
		$commande = $req->getNomGPIO($pin);
		if ($this->debug == true) { $this->log->write('Demande de nom GPIO correspondant au GPIO N° : '.$pin);
			$this->log->write('Nom de Commande retourné : '.$commande);
		}
		$req->majValeurEq($pin, $valEtat);
			
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->log->write('Valeur de retour : '.(int)$varRetour);
		return (int)$varRetour; 
	}	

	/* Lecture de la valeur du pin */
	/* *************************** */
	function getPin($pin=0, $cle='') {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : getPin()');

		// Initialisation des variables.
		$varRetour = 0;
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		//Lecture de la valeur du pin
		$varRetour = shell_exec("/usr/local/bin/gpio read ".$pin);
		
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->log->write('Valeur de retour : '.(int)$varRetour);
		return (int)$varRetour; 
	}	
	
	/* Lecture du matériel à commander (Réponse sous format Tableau) */
	/* ************************************************************* */
	function getMaterielTab($cle='',$litEtat=0) {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : getMaterielTab()');
	
		/* charge la class d'enregistrement des données lues dans la base de données */
		$req = new majbd();
		
		// Initialisation des variables.
		$varRetour = array();
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Lecture du matériel déclaré et correspondance Pin physique/WiringPi.
		$commandes = $req->listGpio();
		$pins = json_decode(_NUMPIN_,true);

		foreach($commandes as $commande=>$pin) {
			if($litEtat == 1) {
				//Lecture de la valeur du pin
				$etat = shell_exec("/usr/local/bin/gpio read ".(_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin));
			} else $etat = 0;
			$varRetour[] = array( 'nom' => $commande, 'pin' => (_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin), 'etat' => $etat);
			
			/* On met à jour les données lues dans la base de données */
			if ($etat == 1) $valEtat = 'on'; else $valEtat = 'off';
			$req->majValeurEq((_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin), $valEtat);
		}
	
		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Tableau retourné au format JSON = '.json_encode($varRetour));
			$this->log->write('Fin de l\'execution de getMaterielTab()');
		}
		return $varRetour; 	
	}

	/* Lecture du matériel à commander (Réponse sous format XML) */
	/* ********************************************************* */
	function getMaterielXml($cle='',$litEtat=0) {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : getMaterielXml()');
		// Initialisation des variables.
		$varRetour = "";
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		/* charge la class d'enregistrement des données lues dans la base de données */
		$req = new majbd();
		
		// Lecture du matériel déclaré et correspondance Pin physique/WiringPi.
		$commandes = $req->listGpio();
		$pins = json_decode(_NUMPIN_,true);
	
		$j = 0;
		$varRetour = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><materielResponse><detailResultat>";
		foreach($commandes as $commande=>$pin) {
			++$j;
			if($litEtat == 1) {
				//Lecture de la valeur du pin
				$etat = shell_exec("/usr/local/bin/gpio read ".(_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin));
			} else $etat = 0;
			$varRetour .= "<materiel att=".'"'.$j.'"'.">";
			$varRetour .= "<nom>".$commande."</nom>" ;
			$varRetour .= "<pin>".(_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin)."</pin>" ;
			$varRetour .= "<etat>".(int)$etat."</etat></materiel>" ;
			/* On enregistre la valeur dans la base de données */
			if ($etat == 1) $valEtat = 'on'; else $valEtat = 'off';
			$req->majValeurEq((_TYPEPIN_=='P' ? (int)$pins[$pin] : (int)$pin), $valEtat);
		}
		$varRetour .= "</detailResultat></materielResponse>";
	
		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Chaine XML = '.$varRetour);
			$this->log->write('Fin de l\'execution de getMaterielXml()');
		}
		return utf8_decode($varRetour); 		
	}

}

/* ************************************************ *
 *       FIN de Contrôle des ports GPIO             *
 * ************************************************ */
?>
