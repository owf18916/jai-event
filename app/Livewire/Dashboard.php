<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use App\Models\Registration;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    use Swalable;

    public $eventId;
    public $perGate = [];
    public $recentScans = [];
    public $totalScan;
    public $totalEmployee;
    public $registration;
    public $nik;

    public function mount()
    {
        $this->eventId = session('event_id') ?? \App\Models\Event::where('is_active', true)->first()->id;
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->perGate = Registration::select('gate_number', DB::raw('count(*) as total'))
            ->where('event_id', $this->eventId)
            ->whereNotNull('scanned_at')
            ->groupBy('gate_number')
            ->pluck('total', 'gate_number')
            ->toArray();

        $this->totalScan = Registration::whereNotNull('scanned_at')->where('event_id', $this->eventId)->count();
        
        $this->totalEmployee = Registration::where('event_id', $this->eventId)->count();

        $this->getRecentScans();
    }

    public function getRecentScans()
    {
        $this->recentScans = Registration::with('employee')
            ->when(!empty($this->nik), function ($q) {
                $q->whereHas('employee', fn ($q2) => $q2->where('employee_code','like',"%{$this->nik}%"));
            })
            ->where('event_id', $this->eventId)
            ->whereNotNull('scanned_at')
            ->latest('scanned_at')
            ->take(10)
            ->get();
    }

    #[On('search-by-nik')]
    public function searchByNik()
    {
        $this->registration = Registration::with('employee')->whereHas('employee', fn ($q) => $q->where('employee_code', $this->nik))->first();

        if (empty($this->registration)) {
            return $this->flashError('NIK tidak terdaftar.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}

