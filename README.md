# 🚀 BitMews - AI-Powered Crypto Intelligence Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-Commercial-green.svg)](LICENSE)

## 📋 Overview

BitMews is a comprehensive crypto intelligence platform that combines real-time news aggregation, AI-powered analysis, multi-chain data tracking, token launchpad, DeFi aggregation, and community features - all in one powerful platform.

## ✨ Key Features

### 🎯 Core Features
- **Real-time Crypto News** - Aggregation from 100+ sources
- **AI-Powered Analysis** - Multi-AI integration (GPT-4, Gemini, Claude, Kimi)
- **Multi-Chain Support** - 50+ blockchains (Ethereum, Solana, BSC, etc.)
- **Token Listings** - Dynamic token pages with real-time data
- **Risk Scoring** - Advanced scam detection and security analysis
- **Portfolio Tracking** - Multi-wallet, cross-chain portfolio management

### 💰 Monetization Features
- **Subscription System** - Tiered pricing with regional support
- **Token Launchpad** - IDO/IEO platform with smart contracts
- **Advertising Platform** - Programmatic ads with on-chain targeting
- **Creator Platform** - Write-to-Earn, tips, sponsored content
- **NFT Marketplace** - Integrated NFT trading and memberships
- **Affiliate System** - Multi-level affiliate commissions

### 🔧 Technical Features
- **Multi-Gateway Payments** - 15+ payment gateways (Stripe, Razorpay, Crypto)
- **Multi-AI System** - Dynamic AI provider management
- **DeFi Aggregation** - Yield farming, LP tracking, cross-chain swaps
- **Advanced Analytics** - Real-time dashboards and reporting
- **API Access** - RESTful API with rate limiting
- **Multi-tenancy** - Organization panels with team management

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11.x
- **Database:** PostgreSQL 15+ / MySQL 8+
- **Cache:** Redis 7+
- **Queue:** Redis + Horizon
- **Search:** Meilisearch / Algolia
- **Storage:** AWS S3 / Local

### Frontend
- **Admin Panel:** Filament PHP v3
- **Public Site:** Next.js 14 (App Router)
- **Mobile:** React Native (Expo)
- **UI:** TailwindCSS, Shadcn/ui

### AI/ML
- **Language:** Python 3.11+
- **Framework:** FastAPI
- **ML:** TensorFlow, Scikit-learn
- **NLP:** Hugging Face Transformers

### Blockchain
- **Smart Contracts:** Solidity
- **Framework:** Hardhat
- **Web3:** ethers.js, wagmi

## 📦 Installation

### Requirements
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ & NPM/Yarn
- PostgreSQL 15+ or MySQL 8+
- Redis 7+
- Git

### Quick Install (Auto-Installer)

1. **Upload files to your server**
```bash
# Extract the zip file to your domain root
unzip bitmews-platform.zip -d /path/to/your/domain
```

2. **Navigate to installer**
```
https://yourdomain.com/install
```

3. **Follow the installation wizard**
- Server requirements check
- Database configuration
- Admin account setup
- Email configuration
- License activation

### Manual Installation

```bash
# Clone repository
git clone https://github.com/IamTamheedNazir/bitmews-platform.git
cd bitmews-platform

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# Then run migrations
php artisan migrate --seed

# Build assets
npm run build

# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Start queue worker
php artisan queue:work --daemon

# Start scheduler (add to crontab)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 🔐 Default Credentials

After installation, use these credentials:

**Super Admin:**
- Email: admin@bitmews.com
- Password: Admin@123456

**Demo User:**
- Email: user@bitmews.com
- Password: User@123456

**⚠️ Change these immediately after first login!**

## 📚 Documentation

- [Installation Guide](docs/installation.md)
- [Configuration](docs/configuration.md)
- [API Documentation](docs/api.md)
- [Admin Guide](docs/admin-guide.md)
- [Developer Guide](docs/developer-guide.md)

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="BitMews"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bitmews
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bitmews.com
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateways
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

# AI Providers
OPENAI_API_KEY=your_openai_key
GEMINI_API_KEY=your_gemini_key
CLAUDE_API_KEY=your_claude_key

# Blockchain
ETHEREUM_RPC_URL=https://eth-mainnet.g.alchemy.com/v2/your-key
SOLANA_RPC_URL=https://api.mainnet-beta.solana.com
```

## 🚀 Deployment

### cPanel Deployment

1. Upload files via File Manager or FTP
2. Extract zip file
3. Point domain to `public` folder
4. Visit `yourdomain.com/install`
5. Follow installation wizard

### VPS/Cloud Deployment

```bash
# Using Laravel Forge (Recommended)
# Or manual deployment with Nginx/Apache

# Nginx configuration
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/bitmews/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔄 Updates

### Automatic Updates
- Admin Panel → System → Updates
- One-click update system

### Manual Updates
```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🐛 Troubleshooting

### Common Issues

**500 Internal Server Error**
```bash
# Check permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

**Database Connection Error**
- Verify database credentials in `.env`
- Ensure database exists
- Check database user permissions

**Queue Not Processing**
```bash
# Restart queue worker
php artisan queue:restart

# Check supervisor configuration
sudo supervisorctl restart bitmews-worker:*
```

## 📊 Performance Optimization

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

## 🔒 Security

- All passwords are hashed using bcrypt
- CSRF protection enabled
- XSS protection enabled
- SQL injection protection via Eloquent ORM
- Rate limiting on API endpoints
- Two-factor authentication support
- Regular security updates

## 📄 License

This is a commercial product. See [LICENSE](LICENSE) file for details.

## 🤝 Support

- **Email:** support@bitmews.com
- **Documentation:** https://docs.bitmews.com
- **Discord:** https://discord.gg/bitmews
- **Ticket System:** https://support.bitmews.com

## 🙏 Credits

Built with ❤️ by the BitMews Team

- Laravel Framework
- Filament PHP
- Next.js
- TailwindCSS
- And many other open-source projects

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---

**Made with 💙 for the Crypto Community**
