<?php
/* *************************************************************** *
 *       Fonctions Publiques appelées par les Web service          *
 ***************************************************************** */
/*                 Contrôle des ports GPIO                         *
 * *************************************************************** */
/* Lecture du matériel à commander (Reponse sous forme de Tableau) */
/* *************************************************************** */
	
class gpio extends wspi {
	

	/* Change la valeur du pin */
	/* *********************** */
	function setPin($pin=0, $valeur, $cle='') {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : setPin()');

		// Initialisation des variables.
		$varRetour = 0;
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		//Definis le PIN en tant que sortie
		shell_exec('/usr/local/bin/gpio mode '.$pin.' out');
		//Active/désactive le pin
		shell_exec('/usr/local/bin/gpio write '.$pin.' '.$valeur);
		//Lecture de la valeur du pin
		$varRetour = shell_exec('/usr/local/bin/gpio read '.$pin);
		
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

	/* ************************************************ *
	 *       FIN de Contrôle des ports GPIO             *
	 * ************************************************ */
	 
}

?>
