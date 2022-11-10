<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Build extends Model
{
    use HasFactory, Uuids;

    protected $casts = [
        'data' => 'array',
        'tags' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
