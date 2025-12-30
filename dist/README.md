# Calculent ready-to-upload packages

The `dist` folder now ships only the ready-to-upload folders generated from the repository sources:

- `calculent-child/` — drop-in Astra child theme folder.
- `calculent-logic/` — drop-in Calculent Logic plugin folder.

Refresh the ready-to-upload folders after making changes by running:

```bash
./scripts/package.sh
```

ZIP archives for distribution are built automatically; rerun the script any time you need fresh `.zip` files for uploading into WordPress. The script copies from `wp-content/themes/calculent-child` and `wp-content/plugins/calculent-logic` so all distributable assets stay in sync with the codebase.
