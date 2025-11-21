<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieEmbedding extends Model
{
    protected $fillable = [
        'movie_id',
        'content',
        'embedded_at'
    ];

    protected $casts = [
        'embedded_at' => 'datetime'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}


