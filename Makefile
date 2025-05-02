
.DEFAULT_GOAL := help
.PHONY: init start stop deploy-prod build-prod fixtures


## 🎯 Initialisation complète du projet
init: ## Init projet : install, db drop/create, migrate, fixtures, assets
	@echo "🚀 Initialisation complète du projet..."
	composer install
	cp -n .env .env.local || true
	docker compose up -d
	php bin/console d:d:d -f || true
	php bin/console d:d:c
	php bin/console d:m:m --no-interaction
	php bin/console d:f:l --no-interaction
	php bin/console sass:build --watch &
	symfony server:start -d

## ⚙️ Démarrage normal (sans toucher aux données)
start: ## Démarre Symfony + Docker + met à jour le schéma DB
	@echo "🔧 Démarrage du projet..."
	composer install
	docker compose up -d
	php bin/console d:m:m --no-interaction
	php bin/console sass:build --watch &
	symfony server:start -d

## 🛑 Arrêt propre de tous les services
stop: ## Stoppe Symfony server + Docker + watchers
	@echo "🛑 Arrêt du projet..."
	symfony server:stop || true
	docker compose down
	pkill -f "php bin/console sass:build --watch" || true

## 🚀 Déploiement production
deploy-prod: ## Prépare le projet pour la production (sans perte de données)
	@echo "📦 Déploiement production..."
	composer install --no-dev --optimize-autoloader
	php bin/console d:m:m --no-interaction --no-debug
	php bin/console sass:build
	php bin/console asset-map:compile

## 🔁 Recharge des fixtures uniquement
fixtures: ## Recharge les fixtures (⚠️ supprime la DB)
	@echo "♻️ Réinitialisation base + fixtures"
	php bin/console d:d:d -f
	php bin/console d:d:c
	php bin/console d:m:m --no-interaction
	php bin/console d:f:l --no-interaction

## 🐳 Docker

up: ## Démarre les services Docker (base de données, mailpit)
	docker compose up -d

down: ## Stoppe les services Docker
	docker compose down

restart: down up ## Redémarre les services Docker

## ⚙️ Symfony

start: ## Démarre le serveur Symfony
	symfony server:start

console: ## Alias vers la console Symfony
	php bin/console

## 🎨 Assets

sass-watch: ## Lance la compilation Sass en mode dev
	php bin/console sass:build --watch

sass-prod: ## Compile Sass pour la production
	php bin/console sass:build

asset-map: ## Compile l'asset map pour la production
	php bin/console asset-map:compile

## 🧪 Fixtures

fixtures: ## Recharge la base et les fixtures
	php bin/console d:d:d -f || true
	php bin/console d:d:c
	php bin/console d:m:m --no-interaction
	php bin/console d:f:l --no-interaction

## 🔧 Qualité

grumphp: ## Lance GrumPHP manuellement
	vendor/bin/grumphp run

## 📬 Mailpit

mailpit-url: ## Affiche l'URL d’accès à l’interface Mailpit
	@echo "➡️ http://localhost:8025"

## 📦 Composer

composer-update: ## Met à jour Composer
	composer update

## MySQL
db-root: ## Ouvre une session MySQL en root dans le conteneur kopeck-db-local
	docker exec -it kopeck-db-local mysql -u root -p

db-secure: ## Supprime root et donne tous les droits à db_user (⚠️ danger en prod)
	docker exec -i kopeck-db-local mysql -u root -p$$(grep MYSQL_ROOT_PASSWORD .env | cut -d '=' -f2) -e "\
	DROP USER IF EXISTS 'root'@'%'; \
	DROP USER IF EXISTS 'root'@'localhost'; \
	CREATE USER IF NOT EXISTS 'db_user'@'%' IDENTIFIED BY 'db_password'; \
	GRANT ALL PRIVILEGES ON *.* TO 'db_user'@'%' WITH GRANT OPTION; \
	FLUSH PRIVILEGES;"
## 🆘 Aide


help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "🔹 [36m%-20s[0m %s\n", $$1, $$2}'
