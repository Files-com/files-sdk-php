#!/usr/bin/env bash
#!/usr/bin/env bash
DEFAULT_PHP_VERSION="$(php --version)"
re='[0-9]+\.[0-9]+'; 
PHP_DETECTED_VERS="5.6";
ran_php_tests=()
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

exists()
{
  command -v "$1" >/dev/null 2>&1
}

run_php_vers()
{
  echo "======= RUNNING UNIT TESTS FOR: $1 ======="
  cd "${DIR}/test" || exit 1 # Force the path
  rm composer.lock
  "$1" "${DIR}/composer.phar" install 
  "$1" ./vendor/bin/phpunit --testsuite default || exit 1
}
# Detect existing version
if [[ $DEFAULT_PHP_VERSION =~ $re ]]; then
  PHP_DETECTED_VERS="${BASH_REMATCH[0]}"; 
fi
if [[ -z ${CI_COMMIT_REF_NAME} ]]; then
  CI_COMMIT_REF_NAME=""
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

## Check composer install main project
cd "${DIR}/" || exit 1
wget wget -nc https://getcomposer.org/download/2.2.21/composer.phar
php composer.phar install  || exit 1


## Configure tests
cd "${DIR}/test" || exit 1

# Run if we have a default php version
if command -v php &> /dev/null; then
  run_php_vers php
  ran_php_tests+=("php$PHP_DETECTED_VERS")
fi

# Run PHP5.6
if exists php5.6 && [[ "${PHP_DETECTED_VERS}" != "5.6" ]]; then
  run_php_vers php5.6
  ran_php_tests+=("php5.6")
fi
# Run PHP7.0
if exists php7.0 && [[ "${PHP_DETECTED_VERS}" != "7.0" ]]; then
  run_php_vers php7.0
  ran_php_tests+=("php7.0")
fi
# Run PHP8.2
if exists php8.2 && [[ "${PHP_DETECTED_VERS}" != "8.2" ]]; then
  run_php_vers php8.2
  ran_php_tests+=("php8.2")
fi

if [ ${#ran_php_tests[*]} -eq 0 ] && [[ "${CI_COMMIT_REF_NAME}" == "" ]]; then
  echo "PHP Tests failed to run"; exit 1
elif ! [ ${#ran_php_tests[*]} -eq 3 ]; then
  echo "PHP Tests ran only for versions:"
  echo "${#ran_php_tests[*]}"
  ( IFS=$'\n'; echo "${ran_php_tests[*]}" )
  exit 1
else
  echo "PHP Tests ran for versions:"
  ( IFS=$'\n'; echo "${ran_php_tests[*]}" )
fi


exit $?
