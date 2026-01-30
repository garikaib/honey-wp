---
description: Build theme assets with Vite and Tailwind
---

# Build Theme Assets

## Development Mode (with hot reload)
// turbo
```bash
cd /home/garikaib/Documents/sites/honey-wp/wp-content/themes/honeyscroop-theme
npm run dev
```

## Production Build
// turbo
```bash
cd /home/garikaib/Documents/sites/honey-wp/wp-content/themes/honeyscroop-theme
npm run build
```

## Notes
- Compiled assets go to `dist/` directory
- Tailwind CSS is processed via PostCSS
- React blocks are bundled by Vite
- After building, refresh the WordPress site to see changes
