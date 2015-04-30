<?php
/**	
 *  Attention : Modification INTERDIT.
 *
 *	Class Log : Permet de loguer.
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
 *  Le Token sera ajouté au nom du fichier log généré afin de ne pas être connu du visiteur.
 *  Le but est d'empécher la lecture des logs depuis le navigateur si on ne connais pas la clé.
 *  Ceci afin de palier au probléme d'un serveur web NGINX, dont le htaccess n'a pas d'effet.
 * 
 **/
 
class log {
    
	// @string, Dossier de log
	private $path = '/logs/';
  private $token;
			
	// @void, Constructeur, utilise le "timezone" pour définir le nom du fichier de log.
	public function __construct() {
		date_default_timezone_set('Europe/Paris');	
		$this->path = dirname(dirname(__FILE__)) . $this->path;
	}
			
  /**
  *	Création du fichier de log
	*
	* @param string $message : Message à ajouter au log.
	*	@description:
	*	 1. Vérifie si le dossier et le fichier existent.
	*	 2. Vérifie si le fichier de log est déjà créé.
	*	 3. Si il n'existe pas, on crée le fichier.
	*	 4. Si il existe, on va à la méthode "Edition"
  */	
	public function write($message) { 
		$date = new DateTime();
		$this->token = strtolower(_TOKEN_);
		if (!empty($this->token)) $this->token = '-' . substr($this->token,0,8); else  $this->token = "";
		$log = $this->path . $date->format('Y-m-d') . '-log' . $this->token . '.txt'; 
		if(is_dir($this->path)) {
			if(!file_exists($log)) {
				$fh  = fopen($log, 'a+');
				if (is_writable($fh)) { 
					$logcontent = date("[j/m/y H:i:s]") . " : " . $message ."\r\n";
					fwrite($fh, $logcontent);
					fclose($fh);
				}
			} else {
				$this->edit($log, $date, $message);
			}
		} else {
			if(mkdir($this->path,0777) === true)  {
			 $this->write($message);  
			}	
		}
	 }
			
	/** 
	*  Modification du fichier de log.
	*
	* @param string $log
	* @param DateTimeObject $date
	* @param string $message
	*/
	private function edit($log,$date,$message) {
		$logcontent = date("[j/m/y H:i:s]") . " : " . $message ."\r\n";
		$logcontent = $logcontent . file_get_contents($log);
		file_put_contents($log, $logcontent);
  }


}

?>
