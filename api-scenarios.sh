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
# The scenario tests load test/vendor/autoload.php (--bootstrap) instead of the default suite's bootstrap.php, which
# would clear the FILES_* settings the launcher gives them.
#
# Then, with the same PHP, launcher and preset, it runs the eleven manual integration tests other than testSession
# against a simulator of their own (test/integration/test-file.php --shared-simulator: configuration, missing-path and
# root-folder errors, user pagination and list/update, creating, finding and deleting a user both ways, creating,
# listing and deleting a folder, uploading/finding/copying/deleting a file and data, ranged and resumed downloads with
# download options and a metadata update, uploading a file and data with mkdir_parents into missing folders, a file's
# priority color readback, and the Accept-Language header of a Spanish API key listing) and checks that run
# (test-file.php --check-shared-simulator). OUT-integration, which must not exist either, receives that
# launcher's evidence and the tests' report (integration.json).
#
# Last, with the same PHP and launcher, it runs test/shared-mock-users-find.php against the users-find preset, with
# FILES_SESSION_ENV unset so that the SDK loads from test/vendor as an installed copy does: it finds the preset's user
# through the public User::find and compares its id, username and name. OUT-users-find, which must not exist either,
# receives that launcher's evidence and the consumer's receipt (users-find-receipt.json).
#
# Then the shared failure-case matrix (the mock server's scenarios/failure-cases.json): with the same PHP and launcher,
# test/failure-cases.php makes each case's calls (enum and date-time validation errors, a dropped connection retried,
# retries spent on dropped connections, and a seeded random sequence of HTTP 422 faults, which no SDK retries) against
# a simulator that the launcher resets to
# the scenario's users and gives its fault rules. The launcher's failure cases check requires every case reported as
# passed, once and in order, and each case's journaled attempts and fault decisions to be exactly the expected ones.
# OUT-failure-cases, which must not exist either, receives that launcher's evidence, the fault rules' state
# (faults.json) and the consumer's results (failure-cases.json).
#
# This fails when any run or check does, with the first failure's status.

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
users_find_out="${out}-users-find"
if [[ -e "${users_find_out}" ]]; then
  echo "${users_find_out} already exists; give a new directory." >&2
  exit 1
fi
failure_cases_out="${out}-failure-cases"
if [[ -e "${failure_cases_out}" ]]; then
  echo "${failure_cases_out} already exists; give a new directory." >&2
  exit 1
fi

cd "${DIR}/test" || exit 1
status=0
FILES_SESSION_ENV="development" ruby "${mock}/with-scenario.rb" --scenario api-scenarios-canonical --out "${out}" --consumer-timeout 900 -- \
  "${php_bin}" ./vendor/bin/phpunit --bootstrap ./vendor/autoload.php --testsuite api-scenarios --log-junit "${out}/phpunit.junit.xml" || status=$?
FILES_SESSION_ENV="development" "${php_bin}" scenarios/account.php "${out}" "${mock}/lib/simulation/generation.json" || status=$((status == 0 ? 1 : status))
FILES_SESSION_ENV="development" ruby "${mock}/with-scenario.rb" --scenario api-scenarios-canonical --out "${integration_out}" \
  --startup-timeout 30 --consumer-timeout 180 --settle-timeout 5 -- \
  "${php_bin}" integration/test-file.php --shared-simulator || status=$((status == 0 ? $? : status))
FILES_SESSION_ENV="development" "${php_bin}" integration/test-file.php --check-shared-simulator "${integration_out}" || status=$((status == 0 ? 1 : status))
env -u FILES_SESSION_ENV ruby "${mock}/with-scenario.rb" --scenario users-find --out "${users_find_out}" \
  --startup-timeout 30 --consumer-timeout 180 --settle-timeout 5 -- \
  "${php_bin}" shared-mock-users-find.php || status=$((status == 0 ? $? : status))
FILES_SESSION_ENV="development" ruby "${mock}/with-scenario.rb" --scenario failure-cases --out "${failure_cases_out}" \
  --startup-timeout 30 --consumer-timeout 120 --settle-timeout 5 --failure-variant sdk-network-retries -- \
  "${php_bin}" failure-cases.php || status=$((status == 0 ? $? : status))
exit "${status}"
