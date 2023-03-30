#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
## Configure base
cd "${DIR}" || exit 1
wget wget -nc https://getcomposer.org/download/2.2.21/composer.phar
php composer.phar install 
## Configure tests
cd "${DIR}/test" || exit 1
rm -Rf "${DIR}/test/vendor/files.com"
php "${DIR}/composer.phar" install 
./vendor/bin/phpunit --testsuite  default || exit 1

exit $?
