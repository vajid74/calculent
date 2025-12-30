# Calculent Design & Elementor Shortcode Guide

This guide captures the exact visual design cues and color palette from `calculent-latest-4.html`, and proposes shortcode patterns you can wire into Elementor widgets.

## Color palette
Extracted from the `:root` variables and supporting accents in the HTML:

- Primary purple: `#7C3AED` (hover `#6D28D9`)
- Secondary orange: `#FF6347` (hover `#E5533A`)
- Hero gradient: `linear-gradient(135deg, #FF1493 0%, #BA55D3 50%, #4169E1 100%)`
- Advanced gradient: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- Card glass gradient: `linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%)`
- Light background: `#F9F9F7`
- Dark background: `#1a1a2e`
- Accent greens: `#10B981`, `#059669`
- Accent cyan: `#06B6D4`
- Accent blue: `#4169E1`
- Accent yellow/gold: `#F59E0B`, `#FFD700`, `#FFA500`
- Accent red/pink: `#EF4444`, `#ff4757`

Use these values directly in Elementor global styles to mirror the HTML without guessing.

### Signature "Calculent Prisma" look (catchier, more distinctive)
- Pair the hero gradient with a **dual-angle glow**: set a pseudo-element behind cards using `filter: blur(32px)` and `background: radial-gradient(circle at 20% 20%, rgba(124,58,237,.25), transparent 45%), radial-gradient(circle at 80% 30%, rgba(255,99,71,.25), transparent 40%)`.
- Add **neumorphic glass** surfaces: `backdrop-filter: blur(18px)` and `border: 1px solid rgba(255,255,255,.08)` on hero search bars and live result cards.
- Use **micro-gradient strokes** on buttons: `background: linear-gradient(120deg, #FF6347, #7C3AED, #06B6D4)` with `box-shadow: 0 12px 30px rgba(124,58,237,.28)` for a premium, poppy CTA.
- Sprinkle **animated accent orbs** (SVG blobs or CSS shapes) with slow `transform: translateY(-6px)` hover to keep the page feeling alive while remaining lightweight.
- Typography: mix a **rounded geometric** heading (e.g., Manrope/Inter) with system-body text; clamp `font-size` to prevent jumps across breakpoints.

## Layout overview
The HTML implements a modern, sectioned landing page that you can recreate in Elementor using containers or sections:

1. **Sticky header** with blurred background, gradient logo text, nav links, and paired primary/secondary buttons.
2. **Hero** featuring the hero gradient background, floating SVG icons, headline, supporting copy, live search bar, and dual CTA buttons.
3. **Featured calculators** grid with animated cards, colored top borders (blue/green/purple/cyan/orange/yellow), SVG icons, title, description, and “Use Tool” links.
4. **Categories** grid of soft gradient boxes with icons and hover lift.
5. **About** two-column layout with feature bullets and icon chips.
6. **Compliance** four-column card grid with top-accent borders and badges.
7. **Testimonials**, **FAQ accordion**, and **CTA band** with gradient background and paired buttons.
8. **Footer** with logo treatment, navigation, newsletter field, and social icons.

Mimic the spacing (`padding: 8rem 2rem` on most sections) and rounded corners (cards `border-radius: 28px`) for fidelity.

### Elementor build notes for a catchier UI
- **Hero**: stack a blurred gradient canvas behind a 2-column layout; add floating icon chips with subtle parallax (`transform: translate3d(var(--x), var(--y), 0)`) and a glass search bar with pill buttons. Use a split CTA (primary gradient + ghost secondary) to mirror the HTML punchiness.
- **Cards**: apply a **top accent bar** (4–6px) that inherits the category color, and animate `box-shadow`/`translateY` on hover (`transition: 220ms ease`).
- **Category chips**: use soft gradients with `mix-blend-mode: screen` and an inner border to get a candy look without heavy assets.
- **Result blocks**: wrap in a glass card with an animated gradient stripe on the left; show real-time numbers with count-up micro-animation for “colorful live results.”
- **Dark mode**: invert backgrounds to `#0b1021`/`#111827`, keep neon gradients, and bump contrast on text/buttons; store theme state in `localStorage` and sync with `prefers-color-scheme`.

