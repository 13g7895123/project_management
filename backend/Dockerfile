FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key if .env exists
RUN if [ -f .env ]; then php artisan key:generate; fi

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Nginx
COPY nginx.conf /etc/nginx/sites-available/default
RUN rm /etc/nginx/sites-enabled/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Create supervisor config
RUN echo '[supervisord]' > /etc/supervisor/conf.d/laravel.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'redirect_stderr=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'stdout_logfile=/var/log/nginx.log' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '' >> /etc/supervisor/conf.d/laravel.conf && \
    echo '[program:php-fpm]' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'command=php-fpm' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'redirect_stderr=true' >> /etc/supervisor/conf.d/laravel.conf && \
    echo 'stdout_logfile=/var/log/php-fpm.log' >> /etc/supervisor/conf.d/laravel.conf

# Expose port 8000
EXPOSE 8000

CMD ["sh", "-c", "cd /var/www/html && php artisan serve --host=0.0.0.0 --port=8000"]