#!/usr/bin/env bash
#
# Package the plugin as an installable WordPress ZIP.
#
# Usage: bin/build-zip.sh
#
# Expects compiled assets in build/ (run `npm run build` first). Writes
# dist/who-team-manager.zip with a single top-level who-team-manager/ folder,
# which WordPress uses as the plugin directory name. The folder name must stay
# fixed so the GitHub updater keeps matching who-team-manager/who-team-manager.php.
#
# Files are copied from an allowlist so dev tooling never ships by accident.

set -euo pipefail

cd "$(dirname "$0")/.."

slug="who-team-manager"
dist_dir="dist"
stage_dir="$dist_dir/$slug"
zip_path="$dist_dir/$slug.zip"

# Paths shipped in the ZIP, relative to the repo root.
include=(
	"$slug.php"
	uninstall.php
	readme.txt
	LICENSE
	includes
	build
)

# Files the plugin cannot run without. Checked after staging.
required=(
	"$slug.php"
	includes/class-plugin.php
	build/block.json
	build/index.js
	build/index.asset.php
	build/plugins/job-title-panel.js
	build/plugins/job-title-panel.asset.php
)

for path in "${include[@]}"; do
	if [[ ! -e "$path" ]]; then
		echo "error: $path is missing. Run \`npm run build\` before packaging." >&2
		exit 1
	fi
done

rm -rf "$stage_dir" "$zip_path"
mkdir -p "$stage_dir"

for path in "${include[@]}"; do
	cp -R "$path" "$stage_dir/"
done

# Drop OS and editor debris that cp may have picked up.
find "$stage_dir" \( -name '.DS_Store' -o -name '._*' -o -name '*.map' \) -delete

for path in "${required[@]}"; do
	if [[ ! -f "$stage_dir/$path" ]]; then
		echo "error: $path is missing from the package" >&2
		exit 1
	fi
done

(
	cd "$dist_dir"
	# -X drops extra file attributes (uid/gid, macOS metadata).
	zip -rqX "$slug.zip" "$slug"
)

rm -rf "$stage_dir"

echo "Built $zip_path"
unzip -l "$zip_path"
