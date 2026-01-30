---
description: Create custom React-based Gutenberg blocks
---

# Create React Blocks

## Steps

1. **Create block folder**
   ```
   src/{block-name}/
   ├── index.jsx      # Block registration and render
   ├── edit.jsx       # Editor component (optional)
   ├── save.jsx       # Frontend save (optional, for static blocks)
   └── style.css      # Block styles
   ```

2. **Basic block structure** (`src/{block-name}/index.jsx`):
   ```jsx
   import { registerBlockType } from '@wordpress/blocks';
   import './style.css';

   registerBlockType('honeyscroop/{block-name}', {
       title: 'Block Title',
       icon: 'star-filled',
       category: 'theme',
       edit: () => {
           return <div className="block-editor">Editor View</div>;
       },
       save: () => {
           return <div className="block-frontend">Frontend View</div>;
       },
   });
   ```

3. **Register in PHP** (`inc/blocks.php`):
   ```php
   function honeyscroop_register_blocks() {
       wp_register_script(
           'honeyscroop-{block-name}',
           HONEYSCROOP_URI . '/dist/{block-name}.js',
           array( 'wp-blocks', 'wp-element', 'wp-editor' ),
           HONEYSCROOP_VERSION
       );

       register_block_type( 'honeyscroop/{block-name}', array(
           'editor_script' => 'honeyscroop-{block-name}',
       ));
   }
   add_action( 'init', 'honeyscroop_register_blocks' );
   ```

4. **Build**
   // turbo
   ```bash
   npm run build
   ```

## Existing Blocks
- `product-grid` - Product category cards
- Check `src/` directory for all blocks

## Dynamic Blocks (Server-Side Render)
For blocks that need PHP data, use `render_callback`:
```php
register_block_type( 'honeyscroop/{block-name}', array(
    'editor_script'   => 'honeyscroop-{block-name}',
    'render_callback' => 'honeyscroop_render_{block_name}',
));
```
