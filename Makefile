phpmetrics:
	./vendor/bin/phpmetrics --report-html=var/myreport ./src

linter-autofix:
	./vendor/bin/php-cs-fixer fix -v --using-cache=no

#todo: fix all linter errors, deprecations
analyze:
	./vendor/bin/phplint
	./vendor/bin/phpstan --memory-limit=-1
	./vendor/bin/psalm --no-cache $(ARGS)
	./vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no

composer-install:
	composer install

composer-dump:
	composer dump-autoload

composer-update:
	composer update

composer-outdated:
	composer outdated
