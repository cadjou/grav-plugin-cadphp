# grav-plugin-cadphp
Include PHP simply inside Grav CMS

**In English**

## Installation

## Optionss

## How to use

### KWA Community

---

**En francais**

## Installation

### Via Grav Package Manager (GPM)
En cours d'implémentation

### Manuellement

Télécharger le plugin : (https://github.com/cadjou/grav-plugin-cadphp/archive/master.zip)[https://github.com/cadjou/grav-plugin-cadphp/archive/master.zip]

## Utilisation

Pour executer un fichier PHP, il faut placer le mot clé <code>cadphp:</code> en début de ligne dans le contenu d'une page Grav.

Ensuite, il faut indiquer le chemin préparamétré **pX** ou *X* est compris entre *1 et 99*<br>
*Plus d'informations dans la partie Paramétrage*

Pour terminer, il faut mettre le chemin relatif par rapport au chemin prédéfinit **pX** sans l'extension *.php*

Par défaut, **p1** est définit pour pointer vers le dossier **/user/plugins/cadphp/php/**
```
cadphp:p1:test // Execute /user/plugins/cadphp/php/test.php
cadphp:p2:chemin_du_fichier
cadphp:p3:chemin_du_fichier
cadphp:p4:chemin_du_fichier
cadphp:p5:chemin_du_fichier
```

### Paramétrage

Dans le interface d'administration, plusieurs options sont possibles.

#### Configuration des localisations des fichiers PHP

Depuis l'interface web, il est possible de définir 5 chemins paramétrables. Le 1er par défaut est le dossier **/user/plugins/cadphp/php/** et le 2ème est le dossier est **/user/plugins/page/**

Si le chemin commence par un <b>/</b> alors cela repésente <i>Document Root</i> sinon c'est le chemin du plugin cadPHP <b>/user/plugins/cadphp/</b>

Il est possible de créer 99 chemins paramétrables en tout.

#### Configuration avancée

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

### Erreurs

En cas d'erreur dans le code PHP, il ne sera pas executé et l'erreur est loggé dans **/logs/grav.log**

### En cours de développement

- Ajouter un chemin prédéfinit via l'interface web
- Traduire le Readme en Anglais
- Commenter le code
- Mettre plus d'informations dans le Erreur