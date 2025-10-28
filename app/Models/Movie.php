<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'poster_url',
        'trailer_url',
        'duration_minutes',
        'base_price',
        'release_date',
        'age_rating',
        'status',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'base_price' => 'decimal:2',
        'release_date' => 'date',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movie');
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'actor_movie');
    }

    public function directors()
    {
        return $this->belongsToMany(Director::class, 'director_movie');
    }
}
