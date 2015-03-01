# WsPiDroid

WsPiDroid est un Web-Service à installer sur un Raspberry Pi. Le but est de permettre à un développeur de faire interagir son application avec un Raspberry Pi, et plus principalement avec les ports GPIO de ce Raspberry Pi. Cela est rendu possible, quelque soit le langage de développement utilisé, dès lors qu'il est capable d'interagir avec un Web-Service de type SOAP.

WsPiDroid est composé d'un ensemble de scripts PHP, dont la librairie NUSaop, à déposer dans un répertoire Web du Raspberry Pi à interoger.

L'avantage du Web-Service pour la gestion des ports GPIO est que le Raspberry Pi n'a que cela à gérer. La partie cliente, peut-être développée dans n'importe quel langage de développement, le Raspberry Pi ne reçoit que des informations d'écriture ou de lecture à effectuer.
Dans un souci de sécurité, l'avantage du Web-Service est qu'il ne peut faire que ce pour quoi est prévu.

# Historique

Le projet de départ est la gestion d'un éclairage extérieur d'ambiance. Le tout automatisé, mais en dehors de l'automatisme, je souhaite pouvoir allumer ou éteindre mes éclairages à partir d'une page Web, mais principalement depuis une application Android. La solution que j'ai retenu est donc la création de ce Web-Service, il permet d'être consommé par tout client Soap, et donc d'interroger ou de piloter les ports GPIO du Raspberry Pi.

# Description du Web-Service

Le Web-service est écrit en PHP, il est de type SOAP. Pour créer ce Web-Service, j'utilise la bibliothèque NuSoap, disponible sur le site Sourceforge (http://sourceforge.net/projects/nusoap/). Celle-ci permet le développement d'un serveur SOAP en PHP. L'utilisation du PHP est un atout majeur, il est en effet très facile de stocker ce type de Web-Service sur tout Raspberry Pi, dont les prérequis sont un serveur Web, PHP et la bibliothèque WiringPi (http://wiringpi.com).

Le logiciel client peut être développé avec tout type de logiciel, sachant invoquer un Web-Service de type SOAP. Il est donc facile de le porter sur tout type de plate-forme cliente. 

Par mesure de sécurité, l'utilisation des commandes du Web-Service est accessible à l'aide d'un Token (clé de sécurité), que le client doit connaitre pour envoyer des ordres au Raspberry Pi via ce Web-Service.

# Fonctionnalités du Web-Service

## Constitution du Web-service

- La bibliothèque NuSoap.
- Le fichier principal du Web-service : « **wspi.php** »
- Un fichier « **config.inc.php** » qui contient différentes informations spécifiques à l'utilisateur:
  - La variable « **$token** », que l'on qualifie de clé de sécurité. Ce Token est libre, il doit être renseigné lors de l'appel du Web-service. Si le Token est différent entre le client et le serveur, le Web-Service retourne au client une erreur de clé de sécurité non valide, et donc ne fait aucune action. La valeur retournée est 9999, il faudra l'interpréter dans le logiciel client comme étant « Clé de sécurité non valide ».
  - Un tableau « **$commandes** », qui contient le nom que l'on souhaite donner à chacun des ports GPIO (éclairage escalier, éclairage chambre, ...), ainsi que le numéro du port auquel il est attaché. Ces informations sont déclarées sur le Web-Service, de cette façon elles sont récupérées par le ou les logiciels clients. Le numéro de port déclaré peut-être le numéro du GPIO ou le numéro WiringPi, au choix de l'utilisateur, dès l'instant que ce choix est le même pour tous les ports.
  - La variable « **$typePin** » permet de préciser si le numéro du port spécifié dans le tableau « $commande » (ci-dessus), est un numéro de port physique « **P** » ou le numéro de port WiringPi « **W** ». Le Web-service fera lui-même la conversion si besoin.

## Les méthodes du Web-service

- **setPin()** : Permet « d'allumer » ou « éteindre » un port GPIO.
  - Trois valeurs d'entrée : le numéro du pin à écrire, la valeur de l'état (1 ou 0), et le Token de sécurité.
  - Une valeur en retour : l'état du pin (1 ou 0). Valeur lu sur le port GPIO après que l'action demandée soit effectuée.
- **getPin()** : Permet de lire un port GPIO.
  - Deux valeurs d'entrée : le numéro du pin à lire, et le Token de sécurité.
  - Une valeur en retour : l'état du pin lu (1 ou 0).
- **getMaterielTab()** : Permet de lire les ports GPIO déclarés dans le Web-Service, ainsi que les informations de chacun des ports.
  - Deux valeurs en entrée : Le token de sécurité, et un deuxième paramètre qui permet de demander si l'on souhaite ou pas un retour de l'état (0 ou 1) de chacun des ports GPIO.
  - Une valeur sous forme de tableau en retour : Chacun des enregistrements du tableau contient le nom du port défini dans le Web-Service par l'utilisateur (éclairage escalier, éclairage chambre, ...), le numéro du pin associé, et si demandé, l'état du pin (1 ou 0).
- **getMaterielXml()** : Identique à getMaterielTab() ci-dessus, mais au lieu de retourner un tableau, le résultat est retourné dans une chaîne sous un format XML.
- **setCommande()** : Permet d'adresser au Raspberry Pi une commande Shell.
Les commandes sont préfixée de la commande « **sudo** », il faut donc donner les droits aux commandes autorisées dans le **sudoers**
  - Deux valeurs d'entrée : la commande shell, et le Token de sécurité.
  - Une valeur en retour : La valeur du retour de la commande Shell.
