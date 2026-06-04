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

## Image numbering system

When the user says "replace image #N on [Page]" or "swap [Page] #N", use this map to identify the correct `<img>` tag. Numbers count only swappable content images (logos and footer logos are excluded). A full reference PDF is saved at `MLF-Image-Map.pdf` in the repo root.

### Homepage (`index.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Hero background | `images/chicago-luxury-arrangement-hero.jpeg` |
| 2 | Designer photo (About section) | Zyrosite CDN — img_4442 |
| 3 | Collections grid #1 — Luxe Love | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg` |
| 4 | Collections grid #2 — Boxful Art | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (7).jpeg` |
| 5 | Collections grid #3 — Petals & Stems | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (10).jpeg` |

### Corporate (`corporate-floral-design-chicago.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Hero background | `images/WhatsApp Image 2026-05-30 at 4.56.17 PM (3).jpeg` |
| 2 | Bottom panel — left | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (6).jpeg` |
| 3 | Bottom panel — center | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg` |
| 4 | Bottom panel — right | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (5).jpeg` |

### Weddings & Events (`weddings-and-events-floral-chicago.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Wedding trio — left | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM (1).jpeg` |
| 2 | Wedding trio — center | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM (2).jpeg` |
| 3 | Wedding trio — right | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (8).jpeg` |
| 4 | Events duo — left | `images/event-005.jpeg` |
| 5 | Events duo — right | `images/event-006.jpeg` |

### Luxe Love (`luxe-love-unique-flower-arrangements.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Mosaic hero (large, top left) | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (3).jpeg` |
| 2 | Bento wide top | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (6).jpeg` |
| 3 | Bento mid left | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (5).jpeg` |
| 4 | Bento mid right | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (9).jpeg` |
| 5 | Mosaic wide break (full-width strip) | `images/WhatsApp Image 2026-05-30 at 4.56.17 PM (3).jpeg` |
| 6 | Mosaic bottom 1 — left | `images/WhatsApp Image 2026-05-30 at 4.56.16 PM (3).jpeg` |
| 7 | Mosaic bottom 2 — center | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (11).jpeg` |
| 8 | Mosaic bottom 3 — right | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (7).jpeg` |

### Boxful Art (`gift-giving-boxful-art.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Gallery #1 | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (7).jpeg` |
| 2 | Gallery #2 | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM.jpeg` |
| 3 | Gallery #3 | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (3).jpeg` |
| 4 | Gallery #4 | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM.jpeg` |
| 5 | Gallery #5 | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (2).jpeg` |
| 6 | Gallery #6 | `images/WhatsApp Image 2026-05-30 at 4.56.17 PM.jpeg` |

### Petals & Stems (`petals-and-stems.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Gallery 1 — mosaic hero | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (10).jpeg` |
| 2 | Gallery 1 — bento 1 | `images/lapel-wedding-grey.jpeg` |
| 3 | Gallery 1 — bento 2 | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (8).jpeg` |
| 4 | Gallery 1 — bento 3 | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM (2).jpeg` |
| 5 | Gallery 1 — bento 4 | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM (4).jpeg` |
| 6 | Gallery 1 — bottom 1 | `images/WhatsApp Image 2026-05-30 at 4.56.16 PM (1).jpeg` |
| 7 | Gallery 1 — bottom 2 | `images/WhatsApp Image 2026-05-30 at 4.56.16 PM.jpeg` |
| 8 | Gallery 1 — bottom 3 | `images/WhatsApp Image 2026-05-30 at 4.56.13 PM (1).jpeg` |
| 9 | Gallery 2 — mosaic hero | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (1).jpeg` |
| 10 | Gallery 2 — bento 1 | `images/event-010.webp` |
| 11 | Gallery 2 — bento 2 | `images/event-008.webp` |
| 12 | Gallery 2 — bento 3 | `images/event-007.webp` |
| 13 | Gallery 2 — bento 4 | `images/WhatsApp Image 2026-05-30 at 4.56.15 PM (5).jpeg` |
| 14 | Gallery 2 — bottom 1 | `images/WhatsApp Image 2026-05-30 at 4.56.18 PM.jpeg` |
| 15 | Gallery 2 — bottom 2 | `images/WhatsApp Image 2026-05-30 at 4.56.14 PM (1).jpeg` |
| 16 | Gallery 2 — bottom 3 | `images/event-002.jpeg` |

### About Us (`about-us-floral-design-business.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Portrait / designer photo | `images/jo-and-erik.png` |

### Kind Words (`client-reviews.html`)
| # | Position | Current file |
|---|----------|-------------|
| 1 | Featured review photo | `images/marie-c-kindwords.jpg` |

### Contact (`contact-us-floral-arrangements.html`)
No swappable content images on this page.

---

## In-progress work

- The announcement banner (`#site-banner`) at the top of `index.html` includes an HTML comment: `<!-- delete this section when redesign is complete -->`. It should be removed once all pages are live and the redesign is complete.
- The designer photo and several collection images still point to the Zyrosite CDN and are pending replacement with locally hosted images.
