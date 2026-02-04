# Production Deployment Process

This document describes how to deploy updates to the Honeyscoop production server (`honeyscoop.co.zw`).

## Standard Deployment (Code + Assets)

This is the most common deployment path when you have made changes to the theme, CSS, or React components.

### 1. Build Assets Locally
Ensure all frontend assets are compiled and ready for production.
```bash
cd wp-content/themes/honeyscroop-theme
npm run build
```

### 2. Push Changes to Production
Use the custom DDEV command to sync files to the server. This command uses `rsync` for efficient, incremental updates.
```bash
ddev prod-push
```

> [!WARNING]
> The `ddev prod-push` command currently synchronizes both **files** and the **database**. 
> If you only wish to sync code without overwriting the production database, use the `rsync` command manually (see below).

## Advanced Deployment

### Code-Only Synchronization (No DB)
If you only want to push theme changes without touching the production database:
```bash
rsync -ahvz --compress-choice=zstd --checksum --delete --progress \
    --exclude='.git/' \
    --exclude='.ddev/' \
    --exclude='.agent/' \
    --exclude='wp-config.php' \
    --exclude='.env' \
    ./ ubuntu@51.195.252.90:/var/www/honeyscoop.co.zw/htdocs/ --rsync-path="sudo rsync"
```

### Server Details
- **IP**: `51.195.252.90`
- **User**: `ubuntu`
- **Path**: `/var/www/honeyscoop.co.zw/htdocs`
- **PHP User**: `www-data`
