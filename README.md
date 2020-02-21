# grav-plugin-cadphp
Include PHP simply inside Grav CMS

**In English**

## Installation

### By Grav Package Manager (GPM)

The CadPHP plugin is easy to install with GPM.

**$ bin/gpm install cadphp**

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

Le plugin CadPHP est facilement installable via la comme GPM ci-dessous:

**$ bin/gpm install cadphp**

### Manuellement

Télécharger le plugin : https://github.com/cadjou/grav-plugin-cadphp/archive/master.zip

Copier le Pack dans le dossier Site-Grav/user/plugins/

Et renommer le plugin **grav-plugin-cadphp-master** en **cadphp**

### Paramètres du plugin

Avec l'expérience, il est possible que les chemins dans les paramètres ne soit prit en compte lors de l'excution du code.
Dans ce cas, il faut resauvegarder la configuration du plugin dans l'administration ou via la commande CLI

## Utilisation
### Depuis le contenue d'un page
Pour exécuter un fichier PHP, il faut placer le mot-clé <code>cadphp:</code> en début de ligne dans le contenu d'une page Grav.

Ensuite, il faut indiquer le chemin pré-paramétré **pX** où **X** est compris entre *1 et 99*.<br>
*Plus d'informations dans la partie paramétrage.*

Pour terminer, il faut mettre le chemin relatif par rapport au chemin prédéfini **pX** sans l'extension *.php*

Par défaut, **p1** est défini pour pointer vers le dossier **/user/plugins/cadphp/php/**

```
cadphp:p1:test // Execute /user/plugins/cadphp/php/test.php (Retour "Hello the word")
cadphp:p2:chemin_du_fichier
cadphp:p3:chemin_du_fichier
cadphp:p4:chemin_du_fichier
cadphp:p5:chemin_du_fichier
```

### Depuis l'entête d'un page

Dans l'entête d'une page, vous pouvez ajouter un tableau comme la exemple ci-dessous :
```
---
title: 'Page with Forms'
cadphp:
    resultatPhpExe: 'p1:test'
---
# My Page

**_cadphp.resultatPhpExe**

```

Pour executer php, le mot-clé **cadphp:** contenant un tableau associatif.
La clé de ce tableau est le nom à utiliser pour utiliser la réponse texte dans le contenu de la page précédé du mot-clé **_cadphp.**

Dans cette exemple **_cadphp.resultatPhpExe** affichera *Hello the word*

### Depuis un formulaire validé

De la même manière que dans l'entête, il est possible de place le tableau dans le process d'un formulaire comme ci-dessous qui sera executé que si le formulaire est validé:

```
---
title: 'formulaire de test'
form:
    name: formulaire
    fields:
        - .....
    process:
        cadphp:
            reponsePhpExe: 'p1:test'
---
```

### Insérer des valeurs dans les champs d'un formulaire

Une autre manière d'appeler le code PHP, est d'utiliser la méthode statique \Grav\Plugin\CadphpPlugin::dataProcess

Et il faut lui passer en paramètre le code à executer comme l'exemple ci-dessous :

```
---
title: 'formulaire de test'
form:
    name: formulaire
    fields:
        checkGroupe:
            type: checkboxes
            label: Groupes
            data-options@:
                - '\Grav\Plugin\CadphpPlugin::dataProcess'
                - 'p1:codePHPQuiRetourneUnTableau'
            data-default@:
                - '\Grav\Plugin\CadphpPlugin::dataProcess'
                - 'p1:codePHPQuiRetourneUnAutreTableau'
---
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

 - En cas d'erreur dans le code PHP, il ne sera pas executé et l'erreur est loggé dans **/logs/grav.log**

## En cours de développement

- Ajouter un chemin prédéfinit via l'interface web
- Commenter le code (30%)
- Mettre plus d'informations dans le Erreur
