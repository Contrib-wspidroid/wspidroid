# Évolutions en cours ou réalisées :
- ~~[APK Android] : Au lancement de l'application, nous n'arrivons plus sur la page d'action sur les GPIO, mais sur une page "menu" avec icônes. Il est ainsi possible d'accéder aux nouveautés du Web-service.~~
- ~~[Web-Service] : Ajout de la possibilité d'envoi d'une commande d'arrêt et de reboot au Raspberry.~~
- ~~[Web-Service] : Lecture de température via des capteurs DS18B20 (port « 1-wire » GPIO 4). Le web-service lit tous les capteurs connectés et retourne le résultat, soit sous forme XML, soit sous forme de tableau. Toutes les valeurs des capteurs connectés sont retournées sous forme <nom> et <valeur>. Le nom est l'identifiant du capteur relevé, exemple "28-000005adf", mais comme pour le nommage des ports GPIO dans le fichier "config.inc.php", il est possible de le nommé en un nom plus explicite.~~
- [**Web-Service**] : Envoi de commande Radio via émetteur RF 433Mh (Commande Volets, télécommande,...) via la lib "rcswitch-pi" https://github.com/r10r/rcswitch-pi
- [**Web-Service**] : Retour d'informations système du Raspberry Pi via PSUtil (charge Cpu, mem, T°CPU)
- ~~[Client-Php] : Créer une interface Responsive. (Utilisation Ecran et Smartphone).~~
- [**Web-Service**] : Lire une distance à l'aide du module Ultrason HC-SR04

# Projet d'évolutions à étudier :
- [Web-Service] : Offrir la possibilité aux utilisateurs de configurer les données du Web-Service (nom des GPIO, nom des capteurs de température) de 2 façons différentes.
Soit par le fichier "config.inc.php" comme actuellement, mais nécessitant l'édition du fichier de config, soit en utilisant une base de données sur le Raspberry hébergeant le Web-Service, oblige à installer une base de données sur le Raspberry si il ne possède pas déjà une, mais plus souple pour l'utilisateur qui peut ainsi configurer via une interface.
- [Web-Service] : Lecture d'image de la PiCam.
- [Web-Service] : Commande PWM pour commande servomoteur.
- [Web-Service] : Commande X10 via un module CM15 avec mochad. http://sourceforge.net/projects/mochad/
