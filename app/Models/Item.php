<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'image_path',
        'status', //'pending', 'approved', 'sold'
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function reports()
    {
        return $this->hasMany(ReportedItem::class);
    }

    public function reportsMade()
    {
        return $this->hasMany(ReportedItem::class, 'reported_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'item_id');
    }

    public function wishedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }


}
