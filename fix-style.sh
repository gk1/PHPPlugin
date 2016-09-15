#!/usr/bin/env bash

for dir in src tests
do
    php -dxdebug.remote_enable=0 ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix ${dir} --level=symfony
    php -dxdebug.remote_enable=0 ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix ${dir} --fixers=align_double_arrow,ereg_to_preg,short_array_syntax
done
