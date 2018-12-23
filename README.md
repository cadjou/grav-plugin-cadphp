# grav-plugin-cadphp
Include PHP simply inside Grav CMS

**In English**

## Installation

### By Grav Package Manager (GPM)
Coming Soon

### Manualy

Upload the plugin : https://github.com/cadjou/grav-plugin-cadphp/archive/master.zip

Copy the Pack in the folder Site-Grav/user/plugins/

And rename the plugin folder **grav-plugin-cadphp-master** to **cadphp**

## Use

To run a PHP file, you have to place the Keyword <code>cadphp:</code> at the beginning of the line in the content of a Grav page.

Next, indicate the path parameterized **pX** where **X** is between 1 and 99<br>
*More information in the Setup part*

To finish, you have to set the relative path to the predefined path **pX** without the extension *.php*

By default, p1 is set to point to the folder **/user/plugins/cadphp/php/**
```
cadphp:p1:test // Run /user/plugins/cadphp/php/test.php
cadphp:p2:chemin_du_fichier
cadphp:p3:chemin_du_fichier
cadphp:p4:chemin_du_fichier
cadphp:p5:chemin_du_fichier
```

#### PHP Limitation

The file must start with <code>\<?php</code> and it must be unique.<br>
The <code>?></code> closure is not mandatory but it must be unique and at the end of the file.

The file must return a string as in this example:
*/cadphp/php/test.php*
```
<?php
return "Hello the World";
```

## Setup

In the administration interface, several options are possible.

### Configuration of PHP file localizations

From the web interface, it is possible to define 5 configurable paths.<br>
The first by default is the folder **/user/plugins/cadphp/php/** and the second is the folder **/user/plugins/page/**

If the path begins with a <b>/</b> then it represents <i>Document Root</i> otherwise it is the path of the plugin cadPHP <b>/user/plugins/cadphp/</b>

A total of 99 configurable paths can be created.

### Advanced Settings
To secure the executed code, some PHP functions are prohibited by default.<br>
The prohibited are functions :
- allow_url_fopen  
- allow_url_include
- exec
- shell_exec
- system
- passthru
- popen
- stream_select
- ini_set

**At your own risk** to change this parts ;) .

## Errors

In case of error in the PHP code, it will not be executed and the error is logged in **/logs/grav.log**

## In Development

- Add a predefined path via the web interface
- Add Comments in the code
- Put more information in the error message

---

**En francais**

## Installation

### Via Grav Package Manager (GPM)

En cours d'implémentation

### Manuellement

Télécharger le plugin : https://github.com/cadjou/grav-plugin-cadphp/archive/master.zip

Copier le Pack dans le dossier Site-Grav/user/plugins/

Et renommer le plugin **grav-plugin-cadphp-master** en **cadphp**

## Utilisation
Pour exécuter un fichier PHP, il faut placer le mot-clé <code>cadphp:</code> en début de ligne dans le contenu d'une page Grav.

Ensuite, il faut indiquer le chemin pré-paramétré **pX** où **X** est compris entre *1 et 99*.<br>
*Plus d'informations dans la partie paramétrage.*

Pour terminer, il faut mettre le chemin relatif par rapport au chemin prédéfini **pX** sans l'extension *.php*

Par défaut, **p1** est défini pour pointer vers le dossier **/user/plugins/cadphp/php/**

```
cadphp:p1:test // Execute /user/plugins/cadphp/php/test.php
cadphp:p2:chemin_du_fichier
cadphp:p3:chemin_du_fichier
cadphp:p4:chemin_du_fichier
cadphp:p5:chemin_du_fichier
```

#### Limitation du PHP

Le fichier doit commencer par <code>\<?php</code> et il doit être unique.<br>
La fermeture <code>?></code> n'est pas obligatoire mais il doit être unique et en fin du fichier.

Le fichier doit retourne une chaine de caractères comme dans cette exemple :<br>
*/cadphp/php/test.php*
```
<?php
return "Hello the World";
```

## Paramétrage

Dans l'interface d'administration, plusieurs options sont possibles.

### Configuration des localisations des fichiers PHP

Depuis l'interface web, il est possible de définir 5 chemins paramétrables.<br>
Le 1er par défaut est le dossier **/user/plugins/cadphp/php/** et le 2ème est le dossier est **/user/plugins/page/**

Si le chemin commence par un <b>/</b> alors cela repésente <i>Document Root</i> sinon c'est le chemin du plugin cadPHP <b>/user/plugins/cadphp/</b>

Il est possible de créer 99 chemins paramétrables en tout.

### Configuration avancée

Pour sécuriser le code executé, certaines fonctions PHP sont interdites.<br>Par défaut, les fonctions interdites :
- allow_url_fopen  
- allow_url_include
- exec
- shell_exec
- system
- passthru
- popen
- stream_select
- ini_set

**A vos risques et périls** ;) .

## Erreurs

En cas d'erreur dans le code PHP, il ne sera pas executé et l'erreur est loggé dans **/logs/grav.log**

## En cours de développement

- Ajouter un chemin prédéfinit via l'interface web
- Traduire le Readme en Anglais
- Commenter le code
- Mettre plus d'informations dans le Erreur
