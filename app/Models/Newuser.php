<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Newuser extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    public $fillable = ['first_name', 'last_name', 'email', 'password'];

    public $hidden = ['password'];
}
