# customization

PHPUNIT = vendor/bin/phpunit

# do not edit the following lines

vendor:
	@composer install

# testing

.PHONY: test-dependencies
test-dependencies: vendor test-cleanup

.PHONY: test
test: test-dependencies
	@$(PHPUNIT) $(ARGS)

.PHONY: test-coverage
test-coverage: test-dependencies
	@mkdir -p build/coverage
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html build/coverage

.PHONY: test-coveralls
test-coveralls: test-dependencies
	@mkdir -p build/logs
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-clover build/logs/clover.xml

.PHONY: test-cleanup
test-cleanup:
	@rm -rf tests/sandbox/*

.PHONY: test-container
test-container: test-container-82

.PHONY: test-container-82
test-container-82:
	@-docker compose run --rm app82 bash
	@docker compose down -v

.PHONY: test-container-85
test-container-85:
	@-docker compose run --rm app85 bash
	@docker compose down -v

.PHONY: lint
lint:
	@XDEBUG_MODE=off php-cs-fixer check --diff
	@XDEBUG_MODE=off vendor/bin/phpstan
