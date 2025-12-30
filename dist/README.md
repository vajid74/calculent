# Calculent ready-to-upload packages

The `dist` folder now contains only the files needed by WordPress:

- `calculent-child/` — Astra child theme folder. Compress **this folder itself** to install via Appearance → Themes, or upload the folder over SFTP.
- `calculent-logic/` — plugin folder. Compress **this folder itself** to install via Plugins → Add New, or upload the folder over SFTP.

Zip archives are gitignored; if you need fresh zips, run:

```bash
./scripts/package.sh
```
