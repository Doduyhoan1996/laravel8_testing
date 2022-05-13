<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const IMAGE_FOLDER = 'post';

    protected $fillable = [
        'user_id',
        'post',
        'image'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
