# MaroCOD

A headless REST API for Moroccan merchants who sell with Cash on Delivery (COD). It manages the order lifecycle, calculates shipping cost from carrier rates, expires stale orders automatically, and reports net cash with returns counted as a cost.

**Stack:** Laravel 13, PHP 8.3, MySQL, Sanctum

## Features

- Merchant register, login and logout with bearer tokens
- Each merchant only sees and changes their own orders
- Order lifecycle enforced by a state machine, with clear 422 errors
- Carrier assignment with automatic shipping cost per city
- Hourly scheduled command that expires orders pending for more than 48 hours
- Analytics: status breakdown, shipping spend over time, net cash, delivery success rate
- Cheapest carrier per city

## Quick start

Requires PHP 8.3+, Composer and MySQL.

```bash
git clone https://github.com/Unes-Fah/MaroCOD.git
cd marocod
composer install
cp .env.example .env        # Windows PowerShell: copy .env.example .env
php artisan key:generate
```

Create an empty MySQL database named `marocod` and set the `DB_*` values in `.env`. Then:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

The API runs at `http://localhost:8000/api`.

**Demo login:** `demo@marocod.test` / `secret123`

`php artisan migrate:fresh --seed` resets the database to its seeded state at any time.

## Using the API

Every endpoint except register and login needs the header `Authorization: Bearer <token>`. A Postman collection that saves the token automatically is in `docs/MaroCOD.postman_collection.json`.

| Method | Path | Description |
|---|---|---|
| POST | `/register` | Create a merchant, returns a token |
| POST | `/login` | Returns a token |
| POST | `/logout` | Revokes the current token |
| GET | `/carriers` | All carriers with their city rates |
| GET | `/carriers/{id}` | One carrier with its rates |
| GET | `/carriers/cheapest` | Cheapest carrier per city. Optional `?city=Fes` |
| GET | `/orders` | Your orders |
| POST | `/orders` | Create an order (starts `pending`) |
| PATCH | `/orders/{id}/carrier` | Assign a carrier: `{"carrier_id": 1}` |
| PATCH | `/orders/{id}/status` | Change status: `{"status": "shipped", "tracking_number": "AM123456"}` |
| GET | `/stats/status-breakdown` | Count and percentage per status |
| GET | `/stats/shipping-costs` | Shipping spend. Optional `?period=day\|month\|year` (default `month`) |
| GET | `/stats/financial-summary` | Revenue, shipping paid, net cash, success rate |

Status codes: `200` ok, `201` created, `401` missing or invalid token, `403` order belongs to another merchant, `404` not found, `422` validation error or broken business rule.

### Order lifecycle

| From | Can become |
|---|---|
| pending | confirmed, cancelled, expired |
| confirmed | shipped, cancelled |
| shipped | delivered, returned |
| delivered, returned, cancelled, expired | nothing (final) |

Rules:

- Shipping requires an assigned carrier and a tracking number.
- A carrier can only be assigned or changed while the order is `pending` or `confirmed`.
- The carrier must deliver to the order's city. Its price is saved on the order as `shipping_cost`.

An illegal change returns the allowed options:

```json
{
  "message": "Cannot change order status from pending to delivered.",
  "from": "pending",
  "to": "delivered",
  "allowed": ["confirmed", "cancelled", "expired"]
}
```

### Financial summary

`GET /stats/financial-summary` returns:

```json
{
  "revenue": 2846.49,
  "shipping_paid": 245,
  "net_cash": 2601.49,
  "delivered_count": 3,
  "returned_count": 3,
  "success_rate": 50
}
```

- `revenue`: total of delivered orders
- `shipping_paid`: shipping cost of delivered **and returned** orders
- `net_cash`: `revenue - shipping_paid` (product cost is not tracked)
- `success_rate`: `delivered / (delivered + returned)` in percent, `null` if nothing has closed yet

## Scheduled command

```bash
php artisan orders:audit-pending               # expire orders pending > 48h
php artisan orders:audit-pending --hours=24    # custom threshold
php artisan orders:audit-pending --dry-run     # report only, change nothing
```

It is scheduled hourly in `routes/console.php`. For local testing, run `php artisan schedule:work`. In production, add this cron entry:

```
* * * * * cd /path/to/marocod && php artisan schedule:run >> /dev/null 2>&1
```

## Tests

```bash
php artisan test
```

The feature tests wipe the configured test database. Use a separate empty database (for example `marocod_test`) set in `.env.testing`, never your development one.

## Production notes

Set `APP_DEBUG=false`, serve over HTTPS, and never commit `.env`.
