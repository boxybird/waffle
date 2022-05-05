<?php

if (!waffle_db()->schema()->hasTable('waffle_sessions')) {
    waffle_db()->schema()->create('waffle_sessions', function ($table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->text('payload');
        $table->integer('last_activity')->index();
    });
}
