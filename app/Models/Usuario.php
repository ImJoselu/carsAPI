<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use HasFactory, SoftDeletes;

    public function coche(){
        return $this->hasMany(Coche::class);
    }

    public function blog(){
        return $this->hasMany(Blog::class);
    }

}
