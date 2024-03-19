<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newuser extends Model
{
    use HasFactory;

    public $fillable = ['first_name', 'last_name', 'email', 'password'];

    public $hidden = ['password'];
}
