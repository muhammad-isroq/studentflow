<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'excerpt',
        'body',
        'published_at',
        'type',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {     
            'news' => 'Berita',
            'article' => 'Artikel',
            default => ucfirst($this->type),
        };
    }
}
