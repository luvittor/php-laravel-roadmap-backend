# Roadmap Backend

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

Set CORS_ALLOWED_ORIGINS with your frontend URLs separated by commas, e.g. `https://www.example.com,https://example.com` or `*` to allow all origins (recommended only for development).

Note: If you change CORS_ALLOWED_ORIGINS after initial setup, you may need to clear the configuration cache to apply the changes:

```bash
php artisan config:clear
php artisan cache:clear
```

### Running the Application

By default, we provide a Docker‐based setup (Nginx + PHP-FPM) to mirror production as closely as possible. However, on Windows/WSL this configuration can be noticeably slower due to file-system performance.

Alternatively, you can run the application directly on your host machine using **Laravel's built-in server**, which is **faster** but less production-like.

#### Docker

- Make sure you copied the `.env.example` to `.env` and configured it properly.
- Make sure Docker Desktop (or Docker Engine) is running.  
- From the project root, build and launch the containers:

```bash
docker-compose up --build
```

- Wait a while for the composer dependencies to install.
  - This may take a few minutes on the first run.
  - If it fails, remove the `vendor` directory and before trying again.
- When you see this: `NOTICE: ready to handle connections` the application is ready.
- Open your browser at: http://localhost:8080/ping

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

- Open your browser at: http://localhost:8000/ping

### Running Tests

After setting up the application, you can run the tests to ensure everything is working correctly.

Our PHPUnit suite uses an in‑memory SQLite database defined in `phpunit.xml`, so you do not need a separate database.

#### Docker

```bash
docker exec -it php-laravel-roadmap-backend php artisan test
```

#### Host Machine

```bash
php artisan test
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
  ↪ Contains all of the endpoints, example bodies, headers, tests, flows, etc.

- **Environment**  
  `docs/postman/php-laravel-roadmap-localhost.postman_environment.json`
  ↪ Includes variables like `{{base_url}}`, `{{auth_token}}`, etc.

### Columns and Cards API Endpoints

| Method   | Endpoint                        | Description                       |
| -------- | ------------------------------- | --------------------------------- |
| `GET`    | `/columns/{year}/{month}/cards` | List cards for a given column     |
| `POST`   | `/cards`                        | Create a new card                 |
| `GET`    | `/cards/{id}`                   | Retrieve a card                   |
| `PATCH`  | `/cards/{id}/title`             | Update only the card title        |
| `PATCH`  | `/cards/{id}/status`            | Update only the card status       |
| `PATCH`  | `/cards/{id}/position`          | Move card to another column/order |
| `DELETE` | `/cards/{id}`                   | Delete a card                     |


### Database Notes

For information on transaction isolation and how duplicate columns are handled see [docs/database.md](docs/database.md).
