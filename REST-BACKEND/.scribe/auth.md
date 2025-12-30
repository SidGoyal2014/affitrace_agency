# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {ACCESS_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Authenticate using Laravel Sanctum. First call <code>POST /api/login</code> to obtain an access token, then include it as <code>Bearer {ACCESS_TOKEN}</code> in the Authorization header for protected endpoints.
