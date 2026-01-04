# API Documentation Generation Guide

This guide explains how to generate and manage Scribe API documentation for the Affitrace backend.

> **Important**: API documentation should only be generated in local/development environments, NOT on production servers.

---

## Prerequisites

- PHP 8.1+
- Composer installed
- Laravel application running locally

---

## Generating Documentation

### Step 1: Navigate to Project Directory

```bash
cd c:\xampp2\htdocs\affitrace\affitrace_REST\1\REST-BACKEND
```

### Step 2: Run Scribe Generate Command

```bash
php artisan scribe:generate
```

Or with force flag to overwrite existing docs:

```bash
php artisan scribe:generate --force
```

### Step 3: Access Documentation

Open in browser:
- **Local**: `http://localhost:8000/docs`
- **If using XAMPP**: `http://localhost/affitrace/affitrace_REST/1/REST-BACKEND/public/docs/index.html`

---

## Generated Files

After running the command, these files are created in `public/docs/`:

| File | Description |
|------|-------------|
| `index.html` | Interactive HTML documentation |
| `collection.json` | Postman collection (import into Postman) |
| `openapi.yaml` | OpenAPI 3.0 specification |

---

## Configuration File

Location: `config/scribe.php`

### Key Settings

| Setting | Current Value | Description |
|---------|---------------|-------------|
| `title` | Affitrace API Documentation | Page title |
| `type` | static | Generates static HTML files |
| `auth.enabled` | true | Shows auth requirements |
| `auth.default` | true | Endpoints require auth by default |

---

## Adding Documentation to Controllers

### Group Annotation (Required)

Add to the class docblock:

```php
/**
 * @group Admin - Offers
 *
 * Endpoints for managing offers.
 */
class OfferController extends Controller
```

### Mark Public Endpoints (No Auth)

```php
/**
 * @unauthenticated
 */
public function publicEndpoint()
```

### Document Parameters

```php
/**
 * @bodyParam email string required User email. Example: user@example.com
 * @bodyParam password string required User password. Example: secret123
 * @queryParam page integer Page number. Example: 1
 * @urlParam id integer required The offer ID. Example: 5
 */
```

### Document Responses

```php
/**
 * @response 200 {"success": true, "data": {...}}
 * @response 401 {"error": "Unauthorized"}
 * @response 422 {"errors": {"email": ["Required"]}}
 */
```

---

## Production Deployment

### Do NOT Deploy Documentation

Add to `.gitignore`:

```gitignore
/public/docs
```

### Alternative: Delete After Deploy

In your deployment script:

```bash
rm -rf public/docs
```

### Why Avoid Docs on Production?

1. **Security**: Exposes API structure to attackers
2. **Performance**: Unnecessary static files
3. **Information Disclosure**: Shows internal endpoints

---

## Troubleshooting

### Command Not Found

Use the local Composer PHAR:

```bash
php composer.phar require knuckleswtf/scribe --dev
```

### Skipped Routes

If routes are skipped, check:
1. Route has a valid controller method
2. Controller file has no syntax errors
3. Route is within the `api/*` prefix

### Regenerate After Changes

Always regenerate after:
- Adding new endpoints
- Changing request parameters
- Modifying response structure
- Adding/removing authentication

```bash
php artisan scribe:generate --force
```

---

## Quick Reference

| Action | Command |
|--------|---------|
| Generate docs | `php artisan scribe:generate` |
| Force regenerate | `php artisan scribe:generate --force` |
| Clear cache first | `php artisan config:clear && php artisan scribe:generate` |

---

## Related Files

- Configuration: `config/scribe.php`
- Output: `public/docs/`
- Source Markdown: `.scribe/` (intermediate files)
