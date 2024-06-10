#!/bin/bash

if [ $# -eq 0 ]; then
    /usr/local/bin/php /var/www/html/cli.php
fi

/usr/local/bin/php /var/www/html/cli.php "$@"