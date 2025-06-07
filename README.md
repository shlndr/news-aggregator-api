# News Aggregator API

A Dockerized Laravel RESTful API for aggregating news from multiple sources, with user authentication, article management, user preferences, personalized feeds, and scheduled news fetching.

---

## Features
- User registration, login, logout (Laravel Sanctum)
- CRUD for articles (with pagination, search, filtering)
- User preferences (category, source)
- Personalized news feed
- Aggregates news from NewsAPI, The Guardian, and NYT
- Scheduled news fetching (via Artisan command)
- PostgreSQL database
- Caching and rate limiting
- Feature tests
- Dockerized for easy setup

---

## Getting Started

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Git](https://git-scm.com/downloads)

### Clone the Repository
```sh
# Clone your repo
git clone https://github.com/shlndr/news-aggregator-api
cd news-aggregator-api
```

### Environment Setup
1. Copy `.env.example` to `.env` in the `src/` directory and set your values:
   - Database: PostgreSQL (see `docker-compose.yml` for credentials)
   - News API keys: `NEWSAPI_KEY`, `GUARDIAN_KEY`, `NYT_KEY`
   
Example:
```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

NEWSAPI_KEY=your_newsapi_key
GUARDIAN_KEY=your_guardian_key
NYT_KEY=your_nyt_key
```

### Build and Start Containers
```sh
docker compose build
docker compose up
```

### Run Migrations
```sh
docker compose exec app php artisan migrate
```

### Fetch News (Manual)
```sh
docker compose exec app php artisan app:fetch-news
```

### Schedule News Fetching (Optional)
```sh
docker compose exec app php artisan schedule:work
```

---

## API Usage

### Authentication
- Register: `POST /api/register`
- Login: `POST /api/login`
- Logout: `POST /api/logout` (Bearer token required)

### Articles
- List: `GET /api/articles`
- Create: `POST /api/articles`
- Show: `GET /api/articles/{id}`
- Update: `PUT/PATCH /api/articles/{id}`
- Delete: `DELETE /api/articles/{id}`
- Search: `GET /api/articles?search=keyword`
- Filter: `GET /api/articles?author=...&published_at=...`

### Preferences
- List: `GET /api/preferences`
- Create: `POST /api/preferences`
- Update: `PUT/PATCH /api/preferences/{id}`
- Delete: `DELETE /api/preferences/{id}`

### Personalized Feed
- `GET /api/feed/personalized`

**All endpoints (except register/login) require Bearer token authentication.**

---

## Testing

Run feature and unit tests:
```sh
docker compose exec app php artisan test
```

---

## Caching & Rate Limiting
- Article and personalized feed endpoints are cached for 10 minutes.
- All API endpoints are rate-limited (60 requests per minute per user).

---

## API Documentation (Swagger)
- Access the Swagger documentation at `/api/documentation`.
- To regenerate the Swagger docs, run:
  ```sh
  docker compose exec app php artisan l5-swagger:generate
  ```

---

## License
MIT

---

## First-Time User Guide

### Step 1: Clone and Setup
1. Clone the repository:
   ```sh
   git clone https://github.com/shlndr/news-aggregator-api
   cd news-aggregator-api
   ```
2. Copy `.env.example` to `.env` in the `src/` directory and update your environment variables (database, API keys, etc.).

### Step 2: Start the Application
1. Build and start the Docker containers:
   ```sh
   docker compose build
   docker compose up
   ```
2. Run migrations to set up the database:
   ```sh
   docker compose exec app php artisan migrate
   ```

### Step 3: Fetch News
- Manually fetch news:
  ```sh
  docker compose exec app php artisan app:fetch-news
  ```
- Or schedule news fetching:
  ```sh
  docker compose exec app php artisan schedule:work
  ```

### Step 4: Explore the API
- Register a new user: `POST /api/register`
- Login to get your Bearer token: `POST /api/login`
- Use the token to access protected endpoints (e.g., `GET /api/articles`, `GET /api/feed/personalized`).
- Check the Swagger documentation at `/api/documentation` for detailed API usage.

### Step 5: Run Tests
- Run the test suite:
  ```sh
  docker compose exec app php artisan test
  ```

---

## Troubleshooting
- If you encounter issues, check the Docker logs:
  ```sh
  docker compose logs
  ```
- Ensure your `.env` file is correctly configured.
- Verify that all required services (PostgreSQL, Redis) are running.
