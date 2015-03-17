<?php/**	 *  Attention : Modification INTERDIT. * *	Projet wspi : WebService pour Domotique sur Raspberry Pi. *	 *	Version	0.1.0 *	Copyright Aideaunet. * 	Site de l'auteur : http://www.aideaunet.com *   *	Les scripts PHP de ce projet sont sous Copyright, leur modification est INTERDITE. * *  L'utilisation de l'ensemble des scripts constituant ce Kit sont libre pour les particuliers,  *  sous reserve que cela reste dans un domaine privé, et sans modification des scripts. *  L'utilisation et l'intégration dans un produit professionnel ou commercial est interdit. *  Pour ce dernier cas, vous devez contacter l'auteur via son site Internet. * *  Le code source est la propriété de son auteur, toute modification est strictement interdite. * **/ // $debug permet de tracer dans un fichier de log à l'emplacement du Web service.$debug = true;// Chargement de la bibliotheque NUSOAP.require_once('./lib/nusoap.php');/* Chargement des classes aditionnelles */require_once('./classes/gpio.class.php');require_once('./classes/1wire.class.php');require_once('./classes/cmd.class.php');require_once('./classes/log.class.php');$log= new log;/* Définition des constantes */include('config.inc.php');define('_TOKEN_', $token);define('_CAPTEUR1WIRE_', json_encode($capteurTemp));define('_NOMGPIO_', json_encode($commandes));define('_NUMPIN_', json_encode($pins));// On désactive la mise en cache du wsdl (pour le test)ini_set('soap.wsdl_cache_enabled', 0);// Fonction qui permet de définir l'URL en dynamique.// *************************************************function getUrlCourante($bShowFileName = true, $bShowQueryString = true, $bShowPort = false) {	global $debug, $log;	// http ou https	if(isset($_SERVER['HTTPS'])) $sUrl = 'https://'; else $sUrl = 'http://'; $sUrl .= $_SERVER['HTTP_HOST'];	if($bShowPort) $sUrl .= ':' . $_SERVER['SERVER_PORT'];	if($bShowFileName) $sUrl .= $_SERVER['SCRIPT_NAME']; else $sUrl .= dirname($_SERVER['SCRIPT_NAME'])."/";	if( ($_SERVER['QUERY_STRING'] != null) && ($bShowQueryString) ) $sUrl .= '?' . $_SERVER['QUERY_STRING'];	// On retourne l'URL.	if ($debug == true) $log->write('Adresse du Web-Service : '.$sUrl);	return $sUrl;} 	 class wspi {		protected $debug;	protected $log;		function __construct() {		global $debug;		$this->debug = $debug; 		/* Déclaration de la classe LOG */		require_once('./classes/log.class.php');		$this->log= new log;	} 		function __destruct() {		if ($this->debug == true) $this->log->write('- - - - - - - - - - - - - - - - - - - -');	}		// Fonction de test de la clé de sécurité, retourne "9999" si pas valide.	// **********************************************************************	function verifcle($cle = '') {		if(strtoupper(_TOKEN_) != strtoupper($cle)) {			if ($this->debug == true) $this->log->write('Erreur : Clé de sécurité non valide ...');			return 9999;		} else {			if ($this->debug == true) $this->log->write('Clé de sécurité vérifiée et valide ...');			return 1;		}	}} // Lancement du service SOAP.$server = new soap_server;// Initialise le support WSDL$server->configureWSDL('wspi', getUrlCourante(false));/* Définition de la méthode "setPin" */$server->register('gpio.setPin', 						// method name		array('pin' => 'xsd:int', 							// input parameters				'valeur' => 'xsd:int', 				'cle' => 'xsd:string'),		array('varRetour' => 'xsd:string'),			// output parameters				'wsSetPin', 												// namespace (espace de nommage unique)				'wsSetPin#setPin', 									// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Active ou desactive un Pin' 				// documentation);/* Définition de la méthode "getPin" */$server->register('gpio.getPin', 						// method name		array('pin' => 'xsd:int', 							// input parameters				'cle' => 'xsd:string'), 		array('varRetour' => 'xsd:string'), 		// output parameters				'wsGetPin', 												// namespace (espace de nommage unique)				'wsGetPin#getPin', 									// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Retourne la valeur du pin.' 				// documentation);/* Définition de la méthode "getMaterielTab" */$server->wsdl->addComplexType(		'Materiel', 'complexType', 'struct', 'all','', 		array(			'nom' => array('name' => 'nom', 'type' => 'xsd:string'),			'pin' => array('name' => 'pin', 'type' => 'xsd:string'),			'etat' => array('name' => 'etat', 'type' => 'xsd:string')		));$server->wsdl->addComplexType(		'MaterielArray', 'complexType', 'array','', 'SOAP-ENC:Array',array(), 		array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Materiel[]')),'tns:Materiel');$server->register('gpio.getMaterielTab', 		// method name		array('cle' => 'xsd:string', 						// input parameters			'litEtat' => 'xsd:int'),		array(			'varRetour' => 'tns:MaterielArray'),	// output parameters			'wsGetMaterielTab', 									// namespace (espace de nommage unique)			'wsGetMaterielTab#getMaterielTab', 		// soapaction (fonction)			'rpc', 																// style			'encoded', 														// use			'Retourne les Matériels actifs sous forme de tableau (array).' 		// documentation);/* Définition de la méthode "getMaterielXml" */$server->register('gpio.getMaterielXml', 		// method name		array('cle' => 'xsd:string', 						// input parameters			'litEtat' => 'xsd:int'),		array('varRetour' => 'xsd:string'), 		// output parameters				'wsGetMaterielXml', 								// namespace (espace de nommage unique)				'wsGetMaterielXml#getMaterielXml',	// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Retourne les Matériels actifs sous forme XML.' 								// documentation);/* Définition de la méthode "setCommande" */$server->register('cmd.setCommande', 				// method name		array('commande' => 'xsd:string', 			// input parameters				'cle' => 'xsd:string'),		array('varRetour' => 'xsd:string'), 		// output parameters				'wssetCommande', 										// namespace (espace de nommage unique)				'wssetCommande#setCommande', 				// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Aucun retour.' 										// documentation);/* Définition de la méthode "setPsutil" */$server->register('cmd.setPsutil', 					// method name		array('commande' => 'xsd:string', 			// input parameters				'cle' => 'xsd:string'),		array('varRetour' => 'xsd:string'), 		// output parameters				'wssetPsutil', 											// namespace (espace de nommage unique)				'wssetPsutil#setPsutil', 						// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Aucun retour.' 										// documentation);/* Définition de la méthode "get1WireXml" */$server->register('wire.get1WireXml', 			// method name		array('cle' => 'xsd:string'), 					// input parameters		array('varRetour' => 'xsd:string'), 		// output parameters				'wsGet1WireXml', 										// namespace (espace de nommage unique)				'wsGet1WireXml#get1WireXml',				// soapaction (fonction)				'rpc', 															// style				'encoded', 													// use				'Retourne les Températures relevées sous forme XML.' 						// documentation);/* Définition de la méthode "get1WireTab" */$server->wsdl->addComplexType(		'Temperature', 'complexType', 'struct', 'all','', 		array(			'nom' => array('name' => 'nom', 'type' => 'xsd:string'),			'valeur' => array('name' => 'valeur', 'type' => 'xsd:string')		));$server->wsdl->addComplexType(		'TemperatureArray', 'complexType', 'array','', 'SOAP-ENC:Array',array(), 		array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Temperature[]')),'tns:Temperature');$server->register('wire.get1WireTab', 			// method name		array('cle' => 'xsd:string'), 					// input parameters		array(			'varRetour' => 'tns:TemperatureArray'),	// output parameters			'wsGet1WireTab', 											// namespace (espace de nommage unique)			'wsGet1WireTab#get1WireTab', 					// soapaction (fonction)			'rpc', 																// style			'encoded', 														// use			'Retourne les Températures relevées sous forme de tableau (array).' // documentation);if ($debug == true) $log->write($HTTP_RAW_POST_DATA);$server->service(isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '');?>