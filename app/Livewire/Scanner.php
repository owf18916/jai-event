<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Models\Registration;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class Scanner extends Component
{
    use Swalable;

    public $nik, $event, $crew_id, $gateNumber, $registration;


    public function mount()
    {
        if (empty(session('event_id')) || empty(session('gate_number'))) {
            return redirect()->route('gate.setup');
        } else {
            $this->event = Event::find(session('event_id'));
            $this->gateNumber = session('gate_number');
            $this->crew_id =  session('crew_id');
        }
    }

    public function searchRegistration()
    {
        $this->validate(['nik' => 'required']);

        DB::transaction(function() {
            $this->registration = Registration::with('employee')->whereHas('employee', fn($q) => $q->where('employee_code', $this->nik))
                ->where('event_id', $this->event->id)
                ->lockForUpdate()
                ->first();

            if(empty($this->registration)) {
                $this->reset('nik');
                return $this->flashError('NIK belum terdaftar.');
            }

            if($this->registration->scanned_at) {
                $this->reset('nik');
                return $this->flashError('NIK sudah terdaftar di Gate '.$this->registration->gate_number);
            }

            $this->dispatch('show-participant', [
                'nama' => $this->registration->employee->name,
                'nik' => $this->registration->employee->employee_code,
                'ticket' => $this->registration->ticket_count,
                'bus' => $this->registration->is_using_bus ? 'Ya' : 'Tidak',
            ]);
        });
    }

    #[On('execute-scan')]
    public function scan()
    {
        $this->validate(['nik' => 'required']);

        DB::transaction(function() {
            $this->registration->update([
                'scanned_at' => now(),
                'scanned_by' => $this->crew_id,
                'gate_number' => $this->gateNumber
            ]);

            $this->reset(['nik','registration']);

            $this->flashSuccess('Registrasi berhasil.');
        });
    }

    #[On('reject-scan')]
    public function rejectScan()
    {
        $this->reset(['nik','registration']);
        $this->flashInfo('Registrasi batal, silahkan menuju ke gate lain.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.scanner');
    }
}


