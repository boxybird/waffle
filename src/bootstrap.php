<?php

if (php_sapi_name() === 'cli') {
    require_once __DIR__.'/../../wp-load.php';
}

/**
 * Require app binding
 */
require_once __DIR__.'/bindings/app.php';

/**
 * Require config binding
 */
require_once __DIR__.'/bindings/config.php';

/**
 * Require exception handler binding
 */
require_once __DIR__.'/bindings/exception-handler.php';

/**
 * Require files binding
 */
require_once __DIR__.'/bindings/files.php';

/**
 * Require files storage binding
 */
require_once __DIR__.'/bindings/storage.php';

/**
 * Require the guard binding
 */
require_once __DIR__.'/bindings/guard.php';

/**
 * Require database binding
 */
require_once __DIR__.'/bindings/db.php';

/**
 * Require scheduler binding
 */
require_once __DIR__.'/bindings/scheduler.php';

/**
 * Require event binding
 */
require_once __DIR__.'/bindings/events.php';

/**
 * Require queue binding
 */
require_once __DIR__.'/bindings/queue.php';

/**
 * Require worker binding
 */
require_once __DIR__.'/bindings/worker.php';

/**
 * Require cache binding
 */
require_once __DIR__.'/bindings/cache.php';

/**
 * Require session binding
 */
require_once __DIR__.'/bindings/session.php';

/**
 * Require validation binding
 */
require_once __DIR__.'/bindings/validation.php';

/**
 * Require the request binding
 */
require_once __DIR__.'/bindings/request.php';

/**
 * Require the encrypter binding
 */
require_once __DIR__.'/bindings/encrypter.php';

/**
 * Require the router binding
 */
require_once __DIR__.'/bindings/router.php';

/**
 * Require helper functions
 */
require_once __DIR__.'/inc/functions.php';

/**
 * Require Admin Pages
 */
require_once __DIR__.'/inc/admin-pages.php';

/**
 * Require Sessions handler
 */
require_once __DIR__.'/inc/sessions.php';
