# �volutions en cours ou r�alis�es :
- ~~[APK Android] : Au lancement de l'application, nous n'arrivons plus sur la page d'action sur les GPIO, mais sur une page "menu" avec ic�nes. Il est ainsi possible d'acc�der aux nouveaut�s du Web-service.~~
- ~~[Web-Service] : Ajout de la possibilit� d'envoi d'une commande d'arr�t et de reboot au Raspberry.~~
- ~~[Web-Service] : Lecture de temp�rature via des capteurs DS18B20 (port � 1-wire � GPIO 4). Le web-service lit tous les capteurs connect�s et retourne le r�sultat, soit sous forme XML, soit sous forme de tableau. Toutes les valeurs des capteurs connect�s sont retourn�es sous forme <nom> et <valeur>. Le nom est l'identifiant du capteur relev�, exemple "28-000005adf", mais comme pour le nommage des ports GPIO dans le fichier "config.inc.php", il est possible de le nomm� en un nom plus explicite.~~
- [**Web-Service**] : Envoi de commande Radio via �metteur RF 433Mh
- [**Web-Service**] : Retour d'informations syst�me via PSUtil (charge Cpu, mem, T�CPU)
- ~~[Client-Php] : Cr�er une interface Responsive. (Utilisation Ecran et Smartphone).~~

# Projet d'�volutions � �tudier :
- [Web-Service] : Offrir la possibilit� aux utilisateurs de configurer les donn�es du Web-Service (nom des GPIO, nom des capteurs de temp�rature) de 2 fa�ons diff�rentes.
Soit par le fichier "config.inc.php" comme actuellement, mais n�cessitant l'�dition du fichier de config, soit en utilisant une base de donn�es sur le Raspberry h�bergeant le Web-Service, oblige � installer une base de donn�es sur le Raspberry si il ne poss�de pas d�j� une, mais plus souple pour l'utilisateur qui peut ainsi configurer via une interface.
