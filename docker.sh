cp .env.example .env
docker-compose up -d
docker-compose exec app composer update --no-scripts
docker-compose exec app composer dump-autoload
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan config:cache
docker-compose exec app composer install
docker-compose exec app php artisan migrate

# cd /var/www/html
docker-composer exec app chown -R www-data storage