#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")"

php_executable="${PHPDOC_PHP:-php}"

# A normal build checks the SDK source on the selected PHP runtime.
if [[ $# -eq 0 ]]; then
  while IFS= read -r -d '' source; do
    "${php_executable}" -l "${source}" > /dev/null
  done < <(find lib -name '*.php' -print0)
  echo "PHP source syntax passed. Build the native API reference with ./build.sh --docs."
  exit 0
fi

# Documentation tools run separately from the PHP 5.x-compatible SDK and its dependencies.
if [[ "${1:-}" != "--docs" || $# -ne 1 ]]; then
  echo "Usage: ./build.sh --docs" >&2
  exit 2
fi

phpdoc_executable="${PHPDOC_BIN:-tools/phpdoc/vendor/bin/phpdoc}"
if [[ ! -f "${phpdoc_executable}" ]]; then
  echo "Install the isolated documentation tools first: composer --working-dir=tools/phpdoc install" >&2
  echo "Alternatively, set PHPDOC_BIN to an installed phpDocumentor 3 executable or PHAR." >&2
  exit 1
fi

"${php_executable}" "${phpdoc_executable}" run --config=phpdoc.dist.xml
