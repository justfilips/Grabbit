<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportedItem extends Model
{
    protected $fillable = ['item_id', 'reported_by', 'reason'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
