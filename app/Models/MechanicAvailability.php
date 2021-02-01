<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicAvailability extends Model
{
    use HasFactory;

    protected $table = 'mechanicavailability';
    public $timestamps = false;
}
