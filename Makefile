vendor: composer.phar
	@php composer.phar install --prefer-source --dev

composer.phar:
	@echo "Installing composer..."
	@curl -s https://getcomposer.org/installer | php

update: vendor
	@php composer.phar update --prefer-source --dev

autoload: vendor
	@php composer.phar dump-autoload

test: vendor
	@phpunit

doc: vendor
	@mkdir -p "docs"

	@apigen \
	--source ./ \
	--destination docs/ --title ICanBoogie/DateTime \
	--exclude "*/composer/*" \
	--exclude "*/tests/*" \
	--template-config /usr/share/php/data/ApiGen/templates/bootstrap/config.neon

clean:
	@rm -fR docs
	@rm -fR vendor
	@rm -f composer.lock
	@rm -f composer.phar