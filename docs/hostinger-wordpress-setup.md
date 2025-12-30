# Calculent.com Hostinger + WordPress Delivery Plan

This playbook outlines how to ship **calculent.com** on Hostinger Premium with WordPress, the Astra-based Calculent child theme (zip/bz2), a companion plugin zip, Elementor Free, shortcode wiring, and all required calculator pages.

## Hosting and WordPress baseline
- **Hostinger Premium**: create the site, enable free SSL, and point the domain to Hostinger nameservers.
- **WordPress install**: one-click install via Hostinger hPanel; set language/timezone, disable sample content, and enforce strong admin credentials.
- **Caching/CDN**: turn on Hostinger cache + CDN; add page caching and server-level Brotli/Gzip (keep plugin stack minimal). Enable HTTP/3/QUIC, preconnect to CDN, and serve WebP/AVIF assets via image optimization.
- **Backups**: enable daily backups and database snapshots before theme/plugin uploads.

## Theme and plugin packages
- **Astra parent theme**: install from WordPress directory (no customization here).
- **Calculent child theme**: upload the provided `calculent-child.zip` (or `calculent-child.tar.bz2` if supplied) via **Appearance → Themes → Add New → Upload**, then activate. This inherits Astra defaults while keeping Calculent styles and critical CSS.
- **Calculent tools plugin**: upload `calculent-tools.zip` via **Plugins → Add New → Upload**; it should register the unified `[calculent_tool type="{slug}"]` shortcode and enqueue lightweight calculator assets.
- **Elementor Free**: install/activate only the free builder; avoid Pro to keep the stack free and compliance-friendly.

## Elementor + shortcode wiring
- Use Elementor’s **Shortcode** widget to drop `[calculent_tool type="{slug}"]` inside hero/section containers.
- Apply the color palette and spacing from `docs/design-and-shortcode-guide.md` via Elementor **Site Settings → Global Colors/Typography**.
- Keep per-page CSS in the child theme or Elementor Custom CSS; avoid inline styles. Set responsive breakpoints consistent with Astra defaults.
- Enable **global performance features**: lazy-load images/backgrounds, preload critical CSS, defer non-essential JS, and enable Elementor’s experiment toggles for improved DOM output.
- Add **dark mode** toggle and PWA manifest/service worker via the child theme to support install prompts and offline FAQs/how-to content.
- Provide **skeleton loaders** on calculator sections and prefetch popular tool pages to make navigation feel instant.
- Layer the **Calculent Prisma look**: ship a small child-theme CSS file that adds blurred gradient glows, glass cards, neon CTA buttons, and category accent bars so every Elementor page inherits the signature “catchy” vibe without per-page tweaks.

## Required site pages
Create these pages before importing any JSON templates so menus and breadcrumbs resolve correctly:

1. **Home** – hero search, featured tools grid, categories, testimonials, FAQ, compliance cards, CTA.
2. **All Tools** – searchable/filterable list pulling every shortcode slug.
3. **Categories** – finance, utility, productivity, health, math/science, marketing; each links to its calculators.
4. **Calculator detail pages** – one per slug (see “Tool slugs and page requirements” below).
5. **About** – mission, privacy-by-design stance, lightweight tech stack summary.
6. **Blog/Insights** – optional SEO content hub; use Astra’s blog layout.
7. **Contact** – form using native HTML or lightweight plugin; no third-party trackers.
8. **Legal** – Privacy Policy, Terms, Disclaimer; link in footer and hero CTA buttons if needed.
9. **Sitemap** – XML via WordPress SEO plugin; HTML sitemap page for users.

## Tool slugs and page requirements
Every calculator page must include:
- Hero (`H1` + supporting copy) and calculator embed via shortcode.
- A 3–5 step **How to use** list before the form.
- **FAQ** accordion with JSON-LD `FAQPage`.
- **Compliance & data use** note (no PII stored, no cookies required, client-side math).
- **Precautions**/caveats list (“estimates only,” “consult a pro,” etc.).
- **Sharing** controls (Copy link + Share result buttons near the result card).
- **Download PDF** action for current inputs/results (print-styled or `jsPDF`).
- **Colorful live results** card that updates as inputs change.
- Required fields marked and validated before enabling Calculate.
- Performance hooks: keep each tool under 50KB JS, lazy-load heavier calculators after interaction, and cache shortcode responses at the edge.
- Trend-ready features: dark-mode-friendly charts, optional voice input for key fields, AI-assisted tips in tooltips, and cross-link related calculators.

Example shortcode placement: `[calculent_tool type="mortgage"]`.

### Finance & Business
- mortgage, emi, loan-compare, savings, retirement, roi, markup-margin, vat-gst, interest-compound, sip.

### Everyday & Utility
- tip, time, date-difference, age, discount, split-bill, unit-converter, currency, temperature, percentage, fraction-decimal.

### Writing & Productivity
- word-counter, character-counter, reading-time, lorem-ipsum, case-converter, password-generator.

### Health & Fitness
- bmi, bmr, calorie, hydration, heart-rate, pregnancy-due-date.

### Math & Science
- equation-solver, statistics-mean-median, triangle, circle, area-volume, unit-physics.

### Web & Marketing
- utm-builder, meta-preview, slugify, schema-markup, redirect-checker.

## Deployment checklist (Hostinger → live)
1. Upload/activate Astra + Calculent child; verify child theme stylesheet loads.
2. Upload/activate Calculent tools plugin; confirm shortcode renders a sample tool.
3. Install Elementor Free; import saved templates or rebuild sections per `design-and-shortcode-guide.md`.
4. Create menus linking Home, All Tools, Categories, Blog, Legal, Contact.
5. Generate required pages and assign calculator slugs; ensure pretty permalinks are enabled.
6. Add caching rules, lazy loading, and `defer` scripts via child theme functions if not provided by host.
7. Validate Core Web Vitals in PageSpeed; remove any heavy plugins before launch.
8. Submit XML sitemap to Search Console; add robots.txt allowing `/tools/*` and disallowing admin paths.

## Backup/export notes
- Keep both `calculent-child.zip` (or `.tar.bz2`) and `calculent-tools.zip` under version control and in Hostinger backups.
- Export Elementor templates (JSON) after building the Home, All Tools, and a sample calculator page for quick restores.
- Document shortcode→page mapping inside the plugin readme for maintenance.
