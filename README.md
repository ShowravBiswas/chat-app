# Real-Time Chat Application with Laravel & Vue 3

A real-time chat application built with Laravel (backend) and Vue 3 (frontend) using Laravel Echo and Pusher for real-time broadcasting, including a real-time "Typing..." indicator.

## Features

-   User-to-user real-time messaging
-   Real-time "Typing..." indicator
-   Private channels with Laravel Echo
-   Message timestamps with smart formatting
-   Responsive UI using Tailwind CSS
-   Axios for API calls
-   Laravel Sanctum for authentication (optional)

## Requirements

-   PHP >= 8.1
-   Composer
-   Node.js & npm
-   Laravel 10.x (or latest)
-   Vue 3
-   Pusher account (or compatible broadcasting service)
-   MySQL or other supported database
-   Redis (optional, recommended for queue/broadcasting)
-   A web server (Apache/Nginx) or Laravel Sail / Valet / Homestead

## Installation Guide

1. Clone the repository

git clone https://github.com/yourusername/your-repo.git
cd your-repo

2. Install backend dependencies

composer install

3. Install frontend dependencies

npm install
npm run dev

4. Environment Setup

cp .env.example .env

5. Generate application key

php artisan key:generate

6. Run database migrations

php artisan migrate
