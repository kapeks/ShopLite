<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PostOffice extends Model
{
    use HasFactory;
    protected $fillable = ['city_id', 'name'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public static function insertIntoFts($id, $name)
    {
        DB::statement('INSERT INTO post_offices_fts (id, name) VALUES (?, ?)', [$id, $name]);
    }
}
