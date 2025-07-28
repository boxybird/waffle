<?php

test('it can create a database table', function (): void {
    if (!waffle_db()->schema()->hasTable('waffle_custom_table')) {
        waffle_db()->schema()->create('waffle_custom_table', function ($table): void {
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

test('it can insert a row into a database table', function (): void {
    waffle_db()->table('waffle_custom_table')->insert([
        'user_id' => 1,
        'extra_user_content' => 'Some extra content about the user.',
    ]);

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->user_id)
        ->toBe(1)
        ->and($row->extra_user_content)->toBe('Some extra content about the user.');
});

test('it can select a row from a database table', function (): void {
    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->user_id)->toBe(1);
});

test('it can update a row in a database table', function (): void {
    waffle_db()->table('waffle_custom_table')->update([
        'extra_user_content' => 'Some extra content about the user.',
    ]);

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row->extra_user_content)->toBe('Some extra content about the user.');
});

test('it can delete a row from a database table', function (): void {
    waffle_db()->table('waffle_custom_table')->delete();

    $row = waffle_db()->table('waffle_custom_table')->first();

    expect($row)->toBeNull();
});

test('it can delete a database table', function (): void {
    waffle_db()->schema()->dropIfExists('waffle_custom_table');

    $exists = waffle_db()->schema()->hasTable('waffle_custom_table');

    expect($exists)->toBeFalse();
});