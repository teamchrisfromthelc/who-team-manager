#!/usr/bin/env bash
#
# Verify that every version string in the plugin agrees.
#
# Usage: bin/check-version.sh [expected-version]
#
# Prints the resolved version on stdout. Exits non-zero when the sources
# disagree, when the version is not MAJOR.MINOR.PATCH, when readme.txt has no
# changelog entry for it, or when it differs from the optional argument (for
# example, a git tag with its leading "v" removed).

set -euo pipefail

cd "$(dirname "$0")/.."

slug="who-team-manager"

# Read one value with a sed expression. Fails if the pattern is missing.
read_value() {
	local file="$1" expr="$2" value
	value="$(sed -nE "$expr" "$file" | head -n 1 | tr -d '[:space:]')"
	if [[ -z "$value" ]]; then
		echo "error: could not read version from $file" >&2
		exit 1
	fi
	printf '%s' "$value"
}

header="$(read_value "$slug.php" 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*(.+)$/\1/p')"
constant="$(read_value "$slug.php" "s/^define\([[:space:]]*'WHO_TEAM_MANAGER_VERSION',[[:space:]]*'([^']+)'.*/\1/p")"
stable_tag="$(read_value readme.txt 's/^Stable tag:[[:space:]]*(.+)$/\1/p')"
package_json="$(read_value package.json 's/^[[:space:]]*"version":[[:space:]]*"([^"]+)".*/\1/p')"
block_json="$(read_value src/block.json 's/^[[:space:]]*"version":[[:space:]]*"([^"]+)".*/\1/p')"

version="$header"
status=0

check() {
	local label="$1" value="$2"
	if [[ "$value" != "$version" ]]; then
		echo "error: $label is $value, plugin header is $version" >&2
		status=1
	fi
}

check "WHO_TEAM_MANAGER_VERSION" "$constant"
check "readme.txt Stable tag" "$stable_tag"
check "package.json version" "$package_json"
check "src/block.json version" "$block_json"

if [[ ! "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
	echo "error: version $version is not MAJOR.MINOR.PATCH" >&2
	status=1
fi

if ! grep -qxF "= $version =" readme.txt; then
	echo "error: readme.txt has no changelog entry \"= $version =\"" >&2
	status=1
fi

if [[ $# -gt 0 && "$1" != "$version" ]]; then
	echo "error: expected version $1, plugin header is $version" >&2
	status=1
fi

if [[ $status -ne 0 ]]; then
	exit "$status"
fi

echo "$version"
