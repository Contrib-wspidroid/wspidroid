<?php

class gpio {
	
	private $debug = true;
	
	public function __construct() {
	}
	
	// Fonction qui permet de loguer.
	// *****************************
	private function addLog($txt) {
		  if (!file_exists("log.txt")) file_put_contents("log.txt", "");
		  file_put_contents("log.txt",date("[j/m/y H:i:s]")." - $txt \r\n".file_get_contents("log.txt"));
	}
	
	/* Change la valeur du pin */
	/* *********************** */
	public function setPin($pin=0, $valeur, $cle='') {
		// Variable de débogage.
		if ($this->debug == true) {
			/* Chargement des classes Logs */
			require_once('./classes/log.class.php');
			$logs = new log();
			$logs->write('Debug en cours : setPin()');
		}

		// Initialisation des variables.
		$varRetour = 0;
	
		// Vérification de la clé de sécurité, 
		if (verifcle($cle) != 1) return 9999;
	
		//Definis le PIN en tant que sortie
		shell_exec('/usr/local/bin/gpio mode '.$pin.' out');
		//Active/désactive le pin
		shell_exec('/usr/local/bin/gpio write '.$pin.' '.$valeur);
		//Lecture de la valeur du pin
		$varRetour = shell_exec('/usr/local/bin/gpio read '.$pin);
		
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->addLog('Valeur de retour : '.$varRetour);
		return $varRetour; 
	}	

	/* Lecture de la valeur du pin */
	/* *************************** */
	public function getPin($pin=0, $cle='') {
		// Variable de débogage.
		global $debug;
		if ($this->debug == true) $this->addLog('Debug en cours : getPin()');
	
		// Initialisation des variables.
		$varRetour = 0;
	
		// Vérification de la clé de sécurité, 
		if (verifcle($cle) != 1) return 9999;
	
		//Lecture de la valeur du pin
		$varRetour = shell_exec("/usr/local/bin/gpio read ".$pin);
		
		/* On retourne le résultat du relevé */
		if ($this->debug == true) $this->addLog('Valeur de retour : '.$varRetour);
		return (int)$varRetour; 
	}	

}

?>
