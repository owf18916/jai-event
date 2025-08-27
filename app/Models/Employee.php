<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
    protected $fillable = ['employee_code','name'];
    public function registrations(){ return $this->hasMany(Registration::class); }
}

