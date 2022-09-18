phpmetrics:
	./vendor/bin/phpmetrics --report-html=var/myreport ./src

lint:
	composer lint
	composer phpcs-check

lint-autofix:
	composer phpcs-fix

analyze:
	composer phpstan
	composer psalm

composer-install:
	composer install

composer-dump:
	composer dump-autoload

composer-update:
	composer update

composer-outdated:
	composer outdated
