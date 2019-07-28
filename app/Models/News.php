<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'preview', 'body', 'publication_date'];

    protected $casts = [
        'publication_date' => 'datetime:H:m d-m-Y',
        'created_at' => 'datetime:H:m d-m-Y',
        'updated_at' => 'datetime:H:m d-m-Y',
    ];

}
