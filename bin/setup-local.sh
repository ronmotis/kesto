#!/usr/bin/env bash
# Reproduce the KESTO local site: WordPress + SQLite (WordPress Studio's stack) + WooCommerce,
# with the theme and plugin from this repo symlinked in, and the demo setup applied.
#
#   bin/setup-local.sh            # install (idempotent) and print next steps
#   bin/setup-local.sh serve      # also start http://localhost:8881
#
# Requires: php 8.1+ (pdo_sqlite, gd with WebP), curl, unzip.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SITE="$ROOT/.local-site"
WPDIR="$SITE/wordpress"
PORT="${PORT:-8881}"
WP_VERSION="${WP_VERSION:-7.1.5}"
WP="php $SITE/wp-cli.phar --allow-root --path=$WPDIR"

mkdir -p "$SITE"
cd "$SITE"
[ -f wp-cli.phar ] || curl -sSLo wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

if [ ! -f "$WPDIR/wp-load.php" ]; then
  curl -sSLo wp.zip "https://downloads.wordpress.org/release/wordpress-${WP_VERSION}.zip" && unzip -q wp.zip && rm wp.zip
fi
for slug in woocommerce sqlite-database-integration; do
  if [ ! -d "$WPDIR/wp-content/plugins/$slug" ]; then
    curl -sSLo p.zip "https://downloads.wordpress.org/plugin/${slug}.latest-stable.zip"
    unzip -q p.zip -d "$WPDIR/wp-content/plugins" && rm p.zip
  fi
done

# SQLite drop-in (what Studio uses instead of MySQL).
if [ ! -f "$WPDIR/wp-content/db.php" ]; then
  sed "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$WPDIR/wp-content/plugins/sqlite-database-integration#; s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
    "$WPDIR/wp-content/plugins/sqlite-database-integration/db.copy" > "$WPDIR/wp-content/db.php"
fi

if [ ! -f "$WPDIR/wp-config.php" ]; then
  $WP config create --dbname=wp --dbuser=x --dbpass=x --skip-check --extra-php <<'PHP'
define( 'DB_DIR', __DIR__ . '/wp-content/database/' );
define( 'DB_FILE', '.ht.sqlite' );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
PHP
fi
if ! $WP core is-installed 2>/dev/null; then
  $WP core install --url="http://localhost:$PORT" --title="KESTO" --admin_user=admin --admin_password=kesto-local --admin_email=admin@kesto.local --skip-email
fi

# Pin the URL explicitly (WordPress may otherwise guess one from the install path).
$WP option update home "http://localhost:$PORT" >/dev/null
$WP option update siteurl "http://localhost:$PORT" >/dev/null

ln -sfn "$ROOT/themes/kesto" "$WPDIR/wp-content/themes/kesto"
ln -sfn "$ROOT/plugins/kesto-core" "$WPDIR/wp-content/plugins/kesto-core"
$WP plugin activate woocommerce kesto-core 2>/dev/null
$WP theme activate kesto 2>/dev/null
$WP rewrite structure '/%postname%/' 2>/dev/null
$WP kesto setup 2>/dev/null | grep -v '^$'

cat > "$SITE/router.php" <<'PHP'
<?php
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file = __DIR__ . '/wordpress' . $path;
if ( '/' !== $path && file_exists( $file ) && ! is_dir( $file ) ) { return false; }
if ( is_dir( $file ) && file_exists( $file . '/index.php' ) ) { $_SERVER['SCRIPT_NAME'] = rtrim( $path, '/' ) . '/index.php'; require $file . '/index.php'; return; }
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/wordpress/index.php';
PHP

echo
echo "Local site ready: http://localhost:$PORT   (admin / kesto-local)"
if [ "${1:-}" = "serve" ]; then
  cd "$WPDIR" && PHP_CLI_SERVER_WORKERS=4 exec php -d memory_limit=512M -S "127.0.0.1:$PORT" -t . "$SITE/router.php"
else
  echo "Start it with: bin/setup-local.sh serve"
fi
