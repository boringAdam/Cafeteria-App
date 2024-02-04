
## Technologies Used
- Laravel Sail: Laravel Sail is a lightweight command-line tool for managing Laravel applications within Docker containers. It simplifies the development environment setup.
- Laravel: Laravel is a popular PHP framework known for its elegant syntax and developer-friendly features.
- Vue.js: Vue.js is a progressive JavaScript framework for building user interfaces. It's used for the frontend of this project to create dynamic and interactive components.
- MySQL: The project uses MySQL as the database to store user data, posts, likes, and comments.

### Pre-requisites
Make sure you have Docker installed on your system.

Installation
Clone the repository:

```bash
git clone https://github.com/boringAdam/Cafeteria-App
```
Navigate to the project directory:

```bash
cd cafeteria-app
```
Copy the .env.example file to .env:

```bash
cp .env.example .env
```
Start the Docker containers using Laravel Sail:


```bash
./vendor/bin/sail composer install
./vendor/bin/sail up -d
```

Run the database migrations and seed the database:

```bash
./vendor/bin/sail artisan db:seed
```

Install the frontend dependencies and build the assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

The Cafeteria App should now be accessible at http://localhost
The database is accessible with phpmyadmin configuration at http://localhost:8001
DB_USERNAME=sail
DB_PASSWORD=password

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
