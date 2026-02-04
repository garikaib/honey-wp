# WordPress Synchronization Workflow

This workflow describes how to synchronize the WordPress site between the local DDEV environment and the remote production server.

## Prerequisites

- SSH access to the production server (`51.195.252.90`).
- DDEV installed locally.
- Zstd installed on both local and remote (for compression).

## Commands

Custom DDEV host commands have been created to simplify the process.

### Full Sync (DB + Files)

```bash
ddev prod-push
```

### Code-Only Sync

If you only want to deploy theme updates without overwriting the production database:

```bash
# From the project root
rsync -ahvz --compress-choice=zstd --checksum --delete \
    --exclude-from='.gitignore' \
    --exclude='.git/' \
    --exclude='.ddev/' \
    --exclude='wp-config.php' \
    --exclude='.env' \
    ./ ubuntu@51.195.252.90:/var/www/honeyscoop.co.zw/htdocs/ --rsync-path="sudo rsync"
```

## Deployment Workflow

1. **Build**: Run `npm run build` in the theme directory.
2. **Commit**: git add/commit/push to GitHub.
3. **Sync**: Run `ddev prod-push` or the manual `rsync` command.
4. **Verify**: Check the live site.

### Pull from Production

Pull the production database and site files to your local DDEV environment.

```bash
ddev prod-pull
```

**What it does:**
1. Exports the production database on the remote server.
2. Downloads the database locally.
3. Imports the database into DDEV and runs `wp search-replace` to update URLs to the local development domain.
4. Syncs the site files from the remote server to local, excluding environment-specific files.

## Technical Details

- **Compression**: Both scripts use `rsync` with `--compress-choice=zstd` for high performance.
- **Verification**: The `--checksum` flag ensures that files are only transferred if their contents have actually changed.
- **Remote Permissions**: The rsync command uses `--rsync-path="sudo rsync"` to ensure that the `ubuntu` user can manage files owned by `www-data` on the production server. This requires passwordless sudo for the `ubuntu` user on the remote.
- **Security**: The `wp-config.php` and `.env` files are explicitly excluded from sync to prevent overwriting production credentials or local development settings.
- **Cleanup**: The `prod-push` script excludes non-WordPress files such as documentation, venv, and internal tools.
