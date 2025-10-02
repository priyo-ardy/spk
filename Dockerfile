# Gunakan image dasar PHP 8.4 dengan apache
FROM php:8.4-apache

#Set working directory di dalam container
WORKDIR /var/www/html

# Install ekstensi PHP yang diperlukan untuk codeigniter 4
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP dari sumber (intl, gd)
RUN docker-php-ext-install -j$(nproc) intl gd

# Install ekstensi MySQLi dan pdo_mysql
RUN docker-php-ext-install -j$(nproc) mysqli pdo_mysql

# Aktifkan Apache rewrite module (penting untuk CodeIgniter)
RUN a2enmod rewrite

# Konfigurasi Apache Virtual Host:
# Buat file vhost.conf yang menunjuk ke folder public CodeIgniter
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Hapus isi default web root Apache dan copy aplikasi Anda
COPY . /var/www/html/

# Ubah permission untuk folder CodeIgniter yang harus bisa ditulis
# (writable) oleh user web server (biasanya www-data)
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Port 80 adalah port default Apache di dalam container
EXPOSE 80