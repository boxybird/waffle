<?php

use BoxyBird\Waffle\App;
use Illuminate\Cache\CacheManager;

App::getInstance()->singleton('cache', function ($app) {
    // Create the cache table if it doesn't exist.
    if (!get_option('waffle_cache_table_exists')) {
        update_option('waffle_cache_table_exists', true, true);

        // Double check if cache table doesn't
        // exist as transient may be manually deleted.
        if (!waffle_db()->schema()->hasTable('waffle_cache')) {
            waffle_db()->schema()->create('waffle_cache', function ($table) {
                $table->string('key')->unique();
                $table->text('value');
                $table->integer('expiration');
            });
        }
    }

    return new CacheManager($app);
});
