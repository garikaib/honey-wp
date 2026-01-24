# Cloudflare Tunnel Setup for dev.honeyscroop.co.zw

Instructions for setting up a persistent tunnel on Ubuntu 24.04 to expose your DDEV site.

## 1. Install cloudflared

```bash
# Add Cloudflare GPG key
sudo mkdir -p --mode=0755 /usr/share/keyrings
curl -fsSL https://pkg.cloudflare.com/cloudflare-main.gpg | sudo tee /usr/share/keyrings/cloudflare-main.gpg >/dev/null

# Add repo
echo 'deb [signed-by=/usr/share/keyrings/cloudflare-main.gpg] https://pkg.cloudflare.com/cloudflared jammy main' | sudo tee /etc/apt/sources.list.d/cloudflared.list

# Install
sudo apt-get update && sudo apt-get install cloudflared
```

## 2. Authenticate and Create Tunnel

```bash
# Login (opens browser)
cloudflared tunnel login

# Create a tunnel named 'honeyscroop-dev'
cloudflared tunnel create honeyscroop-dev
```

This will output a Tunnel ID (e.g., `Viewing your tunnel... ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`). **Save this.**

## 3. Configure the Tunnel

Create a config file at `~/.cloudflared/config.yml`:

```yaml
tunnel: <Your-Tunnel-ID>
credentials-file: /home/garikaib/.cloudflared/<Your-Tunnel-ID>.json

ingress:
  - hostname: dev.honeyscroop.co.zw
    service: http://localhost:8443 # DDEV usually exposes HTTPS on port 8443 or HTTP on 8080.
    originRequest:
      noTLSVerify: true
  - service: http_status:404
```

> **Note:** Since DDEV uses a router, pointing to `http://localhost:8443` (HTTPS) with `noTLSVerify` is often robust. Alternatively, check `ddev describe` for the HTTP port (usually 80 or 8080) and use `http://localhost:8080`.

## 4. Route DNS

Route the domain to your tunnel:

```bash
cloudflared tunnel route dns honeyscroop-dev dev.honeyscroop.co.zw
```

## 5. Run as a Service

Install as a systemd service to keep it running:

```bash
sudo cloudflared service install
sudo systemctl start cloudflared
sudo systemctl enable cloudflared
```

## 6. Verification

Visit `https://dev.honeyscroop.co.zw`. It should proxy to your local DDEV instance.
