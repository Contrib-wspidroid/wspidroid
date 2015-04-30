<?php
/* ************************ */
/* Actuellement pas utilisé */
/* ************************ */

/**
 * Class de vérification de validité de la clé.
 *
 * @auteur dpaul
 */
class cle {
	
	protected $token;
	protected $debug;
	
	/* Constructeur */
	function __construct() {
		global $debug, $token;
		$this->debug = $debug; 
		$this->token = $token;
		/* Déclaration de la classe LOG */
		$this->log= new log;
	}

	/* Destructeur */
	function __destruct() {
		// Code destructeur
	}

	/* 
	 * Fonction de test de la clé de sécurité, retourne "9999" si pas valide.
	 * **********************************************************************
	 */
	function verifcle($cle = '') {
		if(strtoupper($this->token) != strtoupper($cle)) {
			if ($this->debug == true) $this->log->write('Erreur : Clé de sécurité non valide ...');
			return 9999;
		} else {
			if ($this->debug == true) $this->log->write('Clé de sécurité vérifiée et valide ...');
			return 1;
		}
	}
}

?>
