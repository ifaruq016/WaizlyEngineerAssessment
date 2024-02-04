
# Waizly Engineering Assessment

- Backend(1) --> Laravel App
- Backend(2) --> Backend(2) Imeplent_test.text
- Problem Solving Code --> ProblemSolving folder

---

## Installation

1. Clone the repository:

    ```bash
    git clone <repository_url>
    ```

2. Install dependencies using Composer:

    ```bash
    composer install
    ```

3. Copy `.env.example` to `.env`:

    ```bash
    cp .env.example .env
    ```

4. Set up your database connection in the `.env` file.

5. Generate an application key:

    ```bash
    php artisan key:generate
    ```

6. Generate a JWT secret key:

    ```bash
    php artisan jwt:secret
    ```

## Usage

- Run the application:

    ```bash
    php artisan serve
    ```

- Visit `http://localhost:8000` in your browser to view the application.

## Testing

- Run tests:

    ```bash
    php artisan test
    ```

## Viewing Logs

- To tail the Laravel log:

    ```bash
    tail -f storage/logs/laravel.log
    ```