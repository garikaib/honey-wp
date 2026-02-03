# WordPress Synchronization Workflow

This workflow describes how to synchronize the WordPress site between the local DDEV environment and the remote production server.

## Prerequisites

- SSH access to the production server (`51.195.252.90`).
- DDEV installed locally.
- Zstd installed on both local and remote (for compression).

## Commands

Custom DDEV host commands have been created to simplify the process.

### Push to Production

Push local database and `wp-content/uploads` (media) to the production server.

```bash
ddev prod-push
```

**What it does:**
1. Exports the local database.
2. Transfers the database to the remote server.
3. Imports the database on remote and runs `wp search-replace` to update URLs.
4. Syncs the entire site directory (excluding sensitive/local files like `.env`, `wp-config.php`, `.git`, etc.) using `rsync` with `zstd` compression and checksum verification. The script uses `sudo` on the remote server to ensure full write access to the `htdocs` directory.
5. Sets correct ownership (`www-data:www-data`) on the remote server.

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
