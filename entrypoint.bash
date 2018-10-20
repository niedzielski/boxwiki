#!/bin/bash
set -euo pipefail

# Wait for the db to come up
/wait-for-it.sh $DB_SERVER -t 120
# Sometimes it appears to come up and then go back down meaning MW install fails
# So wait for a second and double check!
sleep 1
/wait-for-it.sh $DB_SERVER -t 120

if [ -e "/var/www/html/LocalSettings.php" ]; then
  echo 'LocalSettings.php exists. Skipping database initialization.'
else
  php /var/www/html/maintenance/install.php --dbuser $DB_USER --dbpass $DB_PASS --dbname $DB_NAME --dbserver $DB_SERVER --lang $MW_SITE_LANG --pass $MW_ADMIN_PASS $MW_SITE_NAME $MW_ADMIN_NAME

  cat > LocalSettings.php << 'eof'
<?php
require_once "$IP/LocalSettingsDev.php";
eof

  php /var/www/html/maintenance/update.php --quick
fi

docker-php-entrypoint apache2-foreground