- **get1WireTab()** : Permet de lire les valeurs de température via le port 1-Wire. 
La lecture est effectuée depuis le répertoire `/sys/bus/w1/devices/`
  - Une valeur en entrée : Le Token de sécurité.
  - Une valeur sous forme de tableau en retour : Chacun des enregistrements du tableau contient le nom du capteur (son ID ou son nom si défini par l'utilisateur dans le fichier config.inc.php), et la valeur de température, en degré Celcius, et trois décimales.
- **get1WireXml()** : Identique à get1WireTab() ci-dessus, mais au lieu de retourner un tableau, le résultat est retourné dans une chaîne sous un format XML.
- **setPsutil()** : Permet d'obtenir des informations système via le module « **psutil** » sous Python.
  - Deux valeurs d'entrée : la commande « **psutil** », et le Token de sécurité.
  - Une valeur en retour : les données au format texte, retourné par « **psutil** »

## Installation du module PsUtil pour Python

Se positionner dans le dossier ou est installé « **WsPiDroid** » et exécuter l'installateur.

    ./install.sh

# Donner les droits au Web-Service
## Droits d'utiliser l'éxecutable « gpio »

les commandes GPIO sont effectuées par l'utilisateur « **www-data** » (utilisateur web). Cet utilisateur doit dont posséder les droits de lecture et d'exécution du binaire « `/usr/local/bin/gpio` ».

Pour cela, exécuter la commande suivante :

    sudo chmod o+rx /usr/local/bin/gpio

## Droits d'éxecuter des commandes avec l'utilitaire « sudo »

Afin de pouvoir "Arréter" ou "Redémarrer" le Raspberry Pi depuis un client via ce Web-Service, ce dernier doit avoir les droits d'utiliser la commande « **sudo** » pour ces deux opérations.

Il en est de même pour la lecture des sondes DS18b20, qui necessitent que le Web-Service charge les modules « **w1-gpio** » et « **w1-therm** » dans le noyau Linux.

Pour lui donner les droits, nous allons modifier le « **sudoers** », afin d'ajouter quelques commandes à « **www-data** » qui lui seront autorisées par la commande « **sudo** ».

Edition du « **sudoers** »

    sudo visudo

Dans la fenêtre d'édition, nous créons un Alias qui regroupe uniquement les commandes autorisées par « **www-data** » via un « **sudo** ». Par sécurité, on lui autorisera que ce dont a besoin le web-service.

En dessous du commentaire

    # Cmnd alias specification

Ajouter la ligne :

    Cmnd_Alias CMD=/sbin/halt,/sbin/reboot,/sbin/modprobe w1-gpio,/sbin/modprobe w1-therm,/bin/cat,/usr/bin/python

L'alias se nomme « **CMD** »

Les commandes « **/sbin/halt** » et « **/sbin/reboot** » permettent au Web-Service d'avoir droit aux commandes « **stop** » et « **reboot** » du Raspberry Pi.

Les commandes « **/sbin/modprobe w1-gpio** » et « **/sbin/modprobe w1-therm** » permettent au Web-Service d'avoir les droits de charger les modules « **w1-gpio** » et « **w1-therm** » et ainsi avoir accès aux données des sondes de température DS18b20.

La commande « **/bin/cat** » permet de lister des fichiers.

La commande « **/usr/bin/python** » permet d'exécuter des scripts Python.

Vous n'êtes donc pas obligé de mettre toutes ces autorisations si vous ne les utilisez pas toutes.

Toujours dans notre fichier « **sudoers** », ajouter les autorisations sur l'Alias que nous venons de créer au user « **www-data** ».

A ajouter en dessous des autorisations données au user « **pi** », ce qui doit à la fin du fichier vous donner quelque chose du genre.

    #includedir /etc/sudoers.d
    pi ALL=(ALL) NOPASSWD: ALL
    www-data ALL=NOPASSWD: CMD

# Interrogation du Web-Service en PHP

Pour interroger le Web-Service, nous avons besoin d'un client. Ci-dessous nous allons détailler le minimum nécessaire en PHP pour interroger le Web-Service.

Le client a besoin de la bibliothèque NuSoap. Voici ci-dessous les possibilités d'un client en PHP. 

Déclaration du web-service :

    /* Déclaration du webService */
    include('lib/nusoap.php');
    ini_set("soap.wsdl_cache_enabled", "0");
    $client = new nusoap_client('$WS_adresse.''http://adresse_webservice/wspi/wspi.php?wsdl');

Ecrire sur le port GPIO N°1 pour le mettre à l'état 1 :

    /* Pour mettre le GPIO 1 au niveau 1 */
    /* ********************************* */
    /* Déclaration des paramètres d'entrée du Web-service */
    $parametres = array('pin'=>1, 'valeur'=>1, 'cle' =>"Token");
    /* Execution du web-service et affichage de l'état du pin */
    echo $client->call('setPin', $parametres);

Lire l'état du GPIO 1 :

    /* Pour lire l'état du GPIO 1 */
    /* ************************** */
    $parametres = array('pin'=>$pin, 'cle' =>"Token");
    echo $client->call('getPin', $parametres);

Lire les GPIO déclarés fonctionnels dans le Web-Service (config.inc.php) et retourner leur état :

    /* Pour lire les déclarations GPIO faite dans le web-service */
    /* ********************************************************* */
    $parametres = array('cle' =>"Token", 'litEtat' =>1);
    $valTableau = $client->call('getMaterielTab', $parametres);
    
    /* $valTableau est un tableau retourné par le Web-Service contenant toutes les déclarations GPIO effectuées sur la partie serveur */


# licence
<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">WsPiDroid</span> est mis à disposition selon les termes de la <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">licence Creative Commons Attribution - Pas d&#39;Utilisation Commerciale - Pas de Modification 4.0 International</a>.
