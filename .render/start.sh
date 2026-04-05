#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-10000}"

sed -ri "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

echo "ServerName 0.0.0.0" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null

apache2-foreground
