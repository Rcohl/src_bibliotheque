# Symfony

Ce répo contient une application de gestion de bibliothèques
il s'agit d'un ECF sur Symfony

## Prérequis

- Linux, MacOs ou Windows
- Bash
- PHP 8
- Composer
- Symfony-cli
- Mariadb
- Docker (optionnel)

## Installation

```
git clone https://github.com/Rcohl/src_bibliotheque
cd src_bibliotheque
composer install
```
Créez une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créer un fichier `.env` à la racine du projet :

```
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=15f236cefb4450b8758a06e082092414
DATABASE_URL="mysql://src_bibliotheque:123@127.0.0.1:3306/src_bibliotheque?serverVersion=mariadb-10.6.12&charset=utf8mb4"
```

Pensez à changer la variable `APP_SECRET` et les codes d'accès dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaîne de caractère de 32 caractères en hexadécimal.**

## Migration et fixtures

Pour que l'application soit utilisable, vous devez créer le schéma de base de données et charger des données :

```
bin/dofilo.sh
```


## Utilisation

Lancez le  serveur web de développement : 

```
synfony serve
```
Puis ouvrez la page suivante : [https://localhost:8000](https://localhost:8000)
