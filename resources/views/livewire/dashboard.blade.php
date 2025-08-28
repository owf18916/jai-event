<div class="min-h-screen bg-gradient-to-br from-purple-600 via-indigo-500 to-blue-500 p-6 text-gray-800">
    <div class="top-4 right-4 flex space-x-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="bg-gradient-to-r from-pink-500 to-red-500 hover:from-red-500 hover:to-pink-500 text-white font-bold px-6 py-3 rounded-full shadow-lg transform hover:scale-110 transition">
                🚪 Keluar Sesi Admin
            </button>
        </form>
    </div>

    <!-- Header -->
    <h1 class="text-4xl font-extrabold mb-4 text-center text-white flex items-center justify-center gap-2">
        📊 Dashboard Registrasi
    </h1>

    <!-- Statistik Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 text-white">
        <div class="bg-green-600 rounded-xl p-4 shadow-lg flex flex-col items-center transform hover:scale-105 transition">
            <p class="text-sm font-semibold">Total Terdaftar</p>
            <h2 class="text-3xl font-extrabold">{{ $totalEmployee }}</h2>
        </div>
        <div class="bg-blue-600 rounded-xl p-4 shadow-lg flex flex-col items-center transform hover:scale-105 transition">
            <p class="text-sm font-semibold">Total Registrasi</p>
            <h2 class="text-3xl font-extrabold">{{ $totalScan }}</h2>
        </div>
        <div class="bg-yellow-500 rounded-xl p-4 shadow-lg flex flex-col items-center transform hover:scale-105 transition">
            <p class="text-sm font-semibold">Rasio </p>
            <h2 class="text-3xl font-extrabold">{{ number_format($totalScan/$totalEmployee*100,0) }} %</h2>
        </div>
        <div class="bg-purple-600 rounded-xl p-4 shadow-lg flex flex-col items-center transform hover:scale-105 transition">
            <p class="text-sm font-semibold">Gate Aktif</p>
            <h2 class="text-3xl font-extrabold">{{ count($perGate) }}</h2>
        </div>
    </div>

    <!-- Search by NIK -->
    {{-- <div class="bg-white rounded-2xl shadow-xl p-4 mb-6">
        <div class="flex w-full gap-3">
            <input @keydown.enter="$dispatch('search-by-nik')" type="text" wire:model="nik"
                placeholder="Cari Data Registrasi by NIK"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        </div>

        @if($registration)
            <div id="searchResult"
                class="mt-4 p-4 rounded-xl shadow-md text-white text-center transform scale-0 opacity-0 transition-all duration-500">
                <h3 class="text-xl font-bold flex items-center justify-center gap-2">
                    {{ $registration->employee->name }}
                </h3>
                <p class="text-lg mt-2">
                    Status: 
                    @if($registration->scanned_at)
                        <span class="font-semibold text-green-400">✅ Sudah Registrasi</span>
                    @else
                        <span class="font-semibold text-red-400">❌ Belum Registrasi</span>
                    @endif
                </p>
            </div>
        @else
            <div id="searchResult"
                class="mt-4 p-4 rounded-xl bg-yellow-200 text-yellow-800 text-center transform scale-0 opacity-0 transition-all duration-500">
                ⚠️ Data tidak ditemukan
            </div>
        @endif
    </div> --}}

    <!-- Chart Full Width -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 transform hover:scale-102 transition">
        <h2 class="text-2xl font-bold mb-4 text-indigo-600 flex items-center gap-2">
            🔍 Registrasi Per Gate
        </h2>
        @if(count($perGate) > 0)
            <canvas id="gateChart" class="w-1/2 h-72"></canvas>
        @else
            <div class="text-center text-gray-500 text-lg py-10">
                ⚠️ Belum ada data scan
            </div>
        @endif
    </div>

    <!-- Scan Terbaru -->
    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-102 transition">
        <h2 class="text-2xl font-bold mb-4 text-indigo-600 flex items-center gap-2">
            ⏱ Data Scan Terbaru
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead class="bg-indigo-100 rounded-xl">
                    <tr>
                        <th class="p-3 text-indigo-700">NIK</th>
                        <th class="p-3 text-indigo-700">Nama</th>
                        <th class="p-3 text-indigo-700">Gate</th>
                        <th class="p-3 text-indigo-700">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentScans as $scan)
                        <tr class="bg-indigo-50 hover:bg-indigo-100 rounded-lg shadow-sm">
                            <td class="p-3 font-medium">{{ $scan->employee->employee_code }}</td>
                            <td class="p-3">{{ $scan->employee->name }}</td>
                            <td class="p-3">{{ $scan->gate_number }}</td>
                            <td class="p-3">{{ $scan->scanned_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logout -->
    {{-- <div class="mt-10 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="bg-gradient-to-r from-pink-500 to-red-500 hover:from-red-500 hover:to-pink-500 text-white font-bold px-6 py-3 rounded-full shadow-lg transform hover:scale-110 transition">
                🚪 Keluar Sesi Admin
            </button>
        </form>
    </div> --}}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                    const ctx = document.getElementById('gateChart').getContext('2d');

    // Ambil data
    const gateLabels = @json(array_keys($perGate));
    const gateData = @json(array_values($perGate));

    // Hitung min & max untuk referensi warna
    const maxValue = Math.max(...gateData);

    // Buat warna dinamis berdasarkan nilai scan
    const dynamicColors = gateData.map(value => {
        if (value > maxValue * 0.7) return 'rgba(34, 197, 94, 0.7)';  // Hijau (Tinggi)
        if (value > maxValue * 0.4) return 'rgba(234, 179, 8, 0.7)';  // Kuning (Sedang)
        return 'rgba(239, 68, 68, 0.7)';  // Merah (Rendah)
    });

    let gateChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: gateLabels,
            datasets: [{
                label: 'Jumlah Scan',
                data: gateData,
                backgroundColor: dynamicColors,
                borderRadius: 10,
                barThickness: 50
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1500,
                easing: 'easeOutElastic', // Animasi masuk smooth
                delay: (context) => context.dataIndex * 150 // Animasi bar per item
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#4f46e5',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            scales: {
                x: {
                    ticks: { color: '#4f46e5' },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#4f46e5' },
                    grid: { color: '#e5e7eb' }
                }
            }
        }
    });

    Livewire.on('refreshData', () => {
        // Update data & warna ketika Livewire refresh
        gateChart.data.labels = @json(array_keys($perGate));
        gateChart.data.datasets[0].data = @json(array_values($perGate));
        gateChart.data.datasets[0].backgroundColor = dynamicColors;
        gateChart.update();
    });
            });
        </script>
    @endpush
</div>
