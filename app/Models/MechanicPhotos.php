<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicPhotos extends Model
{
    use HasFactory;

    protected $table = 'mechanicphotos';
    public $timestamps = false;
}
