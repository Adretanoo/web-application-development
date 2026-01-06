<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeFavorite extends Model
{
    protected $fillable = [
        'telegram_user_id',
        'anime_id',
        'title',
        'rating',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id');
    }
}
