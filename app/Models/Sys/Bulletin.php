<?php

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $table = "bulletin";
    public $primaryKey = 'id';
    protected $fillable = [
        'send_user', 'title', 'content', 'valid_time', 'weight'
    ];
}