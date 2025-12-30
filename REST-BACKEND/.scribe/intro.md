# Introduction

RESTful API for Affitrace affiliate tracking platform. Supports Admin, Affiliate, and Advertiser operations.

<aside>
    <strong>Base URL</strong>: <code>http://82.29.160.75/</code>
</aside>

Welcome to the Affitrace API documentation. This API provides endpoints for managing affiliate marketing campaigns, tracking clicks and conversions, and generating reports.

<aside>The API is organized around REST. All request and response bodies are JSON encoded. Authentication is required for most endpoints using Bearer tokens (Laravel Sanctum).</aside>

## User Roles
- **Admin**: Full platform management (advertisers, affiliates, offers, reports)
- **Affiliate**: View assigned offers, tracking URLs, and performance reports  
- **Advertiser**: Manage their offers and view reports

