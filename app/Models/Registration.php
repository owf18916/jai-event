<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model {
    protected $fillable = ['employee_id','event_id','gate_number','ticket_count','is_using_bus','scanned_at'];
    protected $dates = ['scanned_at'];
    public function employee(){ return $this->belongsTo(Employee::class); }
    public function event(){ return $this->belongsTo(Event::class); }
}
