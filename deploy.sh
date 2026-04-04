#!/bin/bash
# =============================================================================
# Script de déploiement Production — Symfony 7.4 / PHP 8.4 / Caddy
# Usage:
#   ./deploy.sh          → simulation prod en local (sans restart services système)
#   ./deploy.sh prod     → déploiement prod réel   (avec restart php8.4-fpm)
# =============================================================================

set -euo pipefail

MODE=${1:-local}

# --- PHP 8.4 explicite (cohabitation PHP 8.4 + 8.4 sur le serveur) ----------
PHP_BIN="php8.4"
if ! command -v "$PHP_BIN" &>/dev/null; then
    PHP_BIN="php"   # fallback (en local, php peut pointer vers 8.4 directement)
fi
CONSOLE="$PHP_BIN bin/console"

# --- Couleurs ----------------------------------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'

step() { echo -e "\n${BLUE}══ $1${NC}"; }
ok()   { echo -e "${GREEN}  ✔ $1${NC}"; }
warn() { echo -e "${YELLOW}  ⚠ $1${NC}"; }
fail() { echo -e "${RED}  ✘ ERREUR : $1${NC}"; exit 1; }

# =============================================================================
# VÉRIFICATIONS PRÉLIMINAIRES
# =============================================================================

step "Vérifications préliminaires"

[ -f "bin/console" ]           || fail "bin/console introuvable — lancer depuis la racine du projet."
command -v composer &>/dev/null || fail "composer introuvable."
command -v "$PHP_BIN" &>/dev/null || fail "$PHP_BIN introuvable."

# Force APP_ENV=prod APP_DEBUG=0 pour toute l'exécution du script
export APP_ENV=prod
export APP_DEBUG=0

ok "Répertoire : $(pwd)"
ok "PHP utilisé : $($PHP_BIN -r 'echo PHP_VERSION;')"
ok "APP_ENV=prod APP_DEBUG=0 forcés"

# =============================================================================
# ÉTAPE 1 — Suppression du cache AVANT tout (évite la contamination opcache/cache)
# =============================================================================
#
# POURQUOI EN PREMIER ?
# Si le cache prod a été compilé avec les deps dev (DebugBundle présent),
# il contient des références à des classes dev-only.
# Supprimer le cache ICI garantit que composer install --no-dev ne va pas
# tenter de booter un container qui référence des classes absentes.
#
step "Suppression du cache prod (étape critique)"

rm -rf var/cache/prod/
ok "var/cache/prod/ supprimé"

# =============================================================================
# ÉTAPE 2 — Composer sans dépendances de développement
# =============================================================================
#
# POURQUOI APRÈS la suppression du cache ?
# composer install déclenche post-install-cmd → "cache:clear" via symfony-cmd.
# Si l'ancien cache référençait DebugBundle (absent avec --no-dev),
# ce cache:clear échouerait. En supprimant le cache d'abord, on démarre propre.
#
step "Installation des dépendances PHP (sans dev)"

composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

ok "vendor/ mis à jour sans paquets dev"

# =============================================================================
# ÉTAPE 3 — Compilation du container prod (cache:warmup)
# =============================================================================

step "Compilation du container DI prod (cache:warmup)"

$CONSOLE cache:warmup --env=prod --no-debug

ok "Container prod compilé dans var/cache/prod/"

# =============================================================================
# ÉTAPE 4 — Migrations base de données
# =============================================================================

step "Migrations Doctrine"

$CONSOLE doctrine:migrations:migrate \
    --env=prod \
    --no-debug \
    --no-interaction \
    --allow-no-migration

ok "Migrations exécutées"

# =============================================================================
# ÉTAPE 5 — Compilation SASS
# =============================================================================

step "Compilation SASS"

$CONSOLE sass:build --env=prod --no-debug

ok "SASS compilé"

# =============================================================================
# ÉTAPE 6 — Compilation des assets (Asset Mapper)
# =============================================================================

step "Assets (Asset Mapper)"

$CONSOLE asset-map:compile --env=prod --no-debug

ok "Assets compilés dans public/assets/"

# =============================================================================
# ÉTAPE 7 — Permissions sur var/ et dossiers d'upload
# =============================================================================
#
# Le processus PHP-FPM (www-data) doit pouvoir écrire dans :
#   - var/                    : cache, logs, sessions, partages (var/share)
#   - public/images/profil/   : photos de profil (VichUploader)
#   - public/images/expense/  : photos de dépenses (VichUploader)
#
step "Permissions des répertoires sensibles"

