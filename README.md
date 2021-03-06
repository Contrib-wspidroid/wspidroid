# WsPiDroid

WsPiDroid est un Web-Service � installer sur un Raspberry Pi. Le but est de permettre � un d�veloppeur de faire interagir son application avec un Raspberry Pi, et plus principalement avec les ports GPIO de ce Raspberry Pi. Cela est rendu possible, quelque soit le langage de d�veloppement utilis�, d�s lors qu'il est capable d'interagir avec un Web-Service de type SOAP.

WsPiDroid est compos� d'un ensemble de scripts PHP, dont la librairie NUSaop, � d�poser dans un r�pertoire Web du Raspberry Pi � interoger.

L'avantage du Web-Service pour la gestion des ports GPIO est que le Raspberry Pi n'a que cela � g�rer. La partie cliente, peut-�tre d�velopp�e dans n'importe quel langage de d�veloppement, le Raspberry Pi ne re�oit que des informations d'�criture ou de lecture � effectuer.
Dans un souci de s�curit�, l'avantage du Web-Service est qu'il ne peut faire que ce pour quoi est pr�vu.

# Historique

Le projet de d�part est la gestion d'un �clairage ext�rieur d'ambiance. Le tout automatis�, mais en dehors de l'automatisme, je souhaite pouvoir allumer ou �teindre mes �clairages � partir d'une page Web, mais principalement depuis une application Android. La solution que j'ai retenu est donc la cr�ation de ce Web-Service, il permet d'�tre consomm� par tout client Soap, et donc d'interroger ou de piloter les ports GPIO du Raspberry Pi.

# Description du Web-Service

Le Web-service est �crit en PHP, il est de type SOAP. Pour cr�er ce Web-Service, j'utilise la biblioth�que NuSoap, disponible sur le site Sourceforge (http://sourceforge.net/projects/nusoap/). Celle-ci permet le d�veloppement d'un serveur SOAP en PHP. L'utilisation du PHP est un atout majeur, il est en effet tr�s facile de stocker ce type de Web-Service sur tout Raspberry Pi, dont les pr�requis sont un serveur Web, PHP et la biblioth�que WiringPi (http://wiringpi.com).

Le logiciel client peut �tre d�velopp� avec tout type de logiciel, sachant invoquer un Web-Service de type SOAP. Il est donc facile de le porter sur tout type de plate-forme cliente. 

Par mesure de s�curit�, l'utilisation des commandes du Web-Service est accessible � l'aide d'un Token (cl� de s�curit�), que le client doit connaitre pour envoyer des ordres au Raspberry Pi via ce Web-Service.

# Fonctionnalit�s du Web-Service

## Constitution du Web-service

- La biblioth�que NuSoap.
- Le fichier principal du Web-service : � **wspi.php** �
- Un fichier � **config.inc.php** � qui contient diff�rentes informations sp�cifiques � l'utilisateur:
  - La variable � **$token** �, que l'on qualifie de cl� de s�curit�. Ce Token est libre, il doit �tre renseign� lors de l'appel du Web-service. Si le Token est diff�rent entre le client et le serveur, le Web-Service retourne au client une erreur de cl� de s�curit� non valide, et donc ne fait aucune action. La valeur retourn�e est 9999, il faudra l'interpr�ter dans le logiciel client comme �tant � Cl� de s�curit� non valide �.
  - Un tableau � **$commandes** �, qui contient le nom que l'on souhaite donner � chacun des ports GPIO (�clairage escalier, �clairage chambre, ...), ainsi que le num�ro du port auquel il est attach�. Ces informations sont d�clar�es sur le Web-Service, de cette fa�on elles sont r�cup�r�es par le ou les logiciels clients. Le num�ro de port d�clar� peut-�tre le num�ro du GPIO ou le num�ro WiringPi, au choix de l'utilisateur, d�s l'instant que ce choix est le m�me pour tous les ports.
  - La variable � **$typePin** � permet de pr�ciser si le num�ro du port sp�cifi� dans le tableau � $commande � (ci-dessus), est un num�ro de port physique � **P** � ou le num�ro de port WiringPi � **W** �. Le Web-service fera lui-m�me la conversion si besoin.

