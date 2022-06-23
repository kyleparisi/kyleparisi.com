local:
	php -S localhost:8000 -t public/

test:
	php Tests.php

deploy:
	ssh -A example.com "cd /var/www/minible && git pull && composer install --no-dev"

pull_db:
	scp example.com:/var/www/minible/application.db .

install:
	composer install