## Elementor-friendly shortcode plan
Wire each calculator/converter into Elementor using a unified shortcode handler so pages stay reusable. Map the existing tools from the HTML to shortcode `type` values:

- `[calculent_tool type="mortgage"]`
- `[calculent_tool type="bmi"]`
- `[calculent_tool type="password-generator"]`
- `[calculent_tool type="temperature"]`
- `[calculent_tool type="tip"]`
- `[calculent_tool type="word-counter"]`
- `[calculent_tool type="unit-converter"]`
- `[calculent_tool type="currency"]`
- `[calculent_tool type="emi"]`
- `[calculent_tool type="time"]`
- `[calculent_tool type="finance"]`
- `[calculent_tool type="health"]`

Implementation sketch:

1. Register a single shortcode in your plugin (`add_shortcode('calculent_tool', ...)`) that renders the requested calculator template based on `type`.
2. Expose the shortcode in Elementor by placing a “Shortcode” widget inside the relevant card or section.
3. For global search/live filtering, output a hidden JSON manifest of calculator names/types inside the shortcode so the existing search bar can query it.
4. Keep CSS in the child theme (or Elementor custom CSS) using the palette above; avoid inline styles so Elementor theming remains centralized.

Use Elementor’s container/column widgets to mirror the structure and drop the shortcodes where each “Use Tool →” link should display the interactive tool.

## Performance, compliance-free, and SEO checklist
- **Licensing:** keep the plugin/theme GPL-only; avoid bundling commercial fonts, icons, or SDKs. Use system fonts or open-source sets (e.g., Font Awesome Free, Heroicons) and self-hosted assets to keep Calculent entirely free for users.
- **Performance-first UI:**
  - Inline critical CSS for the above-the-fold hero, defer the rest in the child theme stylesheet.
  - Prefer native form elements for calculators; keep DOM shallow and avoid nested wrappers to reduce layout cost.
  - Load icons as inline SVG sprites; avoid icon fonts.
  - Defer non-essential JavaScript and enqueue scripts with `defer`/`type=module`; keep each calculator in small, modular files.
  - Add `loading="lazy"` to images/backgrounds and avoid heavy background videos.
  - Ship WebP/AVIF assets, serve via CDN with HTTP/3, and add `preconnect` + `dns-prefetch` for any remote APIs.
  - Prefetch popular calculator pages on hover (`prefetch`/`prerender`) and keep shortcode outputs cacheable with `Cache-Control` + `ETag`.
  - Use skeleton loaders and optimistic UI for calculator results so pages feel instant even on slow networks.
- **Compliance-friendly defaults:**
  - Keep tracking off by default; if analytics are required, provide a toggleable consent banner and avoid storing PII.
  - Use semantic HTML (`<main>`, `<section>`, `<nav>`, `<button>`, `<label>`, `<input>`) with accessible names; ensure focus states use the palette (e.g., `outline: 2px solid #7C3AED`).
  - Prefer privacy-friendly fonts (system stack) to avoid third-party calls.
- **SEO-focused structure:**
  - One `<h1>` in the hero, `<h2>` for sections, descriptive meta title/description per calculator page.
  - Add JSON-LD `FAQPage` schema for the FAQ accordion and `BreadcrumbList` for category pages.
  - Provide clean slugs (`/tools/{type}`) that match shortcode `type` values and use canonical links when embedding tools in multiple contexts.
  - Include fast-loading OG/Twitter images (1200x630) for the hero and category pages.
  - Generate structured data for key calculators (e.g., mortgage/loan calculator `FinancialService`) when relevant to future-proof SEO.

