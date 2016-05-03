#!/usr/bin/env bash

PORT="8000"

if [ -n "$1" ]; then
    PORT="$1"
fi

php -S localhost:${PORT} -t web/ web/index.php
