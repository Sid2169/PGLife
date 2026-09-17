#!/bin/bash
#
# Container entrypoint.
#
# Managed platforms (Railway, Render, Fly.io, Heroku, ...) inject the port the
# app must listen on via the $PORT environment variable. Locally / in
# docker-compose, $PORT is unset and Apache keeps its default port 80.
#
set -e

# Apache allows exactly one MPM. The php:apache base image (and some platforms)
# can end up with both mpm_event and mpm_prefork enabled, which makes Apache
# abort with "AH00534: More than one MPM loaded". mod_php needs mpm_prefork.
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork

PORT="${PORT:-80}"

sed -ri "s/^Listen 80\$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec docker-php-entrypoint "$@"
