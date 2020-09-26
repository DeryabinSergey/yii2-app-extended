FROM yiisoftware/yii2-php:7.4-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/frontend/web|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i -e 's|</Directory>|\n\tRewriteEngine on\n\n\tRewriteRule ^index.php/ - [L,R=404]\n\n\tRewriteCond %{REQUEST_FILENAME} !-f\n\tRewriteCond %{REQUEST_FILENAME} !-d\n\n\tRewriteRule . index.php\n</Directory>|g' /etc/apache2/sites-available/000-default.conf