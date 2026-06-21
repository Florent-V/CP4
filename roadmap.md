# 🚀 Roadmap du Projet - Splitter

Cette roadmap est divisée en étapes clés pour faire passer l'application d'un prototype fonctionnel à une plateforme robuste et scalable prête pour le public.

---

## 🏗 Étape 1 : Consolidation & Sécurité (Priorité Haute)

### Technique (Backend)
- [ ] **Refactoring Monétaire** : Migrer les montants `float` vers des `int` (cents) et intégrer `moneyphp/money`.
- [ ] **Sécurité (Voters)** : Remplacer `SplitterAccessManager` par des `Voters` Symfony standard.
- [ ] **Audit Logs** : Utiliser le bundle `StofDoctrineExtensions` (Loggable) pour afficher l'historique des modifications sur une dépense.
- [ ] **Validation Avancée** : Ajouter des contraintes de validation pour s'assurer qu'un membre appartient bien au splitter lors de l'ajout d'une dépense.

### Infrastructure
- [ ] **CI/CD** : Mettre en place un pipeline GitHub Actions pour lancer `GrumPHP` et les tests automatiquement.
- [ ] **Docker Prod** : Créer un `docker-compose.prod.yml` avec Nginx optimisé et HTTPS (Let's Encrypt).

---

## 📈 Étape 2 : Amélioration de l'Expérience Utilisateur (UX)

### Fonctionnalités Métier
- [ ] **Gestion des Devises** : Permettre de choisir une devise au niveau du Splitter et gérer les taux de conversion.
- [ ] **Dépenses Récurrentes** : Ajouter la possibilité de créer des dépenses automatiques (ex: loyer, abonnement Netflix).
- [ ] **Import CSV Public** : Finaliser le `CsvController` pour permettre à n'importe quel utilisateur d'importer ses dépenses (format Tricount/Splitwise).
- [ ] **Photos de Justificatifs** : Améliorer la visionneuse de photos de reçus (zoom, rotation).

### Interface (Frontend)
- [ ] **Tableau de Bord (Dashboard)** : Créer une vue globale pour un utilisateur montrant la somme totale qu'il doit ou qu'on lui doit à travers tous ses Splitters.
- [ ] **Mode Hors-ligne (PWA)** : Améliorer la gestion du cache pour permettre la saisie de dépenses sans connexion, avec synchronisation ultérieure.

---

## 🚀 Étape 3 : Fonctionnalités Avancées & Scalabilité

### Collaboration & Engagement
- [ ] **Invitations par Email/SMS** : Envoyer des invitations directes avec lien magique.
- [ ] **Notifications Push** : Utiliser Symfony Notifier pour envoyer des notifications PWA quand une dépense est ajoutée.
- [ ] **Commentaires** : Permettre de discuter sur une dépense spécifique.

### Optimisation
- [ ] **Pre-calcul des balances** : Implémenter un système de cache pour les balances afin de supporter des milliers de dépenses par Splitter sans ralentissement.
- [ ] **Export PDF** : Générer des rapports de synthèse propres en PDF.

---

## 🛠 Étape 4 : Évolutions Futures (Vision long terme)

- [ ] **Remboursement Intégré** : Intégration d'API de paiement (Stripe, Lydia, PayPal) pour rembourser directement depuis l'application.
- [ ] **Analyse de Reçus (OCR)** : Utiliser une API d'IA pour scanner les tickets de caisse et remplir automatiquement le montant et le nom de la dépense.
- [ ] **Statistiques Avancées** : Graphiques d'évolution des dépenses par catégorie et par membre.

---

## 🧪 Indicateurs de Succès (KPI)
1. Couverture de tests > 80%.
2. Temps de chargement de la page Splitter < 500ms.
3. Zéro erreur d'arrondi sur les balances.