## High-performance + trend-ready feature set (apply sitewide)
- **Instant load + Core Web Vitals**: limit total JS per page, leverage HTTP/2 push equivalents (preload critical CSS/fonts), and avoid layout shifts with fixed media dimensions and clamp-based typography.
- **Dark mode and theme sync**: offer a toggle tied to `prefers-color-scheme`, reusing the palette with accessible contrast.
- **PWA-lite**: add a manifest, install prompt, and offline fallback for read-only pages (FAQs/how-to); cache calculator assets for quick repeat visits.
- **Edge caching**: serve static assets and shortcode responses through CDN; enable Brotli compression and stale-while-revalidate for regenerating lists (All Tools/Categories).
- **Modern UX hooks**: voice input for key fields (e.g., time/temperature), clipboard buttons, and one-click share via Web Share API.
- **Accessibility-first**: reduced motion mode, high-contrast focus rings, and ARIA labels on all form controls.
- **Analytics-light**: if metrics are needed, use first-party, cookieless analytics (e.g., self-hosted Plausible/Umami) with consent gating.
- **Security hygiene**: CSP with allowed origins for assets, `rel="noopener"` on outbound links, and nonce-based script enqueueing in the plugin.

## Expanded calculator taxonomy (shortcode-ready)
Use these types to maximize coverage and organize Elementor sections by category. All follow the same shortcode shape: `[calculent_tool type="{slug}"]`.

**Finance & Business**
- `mortgage`, `emi`, `loan-compare`, `savings`, `retirement`, `roi`, `markup-margin`, `vat-gst`, `interest-compound`, `sip`.

**Everyday & Utility**
- `tip`, `time`, `date-difference`, `age`, `discount`, `split-bill`, `unit-converter`, `currency`, `temperature`, `percentage`, `fraction-decimal`.

**Writing & Productivity**
- `word-counter`, `character-counter`, `reading-time`, `lorem-ipsum`, `case-converter`, `password-generator`.

**Health & Fitness**
- `bmi`, `bmr`, `calorie`, `hydration`, `heart-rate`, `pregnancy-due-date`.

**Math & Science**
- `equation-solver`, `statistics-mean-median`, `triangle`, `circle`, `area-volume`, `unit-physics`.

**Web & Marketing**
- `utm-builder`, `meta-preview`, `slugify`, `schema-markup`, `redirect-checker`.

**SEO-friendly templates**
- For each slug, pair a dedicated Elementor page with: hero copy, calculator embed shortcode, FAQ block, and a short “How it works” list. Reuse global colors and spacing so the UI stays lightweight and consistent.

## Per-tool UI requirements (apply to every calculator page)
- **How-to steps**: Add a concise 3–5 step “How to use” list above the calculator or in a left column so users understand inputs before interacting.
- **FAQ + schema**: Keep an accordion with at least 3 questions; output JSON-LD `FAQPage` for SEO.
- **Compliance note**: Include a “Compliance & data use” callout that states no PII is stored, cookies are not required, and calculations run locally.
- **Precautions**: Provide a short bullet list of caveats (e.g., “values are estimates,” “consult a professional,” “rounding may vary by locale”).
- **Sharing**: Add “Copy link” and “Share result” buttons near the result card; use the palette accents and native `navigator.share` fallback to mail/clipboard.
- **Downloadable results**: Include a “Download PDF” button that exports the current inputs/results using a lightweight client library (e.g., `window.print()` styled for PDF or `jsPDF`).
- **Colorful live results**: Render the primary output in a bright card using the palette (e.g., gradient header + accent border) and update values live as inputs change; add subtle micro-animations on change for responsiveness.
- **Required inputs**: Mark compulsory inputs with an asterisk, provide inline validation, and disable “Calculate” until required fields are complete.
- **Performance hooks**: keep each tool under 50KB JS, lazy-load heavy calculators after interaction, and ship precomputed presets so the first render is instant.
- **Trend features**: provide dark-mode-friendly charts, optional voice input, AI-assisted explanations/tooltips where appropriate, and cross-link “related calculators” to keep users exploring.
