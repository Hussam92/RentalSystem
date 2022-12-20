FROM php:8.1-fpm

# Arguments defined in docker.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    redis-server \
    nodejs \
    zlib1g-dev \
    libzip-dev \
    nodejs \
    && docker-php-ext-install intl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath mysqli zip gd

# Install PHP redis
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www
USER $user


#CMD ["ls -la /var/www"]
#RUN chmod u+x /var/www/docker/script/start.sh
#CMD ["/var/www/docker/script/start.sh"]
