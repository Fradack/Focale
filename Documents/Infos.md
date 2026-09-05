github = https://github.com/Fradack/Focale
url locale = http://localhost:8888/CMS-PHOTO/
database locale mamp phpmyadmin = http://localhost:8888/phpMyAdmin5/index.php?route=/database/structure&server=1&db=focale

souhait de l'arborescence su site

url.domaine/ablum/id
url.domaine/image/id
url.domaine/administration/

---

## Environnement de dev local (mis à jour 2026-09-05)

MAMP MySQL écoute sur le port **8889** (pas 3306). `php` et `composer` ne sont pas sur le PATH :
tout se lance via le binaire complet `C:\MAMP\bin\php\php8.3.1\php.exe` (php.ini créé à côté avec
openssl/curl/mbstring/fileinfo/pdo_mysql/gd/exif/intl/sodium activés).

Pour lancer le site en local (3 processus séparés) :
```
C:\MAMP\bin\php\php8.3.1\php.exe artisan serve          # site sur http://127.0.0.1:8000
C:\MAMP\bin\php\php8.3.1\php.exe artisan queue:work      # génération des variantes d'image, envoi des mails
```
MAMP doit être démarré pour MySQL (pas besoin d'Apache).

Connexion admin de dev (créée par le seeder, à changer via /administration/profil) :
- URL : http://127.0.0.1:8000/administration/login
- E-mail : admin@focale.test
- Mot de passe : focale-admin

`composer.phar` est présent à la racine du projet (pas de Composer global installé sur la machine).