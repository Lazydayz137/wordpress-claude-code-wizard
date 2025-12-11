#!/bin/bash
# Setup Any Vanilla VPS for WordPress Support (Interactive)
# Usage: ./setup_vps.sh [TARGET_IP] [SSH_USER]

TARGET_IP=$1
SSH_USER=$2

echo "================================================"
echo "   🚀 WordPress VPS Setup Wizard"
echo "================================================"

# 1. Interactive Prompts
if [ -z "$TARGET_IP" ]; then
    read -p "Enter Target VPS IP: " TARGET_IP
fi

if [ -z "$SSH_USER" ]; then
    read -p "Enter SSH Username [root]: " SSH_USER
    SSH_USER=${SSH_USER:-root}
fi

if [ -z "$TARGET_IP" ]; then
    echo "❌ Error: IP Address is required."
    exit 1
fi

echo "------------------------------------------------"
echo "Target: $SSH_USER@$TARGET_IP"
echo "------------------------------------------------"

# 2. Check SSH & Bootstrap Keys
echo "🔍 Checking SSH connection..."

if ssh -o BatchMode=yes -o ConnectTimeout=5 -q "$SSH_USER@$TARGET_IP" exit; then
    echo "✅ SSH connection established (Keys already set up)."
else
    echo "⚠️  SSH Key authentication failed."
    echo "💡 We will now try to install your SSH key."
    echo "   You will be asked for the VPS password."
    echo ""
    
    # Ensure local key exists
    if [ ! -f ~/.ssh/id_rsa.pub ] && [ ! -f ~/.ssh/id_ed25519.pub ]; then
        echo "Generating local SSH key..."
        ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519 -N ""
    fi

    # Copy ID (Will prompt for password)
    if ssh-copy-id "$SSH_USER@$TARGET_IP"; then
        echo "✅ SSH Key installed successfully!"
    else
        echo "❌ Failed to install SSH key. Please check your password and try again."
        exit 1
    fi
fi

# 3. Generate Passwords
echo "🔐 Generating secure credentials..."
MYSQL_ROOT_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9')
WP_DB_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9')

# 4. Upload Provisioning Script
echo "📦 Uploading provisioning script..."
scp scripts/provision_vps.sh "$SSH_USER@$TARGET_IP:/tmp/provision_vps.sh"
ssh "$SSH_USER@$TARGET_IP" "chmod +x /tmp/provision_vps.sh"

# 5. Execute Provisioning
echo "⚙️  Running provisioning script on remote server..."
echo "   (This will install Apache, MySQL, PHP, and WordPress)"
ssh "$SSH_USER@$TARGET_IP" "/tmp/provision_vps.sh '$MYSQL_ROOT_PASS' '$WP_DB_PASS'"

# 6. Save Configuration
echo "📝 Saving configuration for migration..."
cat > .droplet_info <<EOF
{
  "ip_address": "$TARGET_IP",
  "mysql_root_pass": "$MYSQL_ROOT_PASS",
  "wp_db_pass": "$WP_DB_PASS",
  "droplet_name": "custom-vps",
  "manual_setup": true
}
EOF

echo "================================================"
echo "✅ VPS Setup & Provisioning Complete!"
echo "================================================"
echo "MySQL Root: $MYSQL_ROOT_PASS"
echo "WP DB Pass: $WP_DB_PASS"
echo ""
echo "🚀 Ready to Deploy!"
echo "Run the final migration command:"
echo ""
echo "  ./migrate_now.sh"
echo "================================================"
