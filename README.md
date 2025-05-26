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
