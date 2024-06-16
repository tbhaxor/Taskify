#!/bin/sh

set -ex

php artisan migrate --force
chown taskify:taskify database/database.sqlite
php artisan optimize
/usr/bin/supervisord -c /etc/supervisord.conf
