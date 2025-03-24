<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function post_offices()
    {
        return $this->hasMany(PostOffice::class);
    }
}
