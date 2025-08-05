#!/bin/bash

# Project Management System - VPS Deployment Script
# This script helps deploy the Laravel backend on a VPS

set -e

echo "🚀 Starting deployment process..."

# Check if docker and docker-compose are installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please edit .env file with your production settings before continuing!"
    echo "Press any key to continue after editing .env..."
    read -n 1 -s
fi

echo "🔨 Building Docker images..."
docker-compose -f docker-compose.prod.yml build --no-cache

echo "🗄️  Starting database services..."
docker-compose -f docker-compose.prod.yml up -d mysql redis

echo "⏳ Waiting for database to be ready..."
sleep 10

echo "🚀 Starting application..."
docker-compose -f docker-compose.prod.yml up -d

echo "📊 Checking service status..."
docker-compose -f docker-compose.prod.yml ps

echo "🎉 Deployment completed!"
echo ""
echo "📋 Service URLs:"
echo "🌐 Application: http://localhost:$(grep APP_PORT .env | cut -d'=' -f2)"
echo "🗃️  phpMyAdmin: http://localhost:$(grep PHPMYADMIN_PORT .env | cut -d'=' -f2)"
echo "🔍 MySQL: localhost:$(grep MYSQL_EXTERNAL_PORT .env | cut -d'=' -f2)"
echo "📦 Redis: localhost:$(grep REDIS_EXTERNAL_PORT .env | cut -d'=' -f2)"
echo ""
echo "📝 To view logs: docker-compose -f docker-compose.prod.yml logs -f"
echo "🛑 To stop: docker-compose -f docker-compose.prod.yml down"