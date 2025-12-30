#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="$ROOT_DIR/dist"
THEME_DIR="$ROOT_DIR/wp-content/themes/calculent-child"
PLUGIN_DIR="$ROOT_DIR/wp-content/plugins/calculent-logic"
CREATE_ZIP="${CREATE_ZIP:-1}"

function ensure_exists() {
  local path="$1"
  local label="$2"
  if [[ ! -d "$path" ]]; then
    echo "Expected $label directory at $path" >&2
    exit 1
  fi
}

function package_dir() {
  local source_dir="$1"
  local output_zip="$2"
  local base
  base="$(basename "$source_dir")"
  echo "Creating $(basename "$output_zip") from $base" >&2
  (cd "$(dirname "$source_dir")" && zip -rq "$output_zip" "$base")
}

function sync_directory() {
  local source_dir="$1"
  local dest_dir="$2"
  local name
  name="$(basename "$dest_dir")"
  echo "Copying $name as a ready-to-upload folder" >&2
  rm -rf "$dest_dir"
  mkdir -p "$dest_dir"
  cp -a "$source_dir/." "$dest_dir/"
}

ensure_exists "$THEME_DIR" "theme"
ensure_exists "$PLUGIN_DIR" "plugin"
if [[ ! -f "$THEME_DIR/style.css" ]]; then
  echo "Expected theme stylesheet at $THEME_DIR/style.css (WordPress will reject the upload without it)" >&2
  exit 1
fi

mkdir -p "$DIST_DIR"
echo "Creating ZIP archives (CREATE_ZIP=$CREATE_ZIP)" >&2
rm -f "$DIST_DIR"/calculent-child.zip "$DIST_DIR"/calculent-logic.zip
package_dir "$THEME_DIR" "$DIST_DIR/calculent-child.zip"
package_dir "$PLUGIN_DIR" "$DIST_DIR/calculent-logic.zip"

sync_directory "$THEME_DIR" "$DIST_DIR/calculent-child"
sync_directory "$PLUGIN_DIR" "$DIST_DIR/calculent-logic"

echo "All packages created under $DIST_DIR" >&2
