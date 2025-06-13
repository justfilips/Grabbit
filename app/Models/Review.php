<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'reviewer_id',      // who wrote the review (user_id)
        'reviewed_id',      // who is being reviewed (seller or user)
        'item_id',
        'rating',
        'comment',
    ];
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewed()
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
