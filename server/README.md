## Project setup
```
composer install
```

### Copy the example env file and make the required configuration changes in the .env file
```
cp .env.example .env
```

### Run the database migrations (Set the database connection in .env before migrating)
```
php artisan migrate
```

### Start the local development server
```
php artisan serve
```

### Build for production (remember to update the database credentials to be the credentials for the live database)
```
npm run prod
```