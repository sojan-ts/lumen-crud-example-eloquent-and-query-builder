<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'email', 'password'];

    // this ensures that this field is hidden from the response when getting data from database
    protected $hidden = ['password']; 
}
