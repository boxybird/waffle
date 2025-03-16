<?php

/**
 * Require app binding
 */
require __DIR__ . '/bindings/app.php';

/**
 * Require config binding
 */
require __DIR__ . '/bindings/config.php';

/**
 * Require exception handler binding
 */
require __DIR__ . '/bindings/exception-handler.php';

/**
 * Require files binding
 */
require __DIR__ . '/bindings/files.php';

/**
 * Require files storage binding
 */
require __DIR__ . '/bindings/storage.php';

/**
 * Require the guard binding
 */
require __DIR__ . '/bindings/guard.php';

/**
 * Require database binding
 */
require __DIR__ . '/bindings/db.php';

/**
 * Require event binding
 */
require __DIR__ . '/bindings/events.php';

/**
 * Require queue binding
 */
require __DIR__ . '/bindings/queue.php';

/**
 * Require worker binding
 */
require __DIR__ . '/bindings/worker.php';

/**
 * Require cache binding
 */
require __DIR__ . '/bindings/cache.php';

/**
 * Require session binding
 */
require __DIR__ . '/bindings/session.php';

/**
 * Require validation binding
 */
require __DIR__ . '/bindings/validation.php';

/**
 * Require the request binding
 */
require __DIR__ . '/bindings/request.php';

/**
 * Require the encrypter binding
 */
require __DIR__ . '/bindings/encrypter.php';

/**
 * Require helper functions
 */
require_once __DIR__ . '/inc/functions.php';

/**
 * Require Admin Pages
 */
require_once __DIR__ . '/inc/admin-pages.php';

/**
 * Require Sessions handler
 */
require_once __DIR__ . '/inc/sessions.php';
