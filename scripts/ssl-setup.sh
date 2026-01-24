#!/bin/bash
# Automate SSL with Lego and Cloudflare DNS-01 Challenge
# Usage: ./scripts/ssl-setup.sh

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | xargs)
else
    echo "❌ Error: .env file not found. Please create one with CF_EMAIL and CF_API_KEY."
    exit 1
fi

DOMAIN="dev.honeyscroop.co.zw"
EMAIL=$CF_EMAIL
LEGO_PATH=".ddev/lego"

echo "🔐 Starting SSL setup for $DOMAIN..."

# Ensure lego is installed (simple check)
if ! command -v lego &> /dev/null; then
    echo "⚠️ Lego not found. Please install lego first or run inside DDEV."
    # Attempt to install locally if on Linux (optional convenience)
    # wget ...
else
    echo "✅ Lego found."
fi

# Run Lego
# CLOUDFLARE_EMAIL and CLOUDFLARE_API_KEY are needed by lego's cloudflare provider
# Mapping .env vars to what lego expects
export CLOUDFLARE_EMAIL=$CF_EMAIL
export CLOUDFLARE_API_KEY=$CF_API_KEY

mkdir -p $LEGO_PATH

echo "running lego..."
lego --email "$EMAIL" --dns cloudflare --domains "$DOMAIN" --path "$LEGO_PATH" --accept-tos run

if [ $? -eq 0 ]; then
    echo "✅ Certificate obtained successfully!"
    
    # Symlink to DDEV custom certs
    # DDEV expects files in .ddev/custom_certs/
    # Usually passed as:
    #   .ddev/custom_certs/example.com.crt
    #   .ddev/custom_certs/example.com.key
    
    mkdir -p .ddev/custom_certs
    
    # Copy or Symlink (Copy is safer for DDEV restarts)
    cp "$LEGO_PATH/certificates/$DOMAIN.crt" ".ddev/custom_certs/$DOMAIN.crt"
    cp "$LEGO_PATH/certificates/$DOMAIN.key" ".ddev/custom_certs/$DOMAIN.key"
    
    echo "✅ Certificates copied to .ddev/custom_certs/"
    echo "🔄 Restarting DDEV to apply certs..."
    ddev restart
else
    echo "❌ Lego failed. Check your credentials."
fi
