export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
./vendor/laravel/dusk/bin/chromedriver-linux &
cp .env.testing .env
php artisan serve &