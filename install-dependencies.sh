#!/bin/bash

# BitMews Platform - Composer Installation Script
# This script helps install Composer dependencies

echo "========================================="
echo "BitMews Platform - Dependency Installer"
echo "========================================="
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed!"
    echo ""
    echo "Please install Composer first:"
    echo "https://getcomposer.org/download/"
    echo ""
    echo "Or use one of these methods:"
    echo ""
    echo "Method 1: Download composer.phar"
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  php composer.phar install --no-dev --optimize-autoloader"
    echo ""
    echo "Method 2: Install globally"
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  sudo mv composer.phar /usr/local/bin/composer"
    echo "  composer install --no-dev --optimize-autoloader"
    echo ""
    exit 1
fi

echo "✅ Composer found: $(composer --version)"
echo ""

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json not found!"
    echo "Make sure you're in the project root directory."
    exit 1
fi

echo "✅ composer.json found"
echo ""

# Check if vendor folder already exists
if [ -d "vendor" ]; then
    echo "⚠️  vendor folder already exists!"
    echo ""
    read -p "Do you want to reinstall? (y/n): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Installation cancelled."
        exit 0
    fi
    echo "Removing existing vendor folder..."
    rm -rf vendor
fi

echo "📦 Installing Composer dependencies..."
echo "This may take 2-5 minutes..."
echo ""

# Install dependencies
composer install --no-dev --optimize-autoloader

# Check if installation was successful
if [ $? -eq 0 ]; then
    echo ""
    echo "========================================="
    echo "✅ Installation Successful!"
    echo "========================================="
    echo ""
    echo "Vendor folder created with all dependencies."
    echo ""
    echo "Next steps:"
    echo "1. Set permissions: chmod -R 755 storage bootstrap/cache"
    echo "2. Visit installer: https://yourdomain.com/install"
    echo "3. Follow the installation wizard"
    echo ""
else
    echo ""
    echo "========================================="
    echo "❌ Installation Failed!"
    echo "========================================="
    echo ""
    echo "Please check the error messages above."
    echo ""
    echo "Common issues:"
    echo "- PHP version too old (need 8.0+)"
    echo "- Missing PHP extensions"
    echo "- Insufficient memory"
    echo ""
    echo "Try running with more memory:"
    echo "  php -d memory_limit=512M $(which composer) install --no-dev --optimize-autoloader"
    echo ""
    exit 1
fi
