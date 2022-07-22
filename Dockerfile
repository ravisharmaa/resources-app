FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get update && apt-get install -y mariadb-client

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql mbstring exif pcntl bcmath gd && \
    docker-php-ext-enable mysqli

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install node js
RUN curl --silent --location https://deb.nodesource.com/setup_15.x | bash - > /dev/null
RUN apt-get install --yes nodejs build-essential > /dev/null
RUN mkdir /tmp/npm && \
    npm set cache --global /tmp/npm && \
    chmod -R 777 /tmp/npm

RUN chmod -R 777 /home
RUN apt-get clean > /dev/null && \
    rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]