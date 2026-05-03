# LaLaDia Design System — Gold · White · Black

> **Single source of truth.** All colors live in [`app.css`](file:///f:/LaLaDia/resources/css/app.css) `:root`.
> Update one value → entire site updates instantly.

---

## Philosophy

Three color families, each carrying the same warm temperature:

| Family | Character | Role |
|---|---|---|
| **GOLD** | Warm, vivid, jewellery-grade | Brand, CTAs, badges, highlights |
| **WHITE** | Warm whites with a whisper of gold | Backgrounds, surfaces, cards |
| **BLACK** | Warm charcoals with a gold undertone | Text hierarchy, shadows |

> No blues, no greens, no purples, no rose. Pure luxury monochrome.

---

## ✦ Gold Scale (10 stops)

| Token | Hex | Use |
|---|---|---|
| `--gold-25`  | `#FFFFF5` | Ghost bg / hover tint |
| `--gold-50`  | `#FFFBEA` | Softest surface tint / badge bg |
| `--gold-100` | `#FFF3C4` | Light badge / chip fill |
| `--gold-200` | `#FFE066` | Light bright gold |
| `--gold-300` | `#F5C518` | Vivid cinema gold / accent |
| **`--gold-400`** | **`#D4A017`** | **TRUE PRIMARY — satin gold** |
| `--gold-500` | `#B8860B` | Primary hover / dark goldenrod |
| `--gold-600` | `#96680A` | Pressed state / deep antique |
| `--gold-700` | `#6B4A07` | Burnished bronze-gold |
| `--gold-800` | `#3D2B04` | Text on light gold backgrounds |
| `--gold-900` | `#1A1200` | Near-black warm gold |

---

## 🤍 White Scale (6 stops)

| Token | Hex | Use |
|---|---|---|
| `--white-pure`  | `#FFFFFF` | Pure white — floating cards, modals |
| `--white-warm`  | `#FDFCF8` | **Main page background** |
| `--white-soft`  | `#FAF8F2` | Card / section backgrounds |
| `--white-ivory` | `#F5F1E8` | Divider zones, chips |
| `--white-stone` | `#EDE8DC` | Standard borders |
| `--white-linen` | `#E0D9CA` | Strong borders / separators |

---

## 🖤 Black Scale (8 stops)

| Token | Hex | Use |
|---|---|---|
| `--black-rich`    | `#0A0800` | Richest — hero headings |
| `--black-deep`    | `#181510` | **Primary text color** (h1, h2) |
| `--black-strong`  | `#2C2820` | h3, strong body |
| `--black-mid`     | `#4A4438` | Body paragraphs |
| `--black-muted`   | `#706860` | Secondary / muted info |
| `--black-soft`    | `#9A9088` | Captions, meta, timestamps |
| `--black-ghost`   | `#C4BAB0` | Placeholders, disabled |
| `--black-whisper` | `#E2DDD8` | Skeleton shimmer |

---

## 🎨 Semantic Tokens (Use in UI)

### Brand
```css
--color-primary        /* #D4A017 — satin gold (main CTA) */
--color-primary-hover  /* #B8860B — dark goldenrod hover  */
--color-accent         /* #F5C518 — vivid pop accent      */
--color-accent-subtle  /* #FFF3C4 — soft badge bg         */
```

### Backgrounds
```css
--color-bg             /* #FDFCF8 — warm main BG          */
--color-bg-soft        /* #FAF8F2 — card background       */
--color-surface        /* #FFFFFF — pure floating element  */
--color-surface-warm   /* #FFFFF5 — gold ghost hover      */
--color-surface-gold   /* #FFFBEA — gold-tinted chip      */
```

### Text
```css
--color-text            /* #181510 — h1/h2 headings       */
--color-text-secondary  /* #2C2820 — h3, strong body      */
--color-text-body       /* #4A4438 — body paragraphs      */
--color-text-muted      /* #706860 — secondary info       */
--color-text-soft       /* #9A9088 — captions, meta       */
--color-text-placeholder/* #C4BAB0 — input placeholders   */
```

### Borders
```css
--color-border          /* #EDE8DC — standard rule        */
--color-border-soft     /* #F5F1E8 — ghost border         */
--color-border-strong   /* #E0D9CA — visible separator    */
--color-border-gold     /* rgba(212,160,23, 0.3) — gold   */
```

---

## 🛠️ Usage Examples

### Standard Tailwind classes (100% compatible)
```html
<div class="bg-ivory border border-champagne text-brand">
<div class="bg-cream text-muted">
<button class="bg-primary text-white hover:bg-secondary">
```

### RGBA transparency
```html
<!-- 8% gold background -->
<div style="background: rgba(var(--color-primary-rgb), 0.08);">

<!-- gold-tinted border -->
<div style="border-color: var(--color-border-gold);">
```

### New utility classes
```html
<div class="bg-primary-soft">   <!-- rgba gold 8%      -->
<div class="bg-gold-glow">      <!-- gradient gold glow -->
<div class="border-gold">       <!-- 30% gold border    -->
```
