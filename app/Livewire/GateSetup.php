<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use App\Models\Employee;
use App\Traits\Swalable;
use Illuminate\Support\Facades\DB;

class GateSetup extends Component
{
    use Swalable;

    public $employee_code;
    public $gate_number;
    public $event;

    public function mount()
    {
        if (session()->has('event_id') || session()->has('gate_number')) {
            return redirect()->route('scanner');
        }

        $this->event = Event::where('is_active', true)->first();
    }

    public function submit()
    {
        $this->employee_code = ltrim($this->employee_code, '0');

        $this->validate([
            'employee_code' => 'required||digits_between:1,6|exists:employees,employee_code',
            'gate_number'   => ['required','integer','min:1'],
        ]);

        session([
            'crew_id' => Employee::select('id')->where('employee_code',$this->employee_code)->first()->pluck('id'),
            'gate_number' => $this->gate_number,
            'event_id' => $this->event->id,
        ]);

        sleep(3);

        return redirect()->route('scanner');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.gate-setup', [
            'event' => $this->event
        ]);
    }
}

