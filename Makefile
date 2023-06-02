phpmetrics:
	./vendor/bin/phpmetrics --report-html=var/myreport ./src

linter-autofix:
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix -v --using-cache=no

analyze:
	./vendor/bin/phplint
	./vendor/bin/phpstan --memory-limit=-1
	./vendor/bin/psalm --no-cache $(ARGS)
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no

composer-install:
	composer install

composer-dump:
	composer dump-autoload

composer-update:
	composer update

composer-outdated:
	composer outdated
