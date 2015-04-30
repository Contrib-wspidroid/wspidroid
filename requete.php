<?php
/**	
 *  Attention : Modification INTERDIT.
 *
 *	Projet wspi : WebService pour Domotique sur Raspberry Pi.
 *	
 *	Version	0.1.0
 *	Copyright Aideaunet.
 * 	Site de l'auteur : http://www.aideaunet.com
 *  
 *	Les scripts PHP de ce projet sont sous Copyright, leur modification est INTERDITE.
 *
 *  L'utilisation de l'ensemble des scripts constituant ce Kit sont libre pour les particuliers, 
 *  sous reserve que cela reste dans un domaine privé, et sans modification des scripts.
 *  L'utilisation et l'intégration dans un produit professionnel ou commercial est interdit.
 *  Pour ce dernier cas, vous devez contacter l'auteur via son site Internet.
 *
 *  Le code source est la propriété de son auteur, toute modification est strictement interdite.
 *
 **/
/* Requetes d'accès à la base de données */


// Chargement de la bibliotheque NUSOAP.
require_once('lib/nusoap/nusoap.php');

/* Chargement en dynamique des classes à l'aide d'un autoloader */
require 'classes/autoloader.php';
autoloader::register();

/* Création de l'instance log */
$log= new log;

/* lecture des variables globales */
require('config.inc.php');

/* Mise en cache ou pas du wsdl */
ini_set('soap.wsdl_cache_enabled', $cachewsdl);


/* ***************************************************************** */
class requete {
	
	protected $debug;
	protected $log;
	protected $token;
					
	function __construct() {
		global $debug, $token;
		$this->debug = $debug; 
		$this->token = $token;
		/* Déclaration de la classe LOG */
		$this->log= new log;
	} 
	
	/* 
	* Fonction de test de la clé de sécurité, retourne "9999" si pas valide.
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
/* ***************************************************************** */


/* 
 * Fonction de test pour vérifier si le Web-service répond 
 * $cle = La clé de sécurité du Web-Service
 */
function getHello($cle='') {
	global $debug, $log;
	// Variable de débogage.
	if ($debug == true) $log->write('Lancement de la fonction de test de réponse du Web-Service : requete->getHello()');

	// Vérification de la clé de sécurité, 
	$verif = new requete(); if ($verif->verifcle($cle) != 1) return 9999;
	
	return 'ok';
}

/* Fonction permettant d'executer une requete.
 * $query = La requete à executer.
 * $cle = La clé de sécurité du Web-Service
 */
function	execute($query, $cle='') {
	global $debug, $log;
	
	// Vérification de la clé de sécurité, 
	$verif = new requete(); if ($verif->verifcle($cle) != 1) return 9999;
	
	/* Déclaration de la classe DB */
	$db= new DB;
	/* Execution de la requete */
	$varRetour = json_encode($db->query($query),JSON_UNESCAPED_UNICODE);
	
	/* Retour des valeurs renvoyées par la requete */
	if ($debug == true) $log->write('Retour de la méthode Execute(\''.$query.'\') au format json : '.$varRetour);
	return $varRetour;
}
	

/* ***************************************************************** */

// Lancement du service SOAP.
$server = new soap_server;

// Initialisation de la lecture de l'url en dynamique.
$link = new lien;

// Initialise le support WSDL
$server->configureWSDL('requete', $link->getUrlCourante(false));


/* Définition de la méthode "getHello" pour test réponse du Web-Service */
$server->register('getHello',										// method name
		array('cle' => 'xsd:string'),								// input parameters
        array('varRetour' => 'xsd:string'),			// output parameters
				'wsGetHello',														// namespace (espace de nommage unique)
				'wsGetHello#getHello',									// soapaction (fonction)
				'rpc',																	// style
				'encoded',															// use
				'Test de réponse du Web-Service' 				// documentation
);
/* Définition de la méthode "execute" */
$server->register('execute',										// method name
		array('query' => 'xsd:string',							// input parameters
				'cle' => 'xsd:string'),
        array('varRetour' => 'xsd:string'),			// output parameters
				'wsExecute',														// namespace (espace de nommage unique)
				'wsExecute#Execute',										// soapaction (fonction)
				'rpc',																	// style
				'encoded',															// use
				'Execute une requete '									// documentation
);



if ($debug == true) $log->write($HTTP_RAW_POST_DATA);
$server->service(isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '');

?>
