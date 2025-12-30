# Calculent ready-to-upload packages

The `dist` folder ships ready-to-upload packages generated from the repository sources:

- `calculent-child/` — drop-in Astra child theme folder (upload the folder or compress yourself for the WordPress installer).
- `calculent-logic/` — drop-in Calculent Logic plugin folder (upload the folder or compress yourself for the WordPress installer).

Refresh the ready-to-upload folders after making changes by running:

```bash
./scripts/package.sh
```

ZIP archives are intentionally gitignored. If you need uploadable zips, run the packaging script locally to generate them.
