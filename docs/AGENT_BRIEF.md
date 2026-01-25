# 🚀 AGENT BRIEFING: Honeyscroop Project

**READ THIS FIRST.** This document contains the "Lay of the Land" for the Honeyscroop codebase.

## 🌟 1. Project Context (The "What")
*   **Platform**: **WordPress** (Custom Theme Development).
*   **Environment**: **DDEV** (Local Docker-based PHP environment).
*   **Goal**: We are building a premium, high-performance custom theme (`honeyscroop-theme`) from scratch. This involves both **Code** (theme dev) and **Content** (populating the site).
*   **Theme Name**: `honeyscroop-theme`.
*   **Theme Path**: `wp-content/themes/honeyscroop-theme/`.

## 🛠️ 2. Technology Stack (The "How")
*   **Backend**: Native WordPress.
    *   **Custom Post Types (CPTs)**: Defined in PHP (`inc/cpt-*.php`). No plugins used for structure.
    *   **Templating**: Hybrid. Standard PHP templates (`header.php`, `functions.php`) mixed with Block Patterns (`patterns/`).
*   **Frontend**: React + Tailwind CSS.
    *   **Framework**: **React 18** (via Vite).
    *   **Styling**: **Tailwind CSS** + **DaisyUI** (Component Library).
    *   **Architecture**: "React Islands". We do not build a Single Page App (SPA). Instead, we build small, interactive React apps (like the Product Grid or Ticker) and mount them into specific PHP pages.
*   **Build System**: **Vite**.
    *   Compiles `src/` -> `dist/`.
    *   You MUST run `npm run build` after editing any JS/CSS in `src/`.

## 📂 3. The Lay of the Land (Directory Structure)
All work happens inside: `wp-content/themes/honeyscroop-theme/`

*   **`src/` (SOURCE)**: 👈 **EDIT HERE**.
    *   `src/style.css`: The master Tailwind CSS file.
    *   `src/product-grid/`: React app for the Product Categories (3D Hover, Glassmorphism).
    *   `src/partner-ticker/`: React app for the Scrolling Partner Logos (Infinite loop).
*   **`dist/` (OUTPUT)**: 🛑 **DO NOT EDIT**. Vite generates these files.
*   **`inc/` (LOGIC)**: PHP Includes.
    *   `assets.php`: Enqueues the `dist/` scripts and passes WP data to React.
    *   `cpt-*.php`: Defines content structures (Partners, Honey Varieties).
*   **`patterns/` (VIEWS)**: PHP Block Patterns. These contain the HTML containers where React apps mount (e.g., `<div id="root"></div>`).

## ⚡ 4. Standard Workflows
### A. Adding a New Feature
1.  **Define Content**: Do we need a new CPT? Create `inc/cpt-newfeature.php`.
2.  **Build Frontend**: Create `src/new-feature/index.jsx` (React).
3.  **Register Entry**: Add to `vite.config.js`.
4.  **Enqueue**: Add to `inc/assets.php`.
5.  **Mount**: Add `<div id="new-feature-root"></div>` to a PHP template or Pattern.
6.  **Compile**: Run `npm run build`.

### B. Styling
*   Use **DaisyUI** components (`card`, `btn`, `hero`) for rapid premium UI.
*   Use **Tailwind** utility classes for layout.
*   **Assets**: Use `lucide-react` for icons.

## 📝 5. Current "Islands" (Features)
1.  **Product Grid**: React + DaisyUI. Features a custom 3D mouse-tracking tilt effect.
2.  **Partner Ticker**: Infinite scrolling marquee. Fetches "Partner" posts via WP REST API.
3.  **Honey Finder**: Interactive filter tool.

---
**Status**: The core framework is set. We are currently building out the content pages (About, Shop, Product Single).
