#!/usr/bin/env bash
# Runs the generated API scenario tests (test/scenarios, from verification/scenarios/api.json) with one PHP against
# the simulator generated from the same schema, through the mock server's own launcher, with-scenario.rb, which starts
# it on a free loopback port, gives the tests its address, exports its journal and stops it whether or not they
# passed. test/scenarios/account.php then checks the run, and this fails unless every generated case was reported
# once, its own record shows every step it carries out passed, and each case with steps this binding does not carry out
# ended incomplete naming exactly those steps:
#
#   api-scenarios.sh PHP OUT
#
# FILES_API_SCENARIOS_MOCK_SERVER names the generated mock-server directory (simulation contract 3) with its bundle
# installed; without it, its launcher or its generation receipt this fails before anything runs. test/vendor must hold
# this SDK's test dependencies for PHP (test.sh's composer install). OUT, which must not exist, receives the launcher's
# evidence (run.json, journal.json, server.log, ready.json, reset.json), PHPUnit's JUnit report (phpunit.junit.xml),
# each scenario's own record (scenarios/) and the account (account.json).
#
# Then, with the same PHP, launcher and preset, it runs four of the manual integration tests against a simulator of
# their own (test/integration/test-file.php --shared-simulator: creating, finding and deleting a user both ways,
# creating, listing and deleting a folder, and uploading a file and data with mkdir_parents into missing folders) and
# checks that run (test-file.php --check-shared-simulator). OUT-integration, which must not exist either, receives that
# launcher's evidence and the tests' report (integration.json). This fails when either run or check does, with the
# first failure's status.

php_bin="${1:?usage: api-scenarios.sh PHP OUT}"
out="${2:?usage: api-scenarios.sh PHP OUT}"
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
mock="${FILES_API_SCENARIOS_MOCK_SERVER:-}"

if [[ -z "${mock}" || ! -f "${mock}/with-scenario.rb" || ! -f "${mock}/lib/simulation/generation.json" ]]; then
  echo "FILES_API_SCENARIOS_MOCK_SERVER must name the generated simulator (the mock-server target) the API scenarios run against" >&2
  exit 1
fi
if [[ -e "${out}" ]]; then
  echo "${out} already exists; give a new directory." >&2
  exit 1
fi
mkdir -p "$(dirname "${out}")" || exit 1
out="$(cd "$(dirname "${out}")" && pwd)/$(basename "${out}")"
integration_out="${out}-integration"
if [[ -e "${integration_out}" ]]; then
  echo "${integration_out} already exists; give a new directory." >&2
  exit 1
fi

cd "${DIR}/test" || exit 1
status=0
FILES_SESSION_ENV="development" ruby "${mock}/with-scenario.rb" --scenario api-scenarios-canonical --out "${out}" --consumer-timeout 900 -- \
  "${php_bin}" ./vendor/bin/phpunit --testsuite api-scenarios --log-junit "${out}/phpunit.junit.xml" || status=$?
FILES_SESSION_ENV="development" "${php_bin}" scenarios/account.php "${out}" "${mock}/lib/simulation/generation.json" || status=$((status == 0 ? 1 : status))
FILES_SESSION_ENV="development" ruby "${mock}/with-scenario.rb" --scenario api-scenarios-canonical --out "${integration_out}" \
  --startup-timeout 30 --consumer-timeout 180 --settle-timeout 5 -- \
  "${php_bin}" integration/test-file.php --shared-simulator || status=$((status == 0 ? $? : status))
FILES_SESSION_ENV="development" "${php_bin}" integration/test-file.php --check-shared-simulator "${integration_out}" || status=$((status == 0 ? 1 : status))
exit "${status}"
