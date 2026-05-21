FROM php:5.6-apache

# Debian Stretch is EOL — point apt to the archive
RUN printf "deb http://archive.debian.org/debian stretch main\ndeb http://archive.debian.org/debian-security stretch/updates main\n" > /etc/apt/sources.list \
    && printf "Acquire::Check-Valid-Until \"false\";\nAcquire::AllowInsecureRepositories \"true\";\nAcquire::AllowDowngradeToInsecureRepositories \"true\";\n" > /etc/apt/apt.conf.d/99-archive

RUN apt-get -o Acquire::AllowInsecureRepositories=true update \
    && apt-get install -y --allow-unauthenticated --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libxml2-dev \
        zlib1g-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd mysql mysqli pdo pdo_mysql xml zip

RUN a2enmod rewrite

# Serve the redirect index.html (which forwards to _admin_/index.php) before the PEAR index.php.
# Loaded after docker-php.conf so it wins.
RUN echo "DirectoryIndex index.html index.php" > /etc/apache2/conf-available/zz-directory-index.conf \
    && a2enconf zz-directory-index

RUN echo "upload_max_filesize=64M\npost_max_size=64M\nmemory_limit=256M\nmax_execution_time=300\ndate.timezone=America/Argentina/Buenos_Aires" \
    > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
EXPOSE 80
