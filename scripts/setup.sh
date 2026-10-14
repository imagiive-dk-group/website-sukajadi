#!/bin/sh
# Pasang WordPress, WooCommerce, tema Yumnia, dan data contoh.
# Dipanggil dari: docker compose run --rm cli sh /scripts/setup.sh
# Bisa juga di luar Docker (mis. LocalWP → "Open site shell"):
#   sh scripts/setup.sh  dengan variabel DATA=/path/ke/contoh-data SCRIPTS=/path/ke/scripts
set -e

URL="${WP_URL:-http://localhost:8080}"
DATA="${DATA:-/contoh-data}"
SCRIPTS="${SCRIPTS:-/scripts}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"

if [ -d /var/www/html ] && [ -z "$WP_PATH" ]; then cd /var/www/html; fi
[ -n "$WP_PATH" ] && cd "$WP_PATH"

echo "→ Menunggu WordPress & database siap…"
i=0
until [ -f wp-config.php ] && wp db query "SELECT 1" >/dev/null 2>&1; do
  i=$((i+1)); [ $i -gt 60 ] && echo "WordPress belum siap. Coba lagi sebentar." && exit 1
  sleep 2
done

if ! wp core is-installed >/dev/null 2>&1; then
  echo "→ Memasang WordPress…"
  wp core install --url="$URL" --title="Yumnia" --admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASS" --admin_email="admin@contoh.id" --skip-email
fi

echo "→ Bahasa Indonesia…"
wp language core install id_ID --activate >/dev/null 2>&1 || echo "  (lewati: bahasa tidak bisa diunduh)"

echo "→ WooCommerce…"
wp plugin is-installed woocommerce || wp plugin install woocommerce
wp plugin activate woocommerce
wp language plugin install woocommerce id_ID >/dev/null 2>&1 || true

echo "→ Tema Yumnia…"
wp theme activate yumnia
wp rewrite structure '/%postname%/' >/dev/null

echo "→ Data contoh…"
wp eval-file "$SCRIPTS/seed.php" "$DATA"
wp rewrite flush >/dev/null

echo ""
echo "Selesai! Buka $URL"
echo "Admin: $URL/wp-admin  (user: $ADMIN_USER, sandi: $ADMIN_PASS)"
