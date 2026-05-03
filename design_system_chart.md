# LaLaDia Design System Chart: Luxury Gold & White

This document serves as the single source of truth for the project's visual identity. All colors are centrally managed in [app.css](file:///f:/LaLaDia/resources/css/app.css).

## ⚜️ Core Brand Palette
Managed via `--color-primary` and related variables.

| Role | Semantic Token | Hex / RGB | Tailwind Class |
| :--- | :--- | :--- | :--- |
| **Primary Gold** | `primary` | `#B8963C` / `184, 150, 60` | `bg-primary`, `text-primary` |
| **Secondary Gold** | `secondary` | `#9A7A2F` / `154, 122, 47` | `bg-secondary`, `text-secondary` |
| **Accent Gold** | `gold-warm` | `#D4A843` / `212, 168, 67` | `bg-gold-warm`, `text-gold-warm` |
| **Luxury Black** | `brand` / `ink` | `#1A1209` / `26, 18, 9` | `bg-brand`, `text-brand` |

## 🕯️ Surface & Background System
Designed for a premium, high-end readability.

| Layer | Semantic Token | Hex / RGB | Tailwind Class |
| :--- | :--- | :--- | :--- |
| **Main BG** | `ivory` | `#FDFAF4` / `253, 250, 244` | `bg-ivory` |
| **Soft Surface** | `cream` | `#F5EDD8` / `245, 237, 216` | `bg-cream` |
| **Strong Border** | `champagne` | `#EDE0C4` / `237, 224, 196` | `border-champagne` |
| **Soft Border** | `sand` | `#E8D9B0` / `232, 217, 176` | `border-sand` |

## 🖋️ Typography & Neutrals
Consistent hierarchy for high-end customers.

| Weight | Semantic Token | Hex Code | Tailwind Class |
| :--- | :--- | :--- | :--- |
| **Headings** | `brand` | `#1A1209` | `text-brand` |
| **Subheadings** | `brown` | `#3D2E14` | `text-brown` |
| **Body Text** | `muted` | `#6B5A3A` | `text-muted` |
| **Secondary Info** | `taupe` | `#9E8A6A` | `text-taupe` |
| **Disabled** | `disabled` | `#C4B49A` | `text-disabled` |

## 🛠️ Usage Guidelines

### 1. Simple Colors
Use Tailwind classes for 90% of your work:
```html
<div class="bg-ivory border border-champagne text-brand"> ... </div>
```

### 2. Semantic Opacity (RGBA)
For gradients or overlays that need transparency, use the RGB helper variables:
```html
<div style="background: rgba(var(--color-primary-rgb), 0.1);"> ... </div>
```

### 3. Global Updates
To change the site's entire color scheme (e.g., to a Platinum theme), simply update the hex codes and RGB numbers in the `:root` section of `resources/css/app.css`. The changes will propagate instantly to all buttons, charts, and pages.
