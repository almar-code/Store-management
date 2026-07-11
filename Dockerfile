FROM php:8.2-fpm

# 1. تثبيت خادم Nginx والأدوات الأساسية لتشغيل الويب
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nginx

# 2. تثبيت إضافات PHP ليتعامل Laravel مع قاعدة البيانات
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. جلب أداة Composer لإدارة الحزم
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. إعداد خادم Nginx ليفتح المنفذ (7860) الخاص بـ Hugging Face ويقرأ مجلد public
RUN echo 'server { \n\
    listen 7860;\n\
    root /var/www/public;\n\
    index index.php index.html;\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    location ~ \.php$ {\n\
        include fastcgi_params;\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
    }\n\
}' > /etc/nginx/sites-available/default

# 5. تحديد المجلد الرئيسي داخل السيرفر
WORKDIR /var/www

# 6. نسخ ملفات مشروعك بالكامل إلى السيرفر
COPY . .

# 7. تشغيل الـ Composer لتثبيت المكتبات وتحسين الأداء
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 8. إعطاء الصلاحيات لـ Laravel لكي يستطيع الكتابة وتخزين الكاش (Storage)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/lib/nginx /var/log/nginx

# 9. إخبار Hugging Face بالمنفذ المفتوح
EXPOSE 7860

# 10. تشغيل السيرفر فور إقلاع الموقع
CMD service nginx start && php-fpm