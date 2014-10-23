vendor:
	@composer install --prefer-source --dev

update:
	@composer update --prefer-source --dev

autoload: vendor
	@composer dump-autoload

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

