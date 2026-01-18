<?php

// Tenant-specific routes are now served through the central API definition in
// routes/web.php using the custom tenant.header middleware. This file is kept
// intentionally empty so Stancl Tenancy's service provider can continue to
// include it without registering duplicate route definitions.
use Illuminate\Support\Facades\Route;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
