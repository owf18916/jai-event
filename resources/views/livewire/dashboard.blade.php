<div class="min-h-screen relative bg-gradient-to-br from-purple-600 via-indigo-500 to-blue-500 p-6 text-gray-800">
    <!-- Tombol Keluar -->
    <div class="absolute top-4 right-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="flex items-center gap-2 bg-gradient-to-r from-pink-500 to-red-500 hover:from-red-500 hover:to-pink-500 
                    text-white font-bold rounded-full shadow-lg transform hover:scale-105 transition
                    px-3 py-2 text-sm sm:px-6 sm:py-3 sm:text-base">
                🚪 
                <span class="hidden sm:inline">Keluar Sesi Admin</span>
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

    <!-- Chart Full Width -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4 text-indigo-600 flex items-center gap-2">
            🔍 Registrasi Per Gate
        </h2>
        @if(count($perGate) > 0)
            <canvas id="gateChart" class="w-1/2 h-72"></canvas>
        @else
            <div class="text-center text-gray-500 text-lg py-10">
                ⚠️ Belum ada data registrasi
            </div>
        @endif
    </div>

    
    <!-- Scan Terbaru -->
    <div class="bg-white rounded-2xl shadow-xl p-6">
        <h2 class="text-2xl font-bold mb-4 text-indigo-600 flex items-center gap-2">
            ⏱ Data Registrasi Terbaru
        </h2>

        <!-- Search Bar -->
        <div class="mb-4">
            <input 
                type="text" 
                id="searchInput" 
                wire:model="nik"
                wire:keydown.enter="getRecentScans"
                placeholder="Cari NIK..." 
                class="w-full md:w-1/3 p-2 border rounded-xl shadow-sm focus:ring focus:ring-indigo-300 focus:outline-none"
            >
        </div>

        <!-- Loading Spinner -->
        <div wire:loading wire:target="getRecentScans" class="flex flex-col items-center my-3">
            <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-indigo-600 mb-2"></div>
            <span class="text-sm text-indigo-600 font-medium">Sedang mencari data ...</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2" id="scanTable">
                <thead class="bg-indigo-100 rounded-xl">
                    <tr>
                        <th class="p-3 text-indigo-700">NIK</th>
                        <th class="p-3 text-indigo-700">Nama</th>
                        <th class="p-3 text-indigo-700">Gate</th>
                        <th class="p-3 text-indigo-700">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($perGate) > 0)
                        @foreach($recentScans as $scan)
                        <tr class="bg-indigo-50 hover:bg-indigo-100 rounded-lg shadow-sm">
                            <td class="p-3 font-medium">{{ $scan->employee->employee_code }}</td>
                            <td class="p-3">{{ $scan->employee->name }}</td>
                            <td class="p-3">{{ $scan->gate_number }}</td>
                            <td class="p-3">{{ $scan->scanned_at }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="bg-indigo-50 hover:bg-indigo-100 rounded-lg shadow-sm">
                            <td class="p-3 font-medium text-center" colspan="4">Belum ada data registrasi.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


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
