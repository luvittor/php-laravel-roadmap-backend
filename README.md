# Roadmap Backend

## Defining .env

### CORS_ALLOWED_ORIGINS

Set CORS_ALLOWED_ORIGINS with your frontend URLs separated by commas, e.g. `https://www.example.com,https://example.com` or * to allow all origins (recommended only for development).

When changing CORS_ALLOWED_ORIGINS, run:

```bash
php artisan config:clear
php artisan cache:clear
```

## Development Environment

By default, we provide a Docker‐based setup (Nginx + PHP-FPM) to mirror production as closely as possible. However, on Windows/WSL this configuration can be noticeably slower due to file-system performance. You have two ways to run the app locally:

1. **Docker (Recommended for Production Parity)**  
- Make sure Docker Desktop (or Docker Engine) is running.  

- From the project root, build and launch the containers:
 ```bash
 docker-compose up -d --build
 ```

- Open your browser at: http://localhost:8080/ping
  
2. **Laravel Built-in Server (Faster on WSL/Windows)**  

If you experience slowness with Docker, you can run the app directly with Laravel’s development server:
```bash
# Ensure your local PHP (>= 8.2), Composer dependencies, and .env are set up
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Start the built-in server:
php artisan serve --host=0.0.0.0 --port=8000
```

- Open your browser at: http://localhost:8000/ping

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
  ↪ Contains all of the endpoints, example bodies, headers, etc.

- **Environment**  
  `docs/postman/php-laravel-roadmap-localhost.postman_environment.json`
  ↪ Includes variables like `{{base_url}}`, `{{auth_token}}`, etc.

### Card API Endpoints

| Method | Endpoint | Description |
| ------ | -------- | ----------- |
| `GET` | `/columns/{year}/{month}/cards` | List cards for a given column |
| `POST` | `/cards` | Create a new card |
| `GET` | `/cards/{id}` | Retrieve a card |
| `PATCH` | `/cards/{id}/title` | Update only the card title |
| `PATCH` | `/cards/{id}/status` | Update only the card status |
| `PATCH` | `/cards/{id}/position` | Move card to another column/order |
| `DELETE` | `/cards/{id}` | Delete a card |
