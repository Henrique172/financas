<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outgoing extends Model
{
     protected $fillable = ['id'];

    public function Categoryes()
    {
        return $this->belongsTo(Categoryes::class, 'category');
    }
}
