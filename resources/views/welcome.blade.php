<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Roadmap</title>

    
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <header class="mb-12 text-center">
            <h1 class="text-5xl font-bold">Welcome to Laravel Roadmap</h1>
            <p class="mt-4 text-lg text-gray-600">
                A visual roadmap app built with Laravel & React
            </p>
        </header>

        <main class="w-full max-w-2xl px-6">
            <section class="bg-white shadow-md rounded-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold mb-4">Getting Started</h2>
                <p class="text-gray-700 mb-4">
                    This application is intended to help you plan tasks month by month, using boards, lists, and cards.
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>✔️ Laravel 10 (Backend API)</li>
                    <li>✔️ React 18 + Vite (Frontend)</li>
                    <li>✔️ Docker (Nginx + PHP-FPM) for production‐like environment</li>
                    <li>✔️ Built-in Laravel server (`php artisan serve`) for faster local dev</li>
                    <li>✔️ Database: MySQL</li>
                </ul>
            </section>

            <section class="bg-white shadow-md rounded-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold mb-4">Useful Links</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ url('/') }}/ping" class="text-blue-600 hover:underline">
                            🔍 Ping Endpoint
                        </a>
                        <span class="text-gray-500">— check raw response time</span>
                    </li>
                    <li>
                        <a href="https://github.com/your-username/php-laravel-roadmap-backend" target="_blank" class="text-blue-600 hover:underline">
                            🗃 Backend Repository
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/your-username/react-vite-roadmap-frontend" target="_blank" class="text-blue-600 hover:underline">
                            🎨 Frontend Repository
                        </a>
                    </li>
                    <li>
                        <a href="https://laravel.com/docs" target="_blank" class="text-blue-600 hover:underline">
                            📚 Laravel Documentation
                        </a>
                    </li>
                </ul>
            </section>

            <section class="bg-white shadow-md rounded-lg p-8">
                <h2 class="text-2xl font-semibold mb-4">Quick Commands (Local Dev)</h2>
                <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">
# Clone the backend repo
git clone git@github.com:your-username/php-laravel-roadmap-backend.git
cd php-laravel-roadmap-backend

# Install dependencies & set up .env
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Run migrations & start built-in server
php artisan serve --host=0.0.0.0 --port=8000

# OR, if using Docker:
docker-compose up -d --build
</pre>
            </section>
        </main>

        <footer class="mt-12 text-sm text-gray-500">
            &copy; {{ date('Y') }} Laravel Roadmap &middot; All Rights Reserved
        </footer>
    </div>
</body>
</html>
