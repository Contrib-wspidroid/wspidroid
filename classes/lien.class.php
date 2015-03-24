<?php
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


class lien {
    
	protected $debug;
	protected $log;
	
	function __construct() {
		global $debug;
		$this->debug = $debug; 
		/* Déclaration de la classe LOG */
		require_once('./classes/log.class.php');
		$this->log= new log;
	} 
    
    
    /* Fonction qui permet de définir l'URL en dynamique.
     **************************************************** */
    function getUrlCourante($bShowFileName = true, $bShowQueryString = true, $bShowPort = false) {
        // http ou https
        if(!empty($_SERVER['HTTPS'])) $sUrl = 'https://'; else $sUrl = 'http://'; 
				$sUrl .= $_SERVER['HTTP_HOST'];

        if($bShowPort) $sUrl .= ':' . $_SERVER['SERVER_PORT'];
        if($bShowFileName) $sUrl .= $_SERVER['SCRIPT_NAME']; else $sUrl .= dirname($_SERVER['SCRIPT_NAME'])."/";
        if( ($_SERVER['QUERY_STRING'] != null) && ($bShowQueryString) ) $sUrl .= '?' . $_SERVER['QUERY_STRING'];

        // On retourne l'URL.
        if ($this->debug == true) $this->log->write('Adresse du Web-Service : '.$sUrl);
        return $sUrl;
    } 	 
    
    
}

?>
