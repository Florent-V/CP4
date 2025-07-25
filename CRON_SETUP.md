# Nettoyage Automatique des Codes de Partage

## Configuration Cron Simple

Pour nettoyer automatiquement les codes expirés **toutes les heures** :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (ajustez le chemin selon votre installation)
0 * * * * cd /path/to/your/project && php bin/console app:cleanup-expired-share-codes >> var/log/cron.log 2>&1
```

## Test

```bash
# Test manuel
php bin/console app:cleanup-expired-share-codes

# Vérifier les logsscheduler
tail -f var/log/cron.log
```

## Pour Production/Docker

```dockerfile
# Dans votre Dockerfile ou image
RUN crontab -l | { cat; echo "0 * * * * cd /var/www/html && php bin/console app:cleanup-expired-share-codes >> var/log/cron.log 2>&1"; } | crontab -
```

