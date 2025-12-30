# Calculent ready-to-upload packages

The `dist` folder now ships only the ready-to-upload folders generated from the repository sources:

- `calculent-child/` — drop-in Astra child theme folder.
- `calculent-logic/` — drop-in Calculent Logic plugin folder.

Refresh the ready-to-upload folders after making changes by running:

```bash
./scripts/package.sh
```

Need ZIP archives for distribution? Generate them on-demand (they are ignored by default) with:

```bash
CREATE_ZIP=1 ./scripts/package.sh
```

The script copies from `wp-content/themes/calculent-child` and `wp-content/plugins/calculent-logic` so all distributable assets stay in sync with the codebase.
