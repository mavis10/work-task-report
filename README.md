# Work Task Resolution Report

## Overview

This is a small Laravel API that returns a report of work tasks grouped by resolution type within a given date range.

The goal was to keep things simple, readable, and close to real-world backend practices without over-engineering.

---

## Endpoint

GET /api/reports/work-tasks/resolutions?from=YYYY-MM-DD&to=YYYY-MM-DD

### Example

/api/reports/work-tasks/resolutions?from=2024-01-01&to=2024-12-31

---

## What it does

- Filters work tasks based on `created_at` between `from` and `to`
- Excludes:
  - Calls in `Draft`
  - Calls in `Archived`
- Ignores work tasks with no resolution type
- Groups results by resolution type
- Returns count per resolution

---

## Response Example

```json
{
  "resolution_types": [
    {
      "id": 1,
      "name": "Fix Complete – Parts Collection Required",
      "description": "Parts collection needed",
      "count": 3
    }
  ]
}

## How I approached it

I tried to keep the structure clean and avoid putting everything in one place.

- **Controller** → handles request/response only  
- **Request + DTO** → validation and passing clean data  
- **Service** → contains business logic (also handles caching)  
- **Repository** → responsible only for database queries  
- **DTO** → ensures a consistent response format  

The goal was to separate responsibilities so the code is easier to maintain and extend later.

---

## Caching

I added a small caching layer using a `DataCacheService`.

- Cache key is based on the date range  
- Default TTL is **10 minutes**  

**Important note:**  
I’m caching arrays instead of objects because Laravel cache serialization can cause issues with DTOs (you may see `__PHP_Incomplete_Class` otherwise).

---

## Testing

Feature tests cover:

- Response structure  
- Date filtering  
- Excluding Draft / Archived calls  
- Grouping by resolution type  
- Ignoring null resolution types  
- Empty result scenarios  

Cache is also cleared before each test to avoid flaky results.

---

## Things I kept simple

- No authentication (not required for this task)  
- No pagination (data volume is small for this use case)  
- No query scopes (kept query logic inside repository for clarity) 

### No Authentication

Authentication was intentionally skipped.

**Why:**
- Not required for the task
- Focus was on reporting logic and structure

**Tradeoff:**
- Not production-ready
- Would normally add Sanctum or JWT


###  No Pagination

Pagination was not added.

**Why:**
- Aggregated report data is small
- Simpler response for this use case

**Tradeoff:**
- Could become an issue with large datasets

---

## If I had more time

For a production-ready setup, I would:

- Use **Redis** instead of file cache  
- Add **cache tags** for better invalidation  
- Add authentication (likely using Sanctum)  
- Add Pagination  
- Add API rate limiting  
- Add OpenAPI / Swagger documentation  

---

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

php artisan serve

## Run tests
php artisan test