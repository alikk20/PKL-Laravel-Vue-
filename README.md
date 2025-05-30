Bahan
1. php 8.3.21
2. mariadb 10.11.11
3. node 18.19.0

-----DEPLOYMENT Backend-----
1. git clone https://github.com/alikk20/PKL-Laravel-Vue-.git
2. create db
3. composer install
4. config .env
5. set sites-enabled apache
6. sudo chown -R www-data:www-data /var/www/html/
7. a2enmod rewrite, systemctl restart apache2
8. apt install php libapache2-mod-php (error handle request)

-----PROJECT-----
1. php artisan migrate
2. php artisan key:generate
3. php artisan jwt:secret -> JWT_SECRET= <token>
4. php artisan storage:link
5. php artisan shield:install
6. php artisan shield:generate
7. php artisan make:filament-user
8. php artisan tinker
       use App\Models\User;
       use Spatie\Permission\Models\Permission;
       $user = User::find(1);
       $user->givePermissionTo(Permission::all());
9. php artisan cache:clear
10. php artisan view:clear
11. php artisan config:clear
