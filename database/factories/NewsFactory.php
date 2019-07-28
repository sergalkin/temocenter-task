<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\News;
use Faker\Generator as Faker;

$factory->define(News::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'preview' => $faker->realText(30),
        'body' => $faker->realText(),
        'publication_date' => $faker->dateTimeBetween('now','+1 year'),
    ];
});
