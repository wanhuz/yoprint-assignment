# YoPrint Laravel CSV Upload Assignment

This is a mini Laravel project for the YoPrint coding assignment.  
It allows users to upload CSV files containing product data, processes them in the background, and displays the upload history with real-time status updates.

---

## Features

- Upload CSV files via a simple web interface
- Background processing of CSV using **Laravel Horizon**
- Idempotent and upsert behavior: same CSV can be uploaded multiple times without creating duplicate products
- Real-time status updates of uploads (polling every few seconds)
- History of all previous uploads with status (`pending`, `processing`, `completed`, `failed`)
- UTF-8 cleanup for CSV contents
- Optional: Can track errors if file processing fails

---

## CSV Format

| Field | Required |
|-------|----------|
| UNIQUE_KEY | Yes (used for upsert) |
| PRODUCT_TITLE | Yes |
| PRODUCT_DESCRIPTION | No |
| STYLE# | No |
| SANMAR_MAINFRAME_COLOR | No |
| SIZE | No |
| COLOR_NAME | No |
| PIECE_PRICE | No |

---

## Tech Stack

- **Backend:** Laravel 10
- **Database:** SQLite (default, easy to set up)
- **Queue / Background Jobs:** Redis + Laravel Horizon
- **Frontend:** Blade templates + Axios for real-time updates
- **CSV Handling:** PHP built-in `fgetcsv()`

---

## Installation

1. Clone the repository:
```bash
git clone <your-repo-url>
cd yoprint-test
```

2. Install dependencies:

```bash
composer install
```

3. Create environment file:

```bash
cp .env.example .env
```

4. Create SQLite database file:

```bash
touch /absolute/path/to/db.sqlite
```

5. Configure .env for SQLite:

```bash
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/db.sqlite
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

6. Run migrations:

```bash
php artisan migrate
```

7. Start Redis server (if not running):
```bash
sudo service redis-server start
```

8. Start Horizon in a separate terminal:

```bash
php artisan horizon
```

9. Start the Laravel server:
```bash
php artisan serve
```

## Usage

1. Visit the app in your browser: [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
2. Upload a CSV file via the form.
3. View the list of uploads and their status:
   - `pending` → waiting to be processed
   - `processing` → currently being processed
   - `completed` → finished successfully
   - `failed` → encountered an error
4. The table refreshes automatically every 3 seconds to show real-time status.

## Project Structure

- `app/Models/Upload.php` → tracks uploaded CSV files
- `app/Models/Product.php` → stores products and handles upserts
- `app/Jobs/ProcessCsvJob.php` → background job for CSV processing
- `resources/views/index.blade.php` → UI for file upload and status
