#!/bin/bash

# Database setup script with user interaction

set -e

echo "🗄️ Database Setup Assistant"
echo "=========================="

# Check if containers are running
if ! docker-compose -f docker-compose.prod.yml ps | grep -q "Up"; then
    echo "❌ Backend containers are not running. Please start them first:"
    echo "   docker-compose -f docker-compose.prod.yml up -d"
    exit 1
fi

echo "✅ Backend containers are running"
echo ""

# Show current database status
echo "📊 Current Database Status:"
docker-compose -f docker-compose.prod.yml exec -T app php artisan db:setup --force

echo ""
echo "What would you like to do?"
echo "1) Run migrations only (safe - won't delete data)"
echo "2) Run migrations and seed sample data"
echo "3) Fresh install (⚠️  WARNING: This will delete all data!)"
echo "4) Just check status (no changes)"
echo "5) Exit"

read -p "Please choose an option (1-5): " choice

case $choice in
    1)
        echo "🔄 Running migrations..."
        docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
        ;;
    2)
        echo "🔄 Running migrations and seeding..."
        docker-compose -f docker-compose.prod.yml exec -T app php artisan db:setup --force --seed
        ;;
    3)
        echo "⚠️  WARNING: This will delete ALL existing data!"
        read -p "Are you absolutely sure? Type 'yes' to continue: " confirm
        if [ "$confirm" = "yes" ]; then
            echo "🔄 Running fresh migration with sample data..."
            docker-compose -f docker-compose.prod.yml exec -T app php artisan db:setup --force --fresh --seed
        else
            echo "❌ Operation cancelled."
            exit 0
        fi
        ;;
    4)
        echo "📊 Database status already shown above."
        ;;
    5)
        echo "👋 Goodbye!"
        exit 0
        ;;
    *)
        echo "❌ Invalid option. Please run the script again."
        exit 1
        ;;
esac

echo ""
echo "📊 Final Database Status:"
docker-compose -f docker-compose.prod.yml exec -T app php artisan db:setup --force

echo ""
echo "✅ Database setup completed!"

# Show API test suggestion
echo ""
echo "🧪 Test your API:"
echo "   curl https://project.mercylife.cc/api/health"
echo "   curl https://project.mercylife.cc/api/clients"