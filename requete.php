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


/* Définition des constantes */
include('config.inc.php');
define('_TOKEN_', $token);

/* Mise en cache ou pas du wsdl */
ini_set('soap.wsdl_cache_enabled', $cachewsdl);



/* Liste des Zones */
/* *************** */
/* $action = type de requete : select, update, insert, delete
 * $donnees = au format JSON, les clauses de la condition WHERE
 * si besoin d'une condition ORDER, la mettre en dernier.
 * Exple : {"zone_valide":"1","zone_parent":"1","order by":"zone_position asc"}
 */
function tbZone($action,$donnees,$cle) {
	global $debug, $log;
	/* Déclaration de la classe DB */
	$db= new DB;
	/* En cas de debug */
	if ($debug == true) $log->write('Execution de la méthode : tbZone(\''.$action.'\')');
	
	if($action == "select") {
		/* On génére le début de la requete */
		$req = "select * from zones";
		/* On sélectionne les zones suivant les données */
		$condition = " where "; 
		$fin = "";
		$tbDonnees = json_decode($donnees);
		foreach ($tbDonnees as $key => $tbDonnee) {
			if ($key == "order by") $fin = ' order by ' . $tbDonnee;
			else {
				$req .= $condition . $key . '=' . $tbDonnee;
				$condition = " and ";
			}
		}
		if ($debug == true) $log->write('Requete à executer : ' . $req . $fin);
		$varRetour = json_encode($db->query($req . $fin));
	}
	
	/* Retour des valeurs renvoyées par la requete */
	$varRetour = preg_replace('/\\\\u([\da-fA-F]{4})/', '&#x\1;', $varRetour); // Converti les caractères accentués.
	if ($debug == true) $log->write('Retour de la méthode tbZone(\''.$action.'\') au format json : '.$varRetour);
	return $varRetour;
}


/* Liste des Equipements 
 * Filtre sur : Zone, Type d'interface et Sans filtre
 */

/* Liste des Types d'interface */

/* Liste des Equipements d'une catégorie */


/* ***************************************************************** */

// Lancement du service SOAP.
$server = new soap_server;

// Initialisation de la lecture de l'url en dynamique.
$link = new lien;

// Initialise le support WSDL
$server->configureWSDL('requete', $link->getUrlCourante(false));


/* Définition de la méthode "tbZone" */
$server->register('tbZone',											// method name
		array('action' => 'xsd:string',							// input parameters
				'donnees' => 'xsd:string', 
				'cle' => 'xsd:string'),
        array('varRetour' => 'xsd:string'),			// output parameters
				'wsTbZone',															// namespace (espace de nommage unique)
				'wsTbZone#tbZone',											// soapaction (fonction)
				'rpc',																	// style
				'encoded',															// use
				'Execute une requete sur la table zone'	// documentation
);



if ($debug == true) $log->write($HTTP_RAW_POST_DATA);
$server->service(isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '');

?>
