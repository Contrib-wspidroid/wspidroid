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

require('./log.class.php');

class DB {

	/* ********** Les Attributs ********** */

	# @object, Object pour les logs d'exceptions	
	private $log;
	# @object, Connexion object
	protected $connection;
	# @bool ,  Connecté ou pas à la database
	public $isConnected;


	/* ********** Les Fonctions ********** */
	
	/**
	*   Constructeur 
	*/
	public function __construct() { 			
		$this->log = new Log();	
		$this->connect();
	}
		
	/**
	* Connexion à la base de données
	*/
	private function connect() {
		global $CONFIG, $typebase;
		$this->isConnected = true;
		if($typebase == "mysql") {
			try {
				$this->connection = new PDO('mysql:host=' . $CONFIG['db']['host'] . ';port=' . $CONFIG['db']['port'] . ';dbname=' . $CONFIG['db']['dbname'], $CONFIG['db']['login'], $CONFIG['db']['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true));
			} catch(PDOException $e) {
				$this->isConnected = false;
				echo $this->ExceptionLog($e->getMessage());
				die();
			}
		}
	}
	
	private function __destruct() {
	}

	public function deconnect() {
		$this->connection = null;
		$this->isConnected = false;
	}
	
	/**
	* Si la requête SQL contient une instruction SELECT ou SHOW, elle retourne un tableau contenant le résultat.
	* Si l'instruction SQL est un, INSERT, DELETE ou UPDATE, elle renvoie le nombre de lignes affectées.
	*/			
	public function query($query, $fetchmode = PDO::FETCH_ASSOC) {
		if(!$this->isConnected) { $this->connect(); }
		$query = trim($query);
		$rawStatement = explode(" ", $query);
			
		# Which SQL statement is used 
		$statement = strtolower($rawStatement[0]);
			
		if ($statement === 'select' || $statement === 'show') {
			$stmt = $this->connection->prepare($query); 
			$stmt->execute();
			return $stmt->fetchAll($fetchmode);  
		}
		elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
			$stmt = $this->connection->prepare($query); 
			$stmt->execute();
			return $stmt->rowCount();	
		} else {
			return NULL;
		}
	}
		
	/**
	*	Retourne la valeur d'un champs/colonne
	*/
	public function getValue($query) {
		if(!$this->isConnected) { $this->connect(); }
		try { 
			$stmt = $this->connection->prepare($query); 
			$stmt->execute();
			return $stmt->fetchColumn();
		} catch(PDOException $e) {
			echo $this->ExceptionLog($e->getMessage());
			die();
		}
	}
	
	/**
	*	Retourne un tableau à 1 dimension contenant 1 ligne d'enregistrement.
	*/
	public function getRow($query) {
		if(!$this->isConnected) { $this->connect(); }
		try { 
			$stmt = $this->connection->prepare($query.' LIMIT 1'); 
			$stmt->execute();
			return $stmt->fetch();  
		} catch(PDOException $e) {
			echo $this->ExceptionLog($e->getMessage());
			die();
		}
	}

	/**
	*	Retourne un tableau à 2 dimension2 contenant tous les enregistrements demandés.
	*/
	public function getRows($query) {
		if(!$this->isConnected) { $this->connect(); }
		try { 
			$stmt = $this->connection->prepare($query); 
			$stmt->execute();
			return $stmt->fetchAll();       
		} catch(PDOException $e) {
			echo $this->ExceptionLog($e->getMessage());
			die();
		}       
	}


	/**
	*  Retourne le dernier ID inserré
	*/	
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}	

	

	/**	
	* Ecriture des logs
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message , $sql = "") {
		$exception  = 'Exception rencontrée. <br />';
		$exception .= $message;
		$exception .= "<br />";
		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->log->write($message);
		return $exception;
	}			


}


?>
