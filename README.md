# create migrations
php artisan make:migration create_users_table
php artisan make:migration create_projects_table

# migrate to the database
php artisan migrate

# uncomment these from boostrap
$app->withFacades();
$app->withEloquent();


