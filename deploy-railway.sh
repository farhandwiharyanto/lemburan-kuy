#!/bin/bash
echo "🚀 Preparing for Railway deployment..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Make sure you're in Laravel root directory."
    exit 1
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run migrations if database exists
echo "🗄️ Running migrations..."
php artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Cache configuration
echo "⚙️ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Railway preparation complete!"