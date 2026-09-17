#!/bin/bash
#
# Container entrypoint.
#
# Managed platforms (Railway, Render, Fly.io, Heroku, ...) inject the port the
# app must listen on via the $PORT environment variable. Locally / in
# docker-compose, $PORT is unset and Apache keeps its default port 80.
#
set -e

PORT="${PORT:-80}"

sed -ri "s/^Listen 80\$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec docker-php-entrypoint "$@"
