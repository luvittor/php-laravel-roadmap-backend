# Roadmap Backend

## Features & Best Practices

This project was developed with a strong emphasis on modern web development practices and a robust, maintainable architecture. For a detailed explanation of the technical decisions and implementation, please see the [DEVELOPMENT.md](DEVELOPMENT.md) file.

Key highlights include:
- Comprehensive unit testing with PHPUnit.
- A full CI/CD pipeline using GitHub Actions for linting, testing, and deployment.
- Secure API with Laravel Sanctum for token-based authentication.
- Adherence to DRY principles with custom validation rules.
- A containerized development environment with Docker.

## Development Environment

### Cloning and Defining .env file

Start by cloning and copying the example .env file:

```bash
git clone https://github.com/luvittor/php-laravel-roadmap-backend.git
cd php-laravel-roadmap-backend
cp .env.example .env
```

Now review all .env variables and set them according to your environment.

#### CORS_ALLOWED_ORIGINS

Set CORS_ALLOWED_ORIGINS with your frontend URLs separated by commas, e.g. `https://www.example.com,https://example.com` or `*` to allow all origins (recommended only for development, **NOT production**).

Note: If you change CORS_ALLOWED_ORIGINS after initial setup, you may need to clear the configuration cache to apply the changes:

```bash
php artisan config:clear
php artisan cache:clear
```

### Running the Application

By default, we provide a Docker‐based setup (Nginx + PHP-FPM) to mirror production as closely as possible. However, on Windows/WSL, this configuration can be noticeably slower due to file-system performance.

Alternatively, you can run the application directly on your host machine using **Laravel's built-in server**, which is **faster** but less production-like.

#### Docker

- Make sure you copied the `.env.example` to `.env` and configured it properly.
- Make sure Docker Desktop (or Docker Engine) is running.  
- From the project root, build and launch the containers:

```bash
# first run
docker compose up --build -d
# to check if both services are running and healthy
docker ps
# subsequent runs
docker compose up -d
```

- Wait a while for the composer dependencies to install and the application to be ready.
  - This may take a few minutes on the first run.
  - If it fails, remove the `vendor` directory before trying again.
- When services are running and healthy, you can open your browser at: <http://localhost:8000/ping>

#### Host Machine

- Make sure you copied the `.env.example` to `.env` and configured it properly.
- Make sure you have PHP (>= 8.2) and Composer installed on your host machine.

```bash
composer install
php artisan key:generate
php artisan migrate

# Start the built-in server
php artisan serve
```

- Open your browser at: <http://localhost:8000/ping>

### Running Tests

After setting up the application, you can run the tests to ensure everything is working correctly.

Our PHPUnit suite uses an in‑memory SQLite database defined in `phpunit.xml`, so you do not need a separate database.

One test unit emulates a parallel request to one endpoint and requires the `pcntl` extension to run. This test is skipped if `pcntl` is not available.

Before running tests, ensure you have copied the `.env.example` to `.env`. It is not necessary to fill any environment variables for testing, as they are overridden by `phpunit.xml`.

#### Docker

```bash
# Run tests
docker compose exec php-laravel-roadmap-backend php artisan test
# Run tests in parallel with random order
docker compose exec php-laravel-roadmap-backend php artisan test --parallel --processes=4 --order-by=random
```

#### Host Machine

```bash
# Run tests
php artisan test
# Run tests in parallel with random order
php artisan test --parallel --processes=4 --order-by=random
```

### Linting

Run Laravel Pint to ensure the code style matches the project standard.

#### Docker

```bash
# Run Pint in test mode (no changes made)
docker compose exec php-laravel-roadmap-backend composer lint
# Run Pint to fix code style issues
docker compose exec php-laravel-roadmap-backend composer lint:fix
```

#### Host Machine

```bash
# Run Pint in test mode (no changes made)
composer lint
# Run Pint to fix code style issues
composer lint:fix
```

## API Usage

To receive JSON validation errors when calling the API, include the header:

```
Accept: application/json
```

Without this header Laravel may return an HTML response on validation failure.

### Postman Collection

You can import the Postman collection and environment variables provided in this repository to quickly set up your API testing environment.

All of our Postman artifacts live in `docs/postman/`.

- **Collection**  
  `docs/postman/php-laravel-roadmap.postman_collection.json`  
  ↪ Contains all the endpoints, example bodies, headers, tests, flows, etc.

- **Environment**  
  `docs/postman/php-laravel-roadmap-localhost.postman_environment.json`
  ↪ Includes variables like `{{base_url}}`, `{{auth_token}}`, etc.

### Columns and Cards API Endpoints

| Method   | Endpoint                        | Description                       |
| -------- | ------------------------------- | --------------------------------- |
| `GET`    | `/columns/{year}/{month}/cards` | List cards for a given year/month |
| `POST`   | `/cards`                        | Create a new card                 |
| `GET`    | `/cards/{id}`                   | Retrieve a card                   |
| `PATCH`  | `/cards/{id}/title`             | Update only the card title        |
| `PATCH`  | `/cards/{id}/status`            | Update only the card status       |
| `PATCH`  | `/cards/{id}/position`          | Move card to another column/order |
| `DELETE` | `/cards/{id}`                   | Delete a card                     |
