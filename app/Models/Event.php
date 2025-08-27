<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $fillable = ['name','date','place','passcode','is_active'];
    public function registrations(){ return $this->hasMany(Registration::class); }
}

