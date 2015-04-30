<?php
/* ************************************************ *
 *       Début de Contrôle du Bus 1-Wire            *
 * ************************************************ */
/*  Lecture du nom et de la valeur des capteurs     *
 *      Reponse sous forme de Tableau ou XML        */
/* ************************************************ */
	
class wire extends wspi {
	
	protected $fichier;
	
	/* 
	 * Lecture du Bus 1-Wire (Réponse sous format Tableau) 
	 * *************************************************** */
	function get1WireTab($cle='') {
		global $fichier;
		
		/* Variable de débogage */
		if ($this->debug == true) $this->log->write('Debug en cours : get1WireTab()');
	
		/* Initialisation des variables */
		$varRetour = array();
	
		/* Vérification de la clé de sécurité */
		if ($this->verifcle($cle) != 1) return 9999;
	
		/* Lecture du matériel déclaré */
		$capteurTemp = $this->req->listWire();
	
		/* Lecture du nom des capteurs que l'on stock dans un tableau */
		$varLu = exec('sudo modprobe w1-gpio & $ sudo modprobe w1-therm');
		if($dossier = opendir('/sys/bus/w1/devices/')) { 			// Si le dossier existe, on l'ouvre.
			while(false !== ($fichier = readdir($dossier))) { 	// Si la lecture du dossier ne retourne pas d'erreur, on boucle.
				if(substr($fichier,0,3) == '28-') { 							// Seul les fichiers commençant par '28-' sont des capteurs.
					/* On lit chacun des capteurs */
					$varLu = exec('find /sys/bus/w1/devices/ -name "'.$fichier.'" -exec cat {}/w1_slave \; | grep "t=" | awk -F "t=" \'{print $2/1000}\'');
					/* On filtre sur le capteur lu */
					$elementFiltre = array_filter($capteurTemp, function($var){ global $fichier; return $var == $fichier; });
					foreach ($elementFiltre as $key=>$valeur) {
						$nom = (isset($key) ? $key : $fichier);
					}
					$varRetour[] = array( 'nom' => $nom, 'code' => $fichier, 'valeur' => $varLu);
				
					/* On met à jour les données lues dans la base de données */
					$this->req->majValeurEq($fichier, $varLu);
				}
			}
		}

		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Tableau retourné au format JSON = '.json_encode($varRetour));
			$this->log->write('Fin de l\'execution de get1WireTab()');
		}
		return $varRetour; 	
	}
	
	/* 
	 * Lecture du Bus 1-Wire (Réponse sous format XML) 
	 * *********************************************** */
	function get1WireXml($cle='') {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : get1WireXml()');
			
		// Initialisation des variables.
		$varRetour = array();
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Lecture du matériel déclaré.
		$capteurTemp = json_decode(_CAPTEUR1WIRE_,true);
	
		// Lecture du nom des capteurs que l'on stock dans un tableau.
		$varLu = exec('sudo modprobe w1-gpio & $ sudo modprobe w1-therm');
		if($dossier = opendir('/sys/bus/w1/devices/')) { 			// Si le dossier existe, on l'ouvre.
			$varRetour = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><temperatureResponse><detailResultat>";
			$j = 0;
			
			/* On boucle sur les lectures */
			while(false !== ($fichier = readdir($dossier))) { 	// Si la lecture du dossier ne retourne pas d'erreur, on boucle.
				if(substr($fichier,0,3) == '28-') { 							// Seul les fichiers commençant par '28-' sont des capteurs.
					// On lit chacun des capteurs.
					++$j;
					$varRetour .= "<temperature att=".'"'.$j.'"'.">";
					$nom = (isset($capteurTemp[$fichier])?$capteurTemp[$fichier]:$fichier);
					$varRetour .= "<nom>".$nom."</nom>";
					$varRetour .= "<code>".$fichier."</code>";
					$varLu = exec('find /sys/bus/w1/devices/ -name "'.$fichier.'" -exec cat {}/w1_slave \; | grep "t=" | awk -F "t=" \'{print $2/1000}\'');
					$varRetour .= "<valeur>".$varLu."</valeur>";
					$varRetour .= "</temperature>";
					
					/* On enregistre la valeur dans la base de données */
					$this->req->majValeurEq($fichier, $varLu);
				}
			}
			$varRetour .= "</detailResultat></temperatureResponse>";
		}

		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Chaine XML = '.$varRetour);
			$this->log->write('Fin de l\'execution de get1WireXml()');
		}
		return $varRetour; 	
	}

	/* 
	 * Lecture d'un élément du Bus 1-Wire 
	 * $nom = nom ou code de l'équipement
	 * $varRetour = Valeur lu
	 * ********************************** */
	function get1WireUnique($nom, $cle='') {
		/* Variable de débogage */
		if ($this->debug == true) $this->log->write('Debug en cours : get1WireUnique()');
	
		/* Initialisation des variables */
		$varRetour = '';
	
		/* Vérification de la clé de sécurité */
		if ($this->verifcle($cle) != 1) return 9999;
	
		/* Initialisation des modules */
		$varLu = exec('sudo modprobe w1-gpio & $ sudo modprobe w1-therm');
		
		/* Lecture du matériel déclaré */
		if(substr($nom,0,3) != '28-') {
			/* Le nom n'est pas l'ID, on recherche l'ID */
			if ($this->debug == true) $this->log->write('Le nom de l\'équipement est : '.$nom.', on recherche l\'ID');
			$nom = $this->req->getIdWire($nom);	
			if ($this->debug == true) $this->log->write('Id de l\'équipement trouvé : '.$nom);
		} else if ($this->debug == true) $this->log->write('Le nom de l\'équipement correspond à son ID qui est : '.$nom);
		
		/* Lecture de la valeur du capteur */
		$varRetour = exec('cat /sys/bus/w1/devices/'.$nom.'/w1_slave | grep "t=" | awk -F "t=" \'{print $2/1000}\'');
		
		/* On met à jour les données lues dans la base de données */
		$this->req->majValeurEq($nom, $varRetour);
		
		/* On retourne le résultat du relevé */
		if ($this->debug == true) {
			$this->log->write('Valeur de température de \''.$nom.'\' lu = '.$varRetour);
			$this->log->write('Fin de l\'execution de get1WireUnique()');
		}
		return $varRetour; 	
	}
}

/* ************************************************ *
 *       FIN de Contrôle du Bus 1-Wire              *
 * ************************************************ */
?>
