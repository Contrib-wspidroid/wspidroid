<?php

/* Token de sécurité */
/* ***************** */
/* Le token est un clé de sécurité, c'est un mot de passe pour votre WebService.
 * Vous définisez vous même cette clè comme bon vous semble,
 * l'important est qu'elle ne soit connu que par vous, le logiciel client doit la fournir lors de chaque appel au WebService.
 * Le but étant de protéger votre WebService.
 * Il faut déjà connaitre l'adresse de votre WebService, mais en plus il faut la clé pour pouvoir l'utiliser.
 */
$token = "DP37TFJTXXX";

/* Liste des pins que l'on souhaite commander */
/* Déclaration sous forme : "Nom visible par l'utilisateur" => "Pin" */
$commandes = array(
			"Led N° 1"=>11,
			"Led N° 2"=>12,
			"Led N° 3"=>13,
			"Led N° 4"=>15,
			"Led N° 5"=>16,
			"Led N° 6"=>18,
			"Led N° 7"=>22,
			"Led N° 8"=>29
				);

/* Les pins défini ci-dessus sont les pins "physique" (P) ou les "wiringpi" (W) */
$typePin = "P"; // Définition ci-dessus = Pins physique.

/* Liste des capteurs de température */
/* Si le ou les capteurs sont identifiés et nommés, le Web-service retourne leurs noms.
 * Pour tous les capteurs non identifiés et nommés dans le "array" ci-dessous
 * le Web-service retourne l'id du capteur non nommé. */
$capteurTemp = array(
			"28-000005fb1aed"=>"Freebox"
				);

// Tableau de correspondance PIN physiques / PIN wiringPI
$pins = array(11=>0, 12=>1, 13=>2, 15=>3, 16=>4, 18=>5, 22=>6, 7=>7, 
				3=>8, 5=>9, 24=>10, 26=>11, 19=>12, 21=>13, 23=>14, 8=>15, 10=>16, 
				29=>21, 31=>22, 33=>23, 35=>24, 37=>25, 32=>26, 36=>27, 38=>28, 40=>29,
				27=>30, 28=>31);
				
?>
