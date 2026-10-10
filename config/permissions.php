<?php

/**
 * Permission matrix config.
 * Keys are menu slugs; values list the actions that exist for that menu.
 * Convention: <menu>.<action>  e.g. products.create
 *
 * Actions:   view | create | update | delete | restore
 * "view" covers: menu visibility, route access, listing.
 */

return [

    // ── User & access management ─────────────────────────────────────────────
    'users' => ['view', 'create', 'update', 'delete', 'restore'],
    'roles' => ['view', 'create', 'update', 'delete'],
    'audit-log' => ['view'],

    // ── Appointments & availability ──────────────────────────────────────────
    'appointments' => ['view', 'create', 'update', 'delete', 'restore'],
    'availability' => ['view', 'create', 'update', 'delete'],
    'services' => ['view', 'create', 'update', 'delete', 'restore'],
    'clients' => ['view', 'create', 'update', 'delete'],
    'gurus' => ['view', 'create', 'update', 'delete', 'restore'],

    // ── Donations ────────────────────────────────────────────────────────────
    'donation-categories' => ['view', 'create', 'update', 'delete', 'restore'],
    'donations' => ['view', 'create', 'update', 'delete'],

    // ── Vastra Store ─────────────────────────────────────────────────────────
    'matajis' => ['view', 'create', 'update', 'delete', 'restore'],
    'categories' => ['view', 'create', 'update', 'delete', 'restore'],
    'products' => ['view', 'create', 'update', 'delete', 'restore'],
    'inventory' => ['view', 'create', 'update'],
    'vendors' => ['view', 'create', 'update', 'delete', 'restore'],

    // ── Mataji saree orders (Guruji module) ──────────────────────────────────
    'mataji-orders' => ['view', 'create', 'update', 'delete', 'restore'],

    // ── Samagri shop orders (customer orders from app) ───────────────────────
    'samagri-orders' => ['view', 'update', 'delete', 'restore'],

    // ── Notifications ────────────────────────────────────────────────────────
    'notifications' => ['view', 'create', 'update', 'delete'],

    // ── Location data ────────────────────────────────────────────────────────
    'locations' => ['view', 'create', 'update', 'delete'],

    // ── Panchang / API ───────────────────────────────────────────────────────
    'panchang' => ['view'],
    'api-docs' => ['view'],

    // ── App Content / Promotions ─────────────────────────────────────────────
    'promotions' => ['view', 'create', 'update', 'delete', 'restore'],

    // ── Articles / Blog ───────────────────────────────────────────────────────
    'articles' => ['view', 'create', 'update', 'delete', 'restore'],

    // ── Guruji-only self-service menus ────────────────────────────────────────
    'my-poojan' => ['view', 'update'],   // Guruji manages their assigned services
    'my-seva'   => ['view', 'update'],   // Guruji manages their assigned donation categories
    'my-store'  => ['view', 'create', 'update', 'delete'], // Guruji manages own products/categories
];
