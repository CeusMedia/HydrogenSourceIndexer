install: composer-install

install-dev: composer-install-dev

composer-install:
	@test ! -f vendor/autoload.php && composer install --no-dev || true

composer-install-dev:
	@test ! -d vendor/phpunit/phpunit && composer install || true

composer-update:
	@composer update --no-dev

composer-update-dev:
	@composer update

dev-phpstan: composer-install-dev
	@vendor/bin/phpstan analyse --configuration phpstan.neon --xdebug || true

dev-phpstan-save-baseline: composer-install-dev
	@vendor/bin/phpstan analyse --configuration phpstan.neon --generate-baseline phpstan-baseline.neon || true

dev-test: composer-install-dev
	@vendor/bin/phpunit -v || true

dev-test-syntax:
	@find src -type f -print0 | xargs -0 -n1 xargs php -l
