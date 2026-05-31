# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

A **pure static HTML website** for Modern LUXE Flowers, a Chicago luxury floral design studio. There is no build system, no package manager, and no framework. The entire site is hand-authored HTML/CSS/JS files deployed via **GitHub Pages** (the `CNAME` file points to `modernluxeflowers.com`). Pushing to `main` deploys automatically.

## Development

Open `index.html` directly in a browser to preview. There are no build steps, no `npm install`, and no dev server needed.

## Architecture

`index.html` is the homepage and currently the only file in the repo. All CSS lives in an inline `<style>` block in `<head>` and all JS lives in an inline `<script>` at the bottom of `<body>`. The site references several other pages via nav links that **do not yet exist** in this repo:

- `/flower-arrangements-portfolio.html`
- `/about-us-floral-design-business.html`
- `/client-reviews.html`
- `/corporate-floral-design-chicago.html`
- `/contact-us-floral-arrangements.html`
- `/luxe-love-unique-flower-arrangements.html`
- `/gift-giving-boxful-art.html`
- `/petals-and-stems.html`

When creating these pages, follow the same self-contained single-file pattern as `index.html`.

## Design system

CSS custom properties are defined on `:root` at the top of the `<style>` block:

```
--black: #0a0a0a  --dark: #111111  --surface: #161613  --border: #2a2820
--gold: #c9a96e   --gold-dim: #8a7048
--cream: #f5f0e8  --body: #d4cec4   --muted: #a89e90   --dim: #6b6658
```

**Typography:**
- `Cormorant Garamond` (serif, weight 300/400, italic variants) — all display text, headings, editorial copy
- `Jost` (sans-serif, weight 200–500) — nav, labels, UI elements, uppercase tracking text

**Responsive breakpoints:** 1024px (tablet), 768px (mobile), 480px (small mobile).

## Images

Two image sources are in use:

1. **Local** (`images/` directory) — product photos exported from WhatsApp. The hero background (`images/chicago-luxury-arrangement-hero.jpeg`) is served locally. New photos added to the repo go here.
2. **External CDN** (`assets.zyrosite.com`) — legacy images from the previous Zyrosite platform used for the designer photo, logo, and collection thumbnails. These can be replaced by swapping `src` URLs; HTML comments mark the exact swap points (e.g., `<!-- BACKGROUND PHOTO — replace src URL to change this photo -->`).

Images at the repo root (not inside `images/`) are duplicates of the `images/` folder contents and can be ignored.

## In-progress work

- The announcement banner (`#site-banner`) at the top of `index.html` includes an HTML comment: `<!-- delete this section when redesign is complete -->`. It should be removed once all pages are live and the redesign is complete.
- The designer photo and several collection images still point to the Zyrosite CDN and are pending replacement with locally hosted images.
