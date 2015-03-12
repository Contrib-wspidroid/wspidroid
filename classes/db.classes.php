<?php
/* Classe permettant la connexion et les requetes à la base de données */

/**	
 *  Attention : Modification INTERDIT.
 *
 *	Projet wspi : WebService pour Domotique sur Raspberry Pi.
 *	
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

class DB {

	/* ********** Les Attributs ********** */

	private $connection;
    
    
	/* ********** Les Fonctions ********** */
	
	/* Connexion à la base de données */
	private function __construct() {
		global $CONFIG, $typebase;
		if($typebase == "mysql") {
			try {
				$this->connection = new PDO('mysql:host=' . $CONFIG['db']['host'] . ';port=' . $CONFIG['db']['port'] . ';dbname=' . $CONFIG['db']['dbname'], $CONFIG['db']['login'], $CONFIG['db']['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true));
			} catch (Exception $e) {
				throw new Exception('DB : Paramètres incorrect !');
			}
		}
	}



}

?>