## Les m�thodes du Web-service

- **setPin()** : Permet � d'allumer � ou � �teindre � un port GPIO.
  - Trois valeurs d'entr�e : le num�ro du pin � �crire, la valeur de l'�tat (1 ou 0), et le Token de s�curit�.
  - Une valeur en retour : l'�tat du pin (1 ou 0). Valeur lu sur le port GPIO apr�s que l'action demand�e soit effectu�e.
- **getPin()** : Permet de lire un port GPIO.
  - Deux valeurs d'entr�e : le num�ro du pin � lire, et le Token de s�curit�.
  - Une valeur en retour : l'�tat du pin lu (1 ou 0).
- **getMaterielTab()** : Permet de lire les ports GPIO d�clar�s dans le Web-Service, ainsi que les informations de chacun des ports.
  - Deux valeurs en entr�e : Le token de s�curit�, et un deuxi�me param�tre qui permet de demander si l'on souhaite ou pas un retour de l'�tat (0 ou 1) de chacun des ports GPIO.
  - Une valeur sous forme de tableau en retour : Chacun des enregistrements du tableau contient le nom du port d�fini dans le Web-Service par l'utilisateur (�clairage escalier, �clairage chambre, ...), le num�ro du pin associ�, et si demand�, l'�tat du pin (1 ou 0).
- **getMaterielXml()** : Identique � getMaterielTab() ci-dessus, mais au lieu de retourner un tableau, le r�sultat est retourn� dans une cha�ne sous un format XML.
- **setCommande()** : Permet d'adresser au Raspberry Pi une commande Shell.
Les commandes sont pr�fix�e de la commande � **sudo** �, il faut donc donner les droits aux commandes autoris�es dans le **sudoers**
  - Deux valeurs d'entr�e : la commande shell, et le Token de s�curit�.
  - Une valeur en retour : La valeur du retour de la commande Shell.
