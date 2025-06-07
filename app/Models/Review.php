<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'reviewer_id',      // who wrote the review (user_id)
        'reviewee_id',      // who is being reviewed (seller or user)
        'item_id',
        'rating',
        'comment',
    ];
}
