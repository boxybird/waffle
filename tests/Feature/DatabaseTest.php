<?php

test('can create database table', function () {
    if (!waffle_db()->schema()->hasTable('waffle_custom_table')) {
        waffle_db()->schema()->create('waffle_custom_table', function ($table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('extra_user_content');
            $table->timestamp('created_at')->default(waffle_db()::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(waffle_db()::raw('CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('ID')->on('wp_users');
        });
    }

    $exists = waffle_db()->schema()->hasTable('waffle_custom_table');

    expect($exists)->toBeTrue();
});

test('can get insert row', function () {
    waffle_db()->table('waffle_custom_table')->insert([
        'user_id' => 1,
        'extra_user_content' => 'Some extra content about the user.',
    ]);

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->user_id)
        ->toBe(1)
        ->and($row->extra_user_content)->toBe('Some extra content about the user.');
});

test('can get select row', function () {
    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->user_id)->toBe(1);
});

test('can get update row', function () {
    waffle_db()->table('waffle_custom_table')->update([
        'extra_user_content' => 'Some extra content about the user.',
    ]);

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->extra_user_content)->toBe('Some extra content about the user.');
});

test('can get delete row', function () {
    waffle_db()->table('waffle_custom_table')->delete();

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row)->toBeNull();
});

test('can delete database table', function () {
    waffle_db()->schema()->dropIfExists('waffle_custom_table');

    $exists = waffle_db()->schema()->hasTable('waffle_custom_table');

    expect($exists)->toBeFalse();
});
