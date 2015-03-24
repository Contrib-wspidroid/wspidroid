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


class autoloader {
    
    static function register() {
        spl_autoload_register(array(__CLASS__,'autoload'));
    }

    static function autoload($class_name) {
        require_once 'classes/' . strtolower($class_name) . '.class.php';
    }
    
    
}

?>
