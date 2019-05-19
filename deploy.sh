#!/bin/bash

ssh nuc1.te0.io "cd /var/www/kyleparisi.com && git pull && composer install --no-dev"
