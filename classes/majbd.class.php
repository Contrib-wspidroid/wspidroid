<?php
/**
 * Description de majbd
 * Permet de mettre à jour la base de données.
 *
 * @auteur dpaul
 */

/* Chargement en dynamique des classes à l'aide d'un autoloader */
require_once 'autoloader.php';
autoloader::register();

/* lecture des variables globales */
require_once('config.inc.php');

class majbd {
	
	protected $log;
	protected $typebase;
	
	/* Constructeur */
	function __construct() {
		global $debug, $typebase;
		$this->debug = $debug;
		$this->typebase = $typebase;
		
		/* Déclaration de la classe LOG */
		$this->log= new log;
	}

	/* Destructeur */
	function __destruct() {
		// Code destructeur
	}

	/*
	 * Requete de recherche du nom de commande GPIO en fonction de sont N° GPIO
	 * $pin = Numéro du pin GPIO
	 */
	function getNomGPIO($pin) {
		/* Création de la requete */
		$query = 'SELECT eq_nom FROM equipements WHERE eq_code_equip = '.(int)$pin.' AND eq_type_interface_id = 1';
		
		/* Déclaration de la classe DB */
		$db = new DB;
		
		/* Execution de la requete */
		return $db->getValue($query);
	}
	
	/*
	 * Requete de mise à jour des valeurs des équipements
	 * $nom = Nom de l'équipement
	 * $code = Code équipement
	 * $varLu = valeur lu à enregistrer
	 */
	function majValeurEq($code, $varLu) {
		/* Si on utilise pas de base de données, on sort */
		if ($this->typebase == 'none') return true;
						
		/* Convertion des ' en '' */
		$code = preg_replace("/'/", "''", $code);

		/* Création de la requete */
		$query = 'UPDATE equipements SET eq_valeur = \''.$varLu.'\', eq_dern_releve = CURRENT_TIMESTAMP() WHERE eq_code_equip = \''.$code.'\';';
		$query = preg_replace("/(\r\n|\n|\r)/", "", $query);
		
		/* Déclaration de la classe DB */
		$db = new DB;
		
		/* Execution de la requete */
		$varRetour = json_encode($db->query($query),JSON_UNESCAPED_UNICODE);
		
		/* On log le résultat */
		if ($this->debug == TRUE) $this->log->write($varRetour.' trouvé pour la requete : '.$query);
	
		return true;
	}
	
	/*
	 * Création du tableau NomGPIO => Pin GPIO
	 */
	function listGpio() {
		/* Initialisation de la variable */
		$varRetour = [];
		
		/* Création de la requete */
		$query = 'SELECT eq_nom, eq_code_equip FROM equipements WHERE eq_type_interface_id = 1;';
		
		/* Déclaration de la classe DB */
		$db = new DB;
		
		/* Execution de la requete */
		$results = $db->query($query);
		
		/* On crée un tableau au format $nom => $pin */
		foreach ($results as $result) {
			$varRetour[$result['eq_nom']] = $result['eq_code_equip'];
		}
		
		/* On retourne le resultat */
		return $varRetour;
	}
	
	/*
	 * Création du tableau NomWire => code Wire
	 */
	function listWire() {
		/* Initialisation de la variable */
		$varRetour = [];
		
		/* Création de la requete */
		$query = 'SELECT eq_nom, eq_code_equip FROM equipements WHERE eq_type_interface_id = 4;';
		
		/* Déclaration de la classe DB */
		$db = new DB;
		
		/* Execution de la requete */
		$results = $db->query($query);
		
		/* On crée un tableau au format $nom => $pin */
		foreach ($results as $result) {
			$varRetour[$result['eq_nom']] = $result['eq_code_equip'];
		}
		
		/* On retourne le resultat */
		return $varRetour;
	}
	
	/*
	 * Recherche de la correspondance ID 1-wire en fonction de son nom.
	 */
	function getIdWire($nom) {
		/* Création de la requete */
		$query = 'SELECT eq_code_equip FROM equipements WHERE eq_nom = \''.$nom.'\';';
		
		/* Déclaration de la classe DB */
		$db = new DB;
		
		/* Execution de la requete, et retourne la valeur de la requete */
		return $db->getValue($query);
	}
}

?>
