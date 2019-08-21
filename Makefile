# customization

PACKAGE_NAME = ICanBoogie/DateTime
PACKAGE_VERSION = v1.2
PHPUNIT_VERSION = phpunit-5.7.phar
PHPUNIT_FILENAME = build/$(PHPUNIT_VERSION)
PHPUNIT = php $(PHPUNIT_FILENAME)

# do not edit the following lines

all: $(PHPUNIT_FILENAME) vendor

usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@composer install

update:
	@composer update

autoload: vendor
	@composer dump-autoload

test-dependencies: $(PHPUNIT_FILENAME) vendor

$(PHPUNIT_FILENAME):
	mkdir -p build
	wget https://phar.phpunit.de/$(PHPUNIT_VERSION) -O $(PHPUNIT_FILENAME)

test: test-dependencies
	@$(PHPUNIT)

test-coverage: test-dependencies
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage

test-coveralls: test-dependencies
	@mkdir -p build/logs
	composer require --dev php-coveralls/php-coveralls
	@$(PHPUNIT) --coverage-clover build/logs/clover.xml
	php vendor/bin/php-coveralls -v

doc: vendor
	@mkdir -p build/docs
	@apigen generate \
	--source lib \
	--destination build/docs/ \
	--title "$(PACKAGE_NAME) $(PACKAGE_VERSION)" \
	--template-theme "bootstrap"

clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock

.PHONY: all autoload doc clean test test-coverage test-coveralls update