# Crée les dossiers s'ils n'existent pas encore
mkdir -p var/log var/cache var/share
mkdir -p public/images/profil public/images/expense

# chmod en premier (le deploy user possède encore les fichiers)
chmod -R u+rwX,g+rX,o-rwx var/
chmod -R u+rwX,g+rwX,o+rX public/images/profil/ public/images/expense/

if [ "$MODE" = "prod" ]; then
    # En production : ownership www-data (user PHP-FPM) — après chmod
    WEB_USER="www-data"

    if id "$WEB_USER" &>/dev/null; then
        chown -R "$WEB_USER":"$WEB_USER" var/
        chown -R "$WEB_USER":"$WEB_USER" public/images/profil/ public/images/expense/
        ok "Ownership → $WEB_USER:$WEB_USER sur var/ et public/images/"
    else
        warn "Utilisateur $WEB_USER introuvable. Vérifier le user PHP-FPM dans /etc/php/8.4/fpm/pool.d/www.conf"
    fi
fi

ok "Permissions appliquées"

# =============================================================================
# ÉTAPE 8 — Vérification du container
# =============================================================================

step "Vérification du container DI"

$CONSOLE lint:container --env=prod --no-debug

ok "Container valide — aucune dépendance manquante"

# =============================================================================
# ÉTAPE 9 — Actions spécifiques au déploiement réel (mode prod)
# =============================================================================

if [ "$MODE" = "prod" ]; then

    # --- Restart PHP 8.4-FPM (vide l'opcache) --------------------------------
    #
    # CRITIQUE : sans ce restart, PHP-FPM sert les anciens fichiers depuis
    # l'opcache même si var/cache/prod/ a été vidé et reconstruit.
    # L'ancienne version compilée du container (avec DebugBundle) resterait
    # en mémoire → le 500 persisterait.
    #
    step "Restart php8.4-fpm (vidage opcache)"

    if command -v systemctl &>/dev/null; then
        if systemctl is-active --quiet php8.4-fpm; then
            systemctl restart php8.4-fpm
            ok "php8.4-fpm redémarré — opcache vidé"
        else
            warn "php8.4-fpm ne semble pas actif via systemctl."
            warn "Vider l'opcache manuellement si nécessaire."
        fi
    else
        warn "systemctl non disponible. Redémarrer php8.4-fpm manuellement."
    fi

    # --- Reload Caddy (facultatif, utile si config changée) ------------------
    if command -v caddy &>/dev/null && command -v systemctl &>/dev/null; then
        if systemctl is-active --quiet caddy; then
            systemctl reload caddy 2>/dev/null && ok "Caddy rechargé" || warn "Reload Caddy échoué (non bloquant)"
        fi
    fi

    # --- Workers Messenger ----------------------------------------------------
    if command -v supervisorctl &>/dev/null; then
        step "Restart workers Messenger (supervisord)"
        supervisorctl restart messenger-worker:* 2>/dev/null \
            && ok "Workers Messenger redémarrés" \
            || warn "supervisorctl non configuré — ignorer si workers pas utilisés"
    fi
fi

# =============================================================================
# RÉSUMÉ
# =============================================================================

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  Déploiement terminé avec succès !           ║${NC}"
echo -e "${GREEN}║  Mode : ${MODE}$(printf '%*s' $((38 - ${#MODE})) '')║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""

if [ "$MODE" = "local" ]; then
    echo -e "${YELLOW}Prochaine étape pour tester en local :${NC}"
    echo -e "  1. Vérifier que .env.local contient APP_ENV=prod et APP_DEBUG=0"
    echo -e "  2. Lancer : symfony server:start"
    echo -e "  3. Ouvrir : https://127.0.0.1:8000"
    echo ""
    echo -e "${YELLOW}Note :${NC} En local, le restart PHP-FPM n'est pas effectué."
    echo -e "  Si tu utilises le serveur Symfony CLI, l'opcache n'est pas actif → pas de problème."
    echo -e "  Si tu utilises PHP-FPM en local avec opcache, relancer php8.4-fpm manuellement."
    echo ""
fi
