Docker for Relight-Front
========================

#### Prérequis
Avoir installer :
- docker
- docker-compose

Documentation en ligne : https://docs.docker.com/install/#cloud

/!\ Sous Ubuntu/Debian, si vous voulez utiliser docker avec un utilisateur qui n'a pas les droits root : /!\ 
```bash
If you would like to use Docker as a non-root user, you should now consider
adding your user to the "docker" group with something like:

  sudo usermod -aG docker your-user
```

## Architecture

Voici la liste des containers:

* `web`: serveur Apache2.4 et PHP 7.1  
* `mysql`: serveur MySQL 5.7
* `mailcatcher`: serveur de mails avec une interface disponible à l'adresse http://127.0.0.1:1080/
* `blackfire`: This is the [Blackfire](https://blackfire.io/docs/introduction) container (used for profiling the application).
* `nodejs`: NodeJS 6.
* `rabbitmq`: serveur [Rabbit MQ](http://www.rabbitmq.com/documentation.html) avec une interface disponible à l'adresse http://127.0.0.1:15672
* `sphinxsearch`: moteur de recherche [Sphinx](http://sphinxsearch.com/docs/) en version 2.2.10

## Configuration

### Créer le fichier docker-env à la racine du projet
```bash
cp docker-env.dist docker-env
```
Compléter les variables d'environnement. 
```bash
MYSQL_ROOT_PASSWORD=password
```

### Créer le fichier docker-compose.yml à la racine du projet

**/!\ Attention** , 2 modèles de fichier sont mis à votre disposition :
- `docker-compose.yml.dist` qui permet d'installer Dimipro seul
- `docker-compose-with-relight-connection.yml.dist` qui permet de faire tourner Dimipro et Relight en même temps, chacun ayant son propre docker ! 
Dans ce cas, il faudra **installer Relight en premier**. 

```bash
cp docker-compose.yml.dist docker-compose.yml
## OU
cp docker-compose-with-relight-connection.yml.dist docker-compose.yml
```

### Dossiers partagés
Adaptez la liste des dossiers partagés selon vos besoins dans le fichier **docker-compose.yml**

```bash
<dossier projet relight-front dimipro>:/var/www/dimipro
<dossier projet relight-front pulsat>:/var/www/pulsat
<dossier projet relight-front>/docker/dev/web-php7/extra/vhosts:/etc/apache2/sites-available
<dossier docker relight-front>/docker/dev/web-php7/extra:/root/web/extra
```
Vous pouvez créer un dossier par projet ou faire un dossier global 
```bash
/home/<username>/workspace:/var/www
```

Pour **mysql**, on utilisera de préférence un répertoire où seront stockés les fichiers mysql, autre que le répertoire projet :
```bash
<dossier base relight-front>:/var/lib/mysql
```
ex: /var/lib/mysql/

## Installation
### Build the environment
A partir du moment où les fichiers de configuration sont prêts, il suffit 
de se mettre à la racine du projet Dimipro, et faire : 
```bash
make docker-init 
```
Les containers vont être créés et configurés.
Si vous souhaitez tout faire à la main, continuez la lecture ci-dessous.


### Mettre à jour votre fichier hosts
```bash
sudo vim /etc/hosts
```
Exemples:
```bash
# Dimipro
127.0.0.1       www.dimipro-php7.local dimipro-php7.local

# Pulsat
127.0.0.1       www.pulsat-php7.local pulsat-php7.local

```

## Vhosts
Les vhosts sont dans le dossier 
```
docker > dev > web > extra
```
Tous les virtual hosts de ce dossier seront créés et activés pendant l'étape du build.

## Base de données
Une fois le docker mysql démarré, la base de donnée est accessible en ligne de commande ou via un client MySQL (MySQL Workbench, client MySql de phpstorm). 

## Commandes docker-compose
Voici quelques commandes docker-compose qui peuvent être utiles :

### Voir la liste des containers

Pour voir la liste des containers actifs vous pouvez utiliser la commande suivante :
```bash
docker-compose ps
```
Exemple de résultat :
```bash
           Name                         Command             State                                Ports                               
------------------------------------------------------------------------------------------------------------------------------------
relight_front_mailcatcher_1   mailcatcher -f --ip=0.0.0.0   Up      1025/tcp, 0.0.0.0:1080->1080/tcp                                 
relight_front_mysql_1         /usr/local/docker/run.sh      Up      0.0.0.0:3306->3306/tcp                                           
relight_front_web_1           /usr/local/docker/run.sh      Up      0.0.0.0:443->443/tcp, 0.0.0.0:80->80/tcp, 0.0.0.0:8000->8000/tcp 
```

### Démarrer/stopper docker-compose
```bash
make docker-start (ou docker-compose start)
make docker-stop (ou docker-compose stop)
```

### Supprimer tous les containers dimipro
```bash
docker rm -f $(docker ps -a -q --filter="name=relight_front_*")
```

### Supprimer toutes les images 
```bash
docker rmi $(docker images -q relight_front_*)
```

### Se connecter à un container
```bash
make web-shell

# pour sortir du container
exit 
```

## Moteur de recherche Sphinx
Un modèle de fichier de configuration est disponible pour chaque site dans les dossier `docker/dev/sphinxsearch/extra`.

- `dimipro_sphinx.conf.dist` : contient la configuration pour Dimipro
- `pulsat_sphinx.conf.dist` : contient la configuration pour Pulsat
- `all_sphinx.conf.dist` : contient la configuration multisite (Dimipro + Pulsat)

Le container ne prend en compte qu'un seul fichier de configuration. Donc si on veut utiliser sphinxsearch pour 2 sites il faut mettre toutes les configurations dans un seul fichier sphinx.conf

Pour que le moteur fonctionne, il faut du dupliquer le fichier en `docker/dev/sphinxsearch/extra/sphinx.conf` 
et adapter la configuration. 

Exemple : 
```bash
cp docker/dev/sphinxsearch/extra/dimipro_sphinx.conf.dist docker/dev/sphinxsearch/extra/sphinx.conf

```

Il faut juste éditer cette partie : 

```bash
# Configuration generale de la source pour Dimipro V2
source dimiprov2 : main
{
        sql_user = <db_user>
        sql_pass = <db_pass>
        sql_db = <db_name>
}
```