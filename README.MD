# 💸 Gestion de Comptes entre Amis

[![Symfony](https://img.shields.io/badge/Symfony-7.x-black?logo=symfony&logoColor=white)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)](https://www.php.net/)
[![Doctrine](https://img.shields.io/badge/Doctrine-ORM-yellow?logo=data:image/svg+xml;base64,PHN2ZyBmaWxsPSIjMDAwMDAwIiB2aWV3Qm94PSIwIDAgMzIgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHRleHQgeD0iMTAiIHk9IjI0IiBmb250LXNpemU9IjE2Ij5EPC90ZXh0Pjwvc3ZnPg==)](https://www.doctrine-project.org/)
[![UX LiveComponent](https://img.shields.io/badge/UX-LiveComponent-orange?logo=symfony)](https://symfony.com/bundles/ux-live-component/current/index.html)
[![UX TwigComponent](https://img.shields.io/badge/UX-TwigComponent-orange?logo=symfony)](https://symfony.com/bundles/ux-twig-component/current/index.html)
[![PWA](https://img.shields.io/badge/PWA-Ready-blueviolet?logo=pwa)](https://web.dev/progressive-web-apps/)
[![Code Quality](https://img.shields.io/badge/Code_Quality-GrumPHP-brightgreen)](https://github.com/phpro/grumphp)

## ✨ Présentation

Cette application permet de **gérer les dépenses entre plusieurs personnes** de manière simple et collaborative. Que ce soit pour :

- un **week-end entre amis** 🏕️
- des **vacances en groupe** ✈️
- une **colocation** 🏠
- ou même un **couple** ❤️

...vous pouvez suivre, répartir et équilibrer les comptes de manière équitable et transparente.

## ⚙️ Stack technique

- **Symfony 7**
- **PHP 8.2+**
- **Doctrine ORM**
- **Symfony UX** :
    - `LiveComponent`
    - `TwigComponent`
- **AssetMapper**
- **SASS** + **Bootstrap**
- **PWA ready**
- **GrumPHP** avec :
    - `phpmd`
    - `phpstan`
    - `phpcs`
    - `php-cs-fixer`
    - `twigcs`

## 🚀 Installation

### Étape 1 : Cloner le dépôt

```bash
git clone https://github.com/Florent-V/CP4
cd CP4
```

### Étape 2 : Configuration de l’environnement

Copiez le fichier `.env` vers `.env.local` :

```bash
cp .env .env.local
```

Dans ce fichier `.env.local`, configurez :

- les paramètres de **connexion à la base de données**
- les **informations de mailer** (voir plus bas avec Mailpit)

### Étape 3 : Lancer les services Docker

Assurez-vous d’avoir Docker installé, puis lancez les services :

```bash
docker compose up -d
```

Décommentez le service `mailpit` dans le fichier `docker-compose.override.yml` si nécessaire.

### Étape 4 : Lancer le serveur Symfony

```bash
symfony server:start
```

### Étape 5 : Compiler les assets

- En mode développement :

```bash
php bin/console sass:build --watch
```

- En mode production :

```bash
php bin/console sass:build
php bin/console asset-map:compile
```

### Étape 6 : Charger les fixtures

Un script `fixtures.sh` est disponible :

```bash
./fixtures.sh
```

Contenu du script :

```bash
php bin/console d:d:d -f
php bin/console d:d:c
php bin/console d:m:m --no-interaction
php bin/console d:f:l --no-interaction
```

## 📬 Configuration du mailer avec Mailpit

Dans `.env.local` :

```
MAILER_DSN=smtp://localhost:1025
```

L’interface web de Mailpit est disponible sur [http://localhost:8025](http://localhost:8025)

## 🔐 Sécurisation MySQL (optionnel)

Si vous souhaitez supprimer l'utilisateur `root` de la base MySQL dans votre environnement Docker et utiliser un utilisateur dédié :

1. Connectez-vous au conteneur MySQL :

```bash
docker exec -it <nom_du_container_mysql> mysql -u root -p
```

2. Supprimez l'utilisateur root (optionnel, à ne faire que si vous avez un autre utilisateur avec les privilèges nécessaires) :

```sql
DROP USER 'root'@'%';
DROP USER 'root'@'localhost';
```

3. Créez un nouvel utilisateur si ce n’est pas déjà fait (remplacez db_user et db_password par vos valeurs) et donnez-lui les privilèges appropriés :

```sql
CREATE USER 'db_user'@'%' IDENTIFIED BY 'db_password';
GRANT ALL PRIVILEGES ON *.* TO 'db_user'@'%';
FLUSH PRIVILEGES;
```

Attention : N’appliquez cela qu’en développement ou dans des environnements contrôlés. En production, il est recommandé de restreindre les privilèges au strict nécessaire.

## 🧪 Qualité du code

Le projet utilise **GrumPHP** pour garantir la qualité du code.

Les outils intégrés :

- `PHPStan` : analyse statique
- `PHPMD` : détection de mauvaises pratiques
- `PHP_CodeSniffer` : convention de code
- `PHP-CS-Fixer` : formatage
- `TwigCS` : conventions sur les templates Twig

Les hooks sont lancés automatiquement avant chaque commit.

## 📱 Fonctionnalités PWA

- Installation sur mobile/tablette
- Mode hors-ligne
- Chargement rapide via service worker

## 🤝 Contribuer

Les contributions sont les bienvenues ! Merci de respecter les conventions et le formatage du code.

---

🎯 **Prêt à équilibrer les comptes ? Lancez-vous !**