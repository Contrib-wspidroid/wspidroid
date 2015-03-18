<?php
/* ************************************************ *
 *       Début de Contrôle du Bus 1-Wire            *
 * ************************************************ */
/*  Lecture du nom et de la valeur des capteurs     *
 *      Reponse sous forme de Tableau ou XML        */
/* ************************************************ */
	
class wire extends wspi {
	
	/* Lecture du Bus 1-Wire (Réponse sous format Tableau) */
	/* *************************************************** */
	function get1WireTab($cle='') {
		// Variable de débogage.
		if ($this->debug == true) $this->log->write('Debug en cours : get1WireTab()');
	
		// Initialisation des variables.
		$varRetour = array();
	
		// Vérification de la clé de sécurité, 
		if ($this->verifcle($cle) != 1) return 9999;
	
		// Lecture du matériel déclaré.
		$capteurTemp = json_decode(_CAPTEUR1WIRE_,true);
	
		// Lecture du nom des capteurs que l'on stock dans un tableau.
		$varLu = exec('sudo modprobe w1-gpio & $ sudo modprobe w1-therm');
		if($dossier = opendir('/sys/bus/w1/devices/')) { 			// Si le dossier existe, on l'ouvre.
			while(false !== ($fichier = readdir($dossier))) { 	// Si la lecture du dossier ne retourne pas d'erreur, on boucle.
				if(substr($fichier,0,3) == '28-') { 							// Seul les fichiers commençant par '28-' sont des capteurs.
					// On lit chacun des capteurs.
					$varLu = exec('find /sys/bus/w1/devices/ -name "'.$fichier.'" -exec cat {}/w1_slave \; | grep "t=" | awk -F "t=" \'{print $2/1000}\'');
					$varRetour[] = array( 'nom' => (isset($capteurTemp[$fichier])?$capteurTemp[$fichier]:$fichier), 'valeur' => $varLu);
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
	/* Lecture du Bus 1-Wire (Réponse sous format XML) */
	/* *********************************************** */
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
			while(false !== ($fichier = readdir($dossier))) { 	// Si la lecture du dossier ne retourne pas d'erreur, on boucle.
				if(substr($fichier,0,3) == '28-') { 							// Seul les fichiers commençant par '28-' sont des capteurs.
					// On lit chacun des capteurs.
					++$j;
					$varRetour .= "<temperature att=".'"'.$j.'"'.">";
					$varRetour .= "<nom>".(isset($capteurTemp[$fichier])?$capteurTemp[$fichier]:$fichier)."</nom>";
					$varRetour .= "<valeur>".exec('find /sys/bus/w1/devices/ -name "'.$fichier.'" -exec cat {}/w1_slave \; | grep "t=" | awk -F "t=" \'{print $2/1000}\'')."</valeur>";
					$varRetour .= "</temperature>";
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


}

/* ************************************************ *
 *       FIN de Contrôle du Bus 1-Wire              *
 * ************************************************ */
?>