- **get1WireTab()** : Permet de lire les valeurs de temp�rature via le port 1-Wire. 
La lecture est effectu�e depuis le r�pertoire `/sys/bus/w1/devices/`
  - Une valeur en entr�e : Le Token de s�curit�.
  - Une valeur sous forme de tableau en retour : Chacun des enregistrements du tableau contient le nom du capteur (son ID ou son nom si d�fini par l'utilisateur dans le fichier config.inc.php), et la valeur de temp�rature, en degr� Celcius, et trois d�cimales.
- **get1WireXml()** : Identique � get1WireTab() ci-dessus, mais au lieu de retourner un tableau, le r�sultat est retourn� dans une cha�ne sous un format XML.
- **setPsutil()** : Permet d'obtenir des informations syst�me via le module � **psutil** � sous Python.
  - Deux valeurs d'entr�e : la commande � **psutil** �, et le Token de s�curit�.
  - Une valeur en retour : les donn�es au format texte, retourn� par � **psutil** �

## Installation du module PsUtil pour Python

Se positionner dans le dossier ou est install� � **WsPiDroid** � et ex�cuter l'installateur.

    ./install.sh

# Donner les droits au Web-Service
## Droits d'utiliser l'�xecutable � gpio �

les commandes GPIO sont effectu�es par l'utilisateur � **www-data** � (utilisateur web). Cet utilisateur doit dont poss�der les droits de lecture et d'ex�cution du binaire � `/usr/local/bin/gpio` �.

Pour cela, ex�cuter la commande suivante :

    sudo chmod o+rx /usr/local/bin/gpio

## Droits d'�xecuter des commandes avec l'utilitaire � sudo �

Afin de pouvoir "Arr�ter" ou "Red�marrer" le Raspberry Pi depuis un client via ce Web-Service, ce dernier doit avoir les droits d'utiliser la commande � **sudo** � pour ces deux op�rations.

Il en est de m�me pour la lecture des sondes DS18b20, qui necessitent que le Web-Service charge les modules � **w1-gpio** � et � **w1-therm** � dans le noyau Linux.

Pour lui donner les droits, nous allons modifier le � **sudoers** �, afin d'ajouter quelques commandes � � **www-data** � qui lui seront autoris�es par la commande � **sudo** �.

Edition du � **sudoers** �

    sudo visudo

Dans la fen�tre d'�dition, nous cr�ons un Alias qui regroupe uniquement les commandes autoris�es par � **www-data** � via un � **sudo** �. Par s�curit�, on lui autorisera que ce dont a besoin le web-service.

En dessous du commentaire

    # Cmnd alias specification

Ajouter la ligne :

    Cmnd_Alias CMD=/sbin/halt,/sbin/reboot,/sbin/modprobe w1-gpio,/sbin/modprobe w1-therm,/bin/cat,/usr/bin/python

L'alias se nomme � **CMD** �

Les commandes � **/sbin/halt** � et � **/sbin/reboot** � permettent au Web-Service d'avoir droit aux commandes � **stop** � et � **reboot** � du Raspberry Pi.

Les commandes � **/sbin/modprobe w1-gpio** � et � **/sbin/modprobe w1-therm** � permettent au Web-Service d'avoir les droits de charger les modules � **w1-gpio** � et � **w1-therm** � et ainsi avoir acc�s aux donn�es des sondes de temp�rature DS18b20.

La commande � **/bin/cat** � permet de lister des fichiers.

La commande � **/usr/bin/python** � permet d'ex�cuter des scripts Python.

Vous n'�tes donc pas oblig� de mettre toutes ces autorisations si vous ne les utilisez pas toutes.

Toujours dans notre fichier � **sudoers** �, ajouter les autorisations sur l'Alias que nous venons de cr�er au user � **www-data** �.

A ajouter en dessous des autorisations donn�es au user � **pi** �, ce qui doit � la fin du fichier vous donner quelque chose du genre.

    #includedir /etc/sudoers.d
    pi ALL=(ALL) NOPASSWD: ALL
    www-data ALL=NOPASSWD: CMD

# Interrogation du Web-Service en PHP

Pour interroger le Web-Service, nous avons besoin d'un client. Ci-dessous nous allons d�tailler le minimum n�cessaire en PHP pour interroger le Web-Service.

Le client a besoin de la biblioth�que NuSoap. Voici ci-dessous les possibilit�s d'un client en PHP. 

D�claration du web-service :

    /* D�claration du webService */
    include('lib/nusoap.php');
    ini_set("soap.wsdl_cache_enabled", "0");
    $client = new nusoap_client('$WS_adresse.''http://adresse_webservice/wspi/wspi.php?wsdl');

Ecrire sur le port GPIO N�1 pour le mettre � l'�tat 1 :

    /* Pour mettre le GPIO 1 au niveau 1 */
    /* ********************************* */
    /* D�claration des param�tres d'entr�e du Web-service */
    $parametres = array('pin'=>1, 'valeur'=>1, 'cle' =>"Token");
    /* Execution du web-service et affichage de l'�tat du pin */
    echo $client->call('setPin', $parametres);

Lire l'�tat du GPIO 1 :

    /* Pour lire l'�tat du GPIO 1 */
    /* ************************** */
    $parametres = array('pin'=>$pin, 'cle' =>"Token");
    echo $client->call('getPin', $parametres);

Lire les GPIO d�clar�s fonctionnels dans le Web-Service (config.inc.php) et retourner leur �tat :

    /* Pour lire les d�clarations GPIO faite dans le web-service */
    /* ********************************************************* */
    $parametres = array('cle' =>"Token", 'litEtat' =>1);
    $valTableau = $client->call('getMaterielTab', $parametres);
    
    /* $valTableau est un tableau retourn� par le Web-Service contenant toutes les d�clarations GPIO effectu�es sur la partie serveur */


# licence
<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">WsPiDroid</span> est mis � disposition selon les termes de la <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">licence Creative Commons Attribution - Pas d&#39;Utilisation Commerciale - Pas de Modification 4.0 International</a>.
