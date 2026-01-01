<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$config = require __DIR__ . '/../src/config.php';

$capsule = new Capsule;
$capsule->addConnection($config['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->dropIfExists('post_tags');
Capsule::schema()->dropIfExists('comments');
Capsule::schema()->dropIfExists('posts');
Capsule::schema()->dropIfExists('tags');
Capsule::schema()->dropIfExists('users');

Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('username')->unique();
    $table->string('password');
    $table->string('email')->nullable();
    $table->text('avatar')->nullable();
    $table->string('role')->default('user');
    $table->timestamps();
});

Capsule::schema()->create('posts', function ($table) {
    $table->increments('id');
    $table->unsignedInteger('user_id');
    $table->string('title');
    $table->text('content');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

Capsule::schema()->create('comments', function ($table) {
    $table->increments('id');
    $table->unsignedInteger('user_id');
    $table->unsignedInteger('post_id');
    $table->text('content');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
});

Capsule::schema()->create('tags', function ($table) {
    $table->increments('id');
    $table->string('name')->unique();
    $table->timestamps();
});

Capsule::schema()->create('post_tags', function ($table) {
    $table->unsignedInteger('post_id');
    $table->unsignedInteger('tag_id');

    $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
    $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

    $table->primary(['post_id', 'tag_id']);
});

echo "Таблиці успішно створено\n";