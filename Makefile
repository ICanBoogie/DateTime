# customization

PACKAGE_NAME = ICanBoogie/DateTime
PHPUNIT = vendor/bin/phpunit

# do not edit the following lines

vendor:
	@composer install

.PHONY: test-dependencies
test-dependencies: vendor

.PHONY: test
test: test-dependencies
	@$(PHPUNIT)

.PHONY: test-coverage
test-coverage: test-dependencies
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage

.PHONY: test-coveralls
test-coveralls: test-dependencies
	@mkdir -p build/logs
	@$(PHPUNIT) --coverage-clover build/logs/clover.xml
