# 🧐 Audit et Revue du Projet - Splitter

Ce document présente un audit complet de l'application, classé par importance des points relevés, allant des problématiques critiques aux améliorations de confort.

---

## 🔴 Importance Haute : Critique & Sécurité

### 1. Précision Monétaire (Floating Point)
**Problème** : L'application utilise des `float` pour stocker les montants (`Expense::$amount`, `Transfer::$amount`). Les calculs de virgule flottante en PHP (et en informatique en général) sont imprécis (ex: `0.1 + 0.2 != 0.3`).
**Risque** : Des erreurs de centimes qui s'accumulent, rendant les comptes impossibles à équilibrer parfaitement.
**Recommandation** : Utiliser des **entiers (cents)** pour le stockage en base de données et des bibliothèques comme `moneyphp/money` pour les calculs.

### 2. Sécurité : Absence de Voters Symfony
**Problème** : La logique de sécurité est déportée dans des services comme `SplitterAccessManager` ou des méthodes privées dans les contrôleurs (`rejectIfNotMember`).
**Risque** : Code moins maintenable, difficile à tester de manière isolée et risque d'oubli d'un check de sécurité dans un nouveau contrôleur.
**Recommandation** : Implémenter des **Voters Symfony** (`SplitterVoter`, `ExpenseVoter`) pour centraliser la logique de `GRANT` et utiliser `is_granted()` de manière standard.

### 3. Logique en dur (Hardcoded logic) dans `CsvController`
**Problème** : Le `CsvController` contient des identifiants et des noms en dur (ex: `vasseurflorent@gmail.com`, `Vie Quotidienne`).
**Risque** : Fonctionnalité inutilisable pour d'autres utilisateurs et risque de plantage si les données n'existent pas.
**Recommandation** : Rendre le contrôleur générique en permettant à l'utilisateur de choisir le Splitter de destination et en utilisant l'utilisateur connecté.

### 4. Injection de SQL et Performance dans `SplitterRepository`
**Problème** : La méthode `findUserSplit` utilise une boucle pour construire des `orWhere` avec des paramètres dynamiques.
**Recommandation** : Utiliser l'expression `MEMBER OF` de manière plus optimisée ou des jointures explicites pour éviter des requêtes complexes.

---

## 🟡 Importance Moyenne : Architecture & Performance

### 1. Complexité de la Triade User / AppUser / Member
**Analyse** : Le choix est justifié par le besoin de gérer des membres sans compte, mais cela crée une complexité de navigation dans le code (ex: `$user->getAppUser()->getFavoriteSplitters()`).
**Recommandation** : Simplifier l'accès via des méthodes de raccourci dans l'entité `User` ou utiliser des `ArgumentValueResolver` pour injecter directement l'`AppUser` dans les contrôleurs.

### 2. Calculs de Balance à la Volée
**Problème** : Les balances sont recalculées intégralement à chaque affichage de la page `ShowController`.
**Risque** : Ralentissement important de l'application à mesure que le nombre de dépenses augmente.
**Recommandation** : Mettre en cache les résultats de `BalanceCalculator` ou utiliser une approche "Event Sourcing" / "Projections" pour maintenir une table de balances à jour lors de chaque ajout/modif de dépense.

### 3. Gestion des Devises
**Problème** : La devise est fixée en dur à `€` dans plusieurs contrôleurs.
**Recommandation** : Permettre de définir une devise par Splitter.

### 4. Infrastructure & Docker
**Problème** : La configuration Docker est très orientée développement (MySQL root password en clair, pas de configuration de cache PHP pour la prod dans le Dockerfile).
**Recommandation** : Préparer un `Dockerfile` multi-stage pour la production et utiliser des secrets Docker/Kubernetes pour les mots de passe.

---

## 🟢 Importance Faible : Qualité de Code & Maintenance

### 1. Couverture de Tests
**Analyse** : Il existe quelques tests unitaires sur `BalanceCalculator`, ce qui est excellent. Cependant, la logique de sécurité (Voters) et les workflows critiques (création de dépense avec répartition complexe) manquent de tests fonctionnels.
**Recommandation** : Ajouter des tests de bout en bout avec Foundry pour les fixtures et Panther/Playwright pour le front.

### 2. Documentation API
**Problème** : L'application semble avoir des points d'entrée API (`ShareCodeController`), mais aucune documentation (Swagger/OpenAPI) n'est présente.
**Recommandation** : Installer `NelmioApiDocBundle`.

### 3. Utilisation de Symfony UX
**Point Positif** : Excellente utilisation de `LiveComponent` et `TwigComponent`.
**Amélioration** : Pousser l'utilisation de `Turbo` pour éviter les rechargements de page complets sur les formulaires de dépenses.

---

## 🛠 Résumé Technique
- **Framework** : Symfony 7.4 (À jour, excellent)
- **PHP** : 8.4 (Très récent, parfait)
- **Qualité de code** : Présence de GrumPHP et PHPStan (Très bon point)
- **Points forts** : Architecture modulaire, utilisation des dernières fonctionnalités Symfony, PWA ready.
- **Points faibles** : Précision monétaire, centralisation de la sécurité, scalabilité des calculs.
