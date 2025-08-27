<div 
    x-data="{ showPasscode: false, passcode: '', error: '' }"
    class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 p-6">
    <!-- Tombol Reset Gate -->
    <div class="absolute top-4 right-4 flex space-x-2">
        <!-- Tombol Reset Gate -->
        <a href="{{ route('reset.gate') }}" 
        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow-lg transition">
            Reset Gate
        </a>

        <!-- Tombol Dashboard -->
        <button 
            @click="showPasscode = true" 
            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow-lg transition">
            Dashboard
        </button>
    </div>

    <!-- Event Poster -->
    <div class="mb-6">

        <img src="{{ asset('images/event.png') }}" class="w-64 md:w-96 rounded-xl shadow-xl border-4 border-white" alt="Event Poster">
    </div>

    <!-- Gate Info -->
    <h1 class="text-2xl font-bold text-white mb-2">{{ $event->name }}</h1>
    <h2 class="text-3xl font-bold text-white mb-4">Gate {{ $gateNumber }}</h2>

    <!-- Scan Input -->
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
        <input 
            type="text" 
            wire:model="nik"
            wire:keydown.enter="searchRegistration" 
            placeholder="Scan atau ketik ID Card..." 
            class="w-full text-xl p-4 rounded-lg border focus:outline-none focus:ring-4 focus:ring-indigo-300"
            autofocus
        >
    </div>

    <!-- Full Screen Loading Overlay -->
    <div wire:loading wire:target="searchRegistration, execute-scan"
        class="fixed inset-0 bg-indigo-700 bg-opacity-90 flex flex-col items-center justify-center z-50 text-white p-4">
        
        <!-- Fun Animation -->
        <div class="flex flex-col items-center space-y-6">
            <!-- Emoji dengan animasi bounce -->
            <div class="text-6xl animate-bounce">🎉</div>

            <!-- Teks dengan animasi pulse -->
            <h2 class="text-3xl font-bold animate-pulse">Sedang Memproses...</h2>

            <!-- Spinner custom -->
            <svg class="animate-spin h-14 w-14 text-yellow-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>

            <!-- Text tipikal fun -->
            <p class="mt-4 text-lg italic">Tunggu sebentar ya... ✨</p>
        </div>
    </div>

    <!-- Fullscreen Passcode Pop-up -->
    <div 
        x-show="showPasscode" 
        class="fixed inset-0 bg-gradient-to-br from-purple-600 to-indigo-500 flex flex-col items-center justify-center z-50 text-white p-6"
        x-transition
    >
        <!-- Emoji Fun -->
        <div class="text-6xl mb-6 animate-bounce">🔒</div>
        
        <h2 class="text-3xl font-bold mb-4">Masukkan Passcode</h2>
        <p class="text-lg mb-6 italic">Akses dashboard hanya untuk yang punya kode rahasia! 😉</p>

        <!-- Input -->
        <input 
            type="password" 
            x-model="passcode" 
            placeholder="Ketik kode..." 
            class="text-center text-xl p-4 rounded-lg border focus:outline-none focus:ring-4 focus:ring-yellow-300 w-64 text-black"
        >

        <!-- Error Message -->
        <div x-show="error" class="text-red-300 mt-2" x-text="error"></div>

        <!-- Tombol -->
        <div class="mt-6 flex space-x-4">
            <button 
                @click="
                    if (passcode === '4dm1N') {
                        window.location.href = '{{ route('dashboard') }}';
                    } else {
                        error = 'Passcode salah! Coba lagi 🙅‍♂️';
                    }
                " 
                class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg shadow-lg text-lg transition">
                ✔ Masuk
            </button>
            <button 
                @click="showPasscode = false; passcode=''; error='';"
                class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg shadow-lg text-lg transition">
                ✖ Batal
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("show-participant", function (data) {
            const {nama, nik, ticket, bus} = data.detail[0]
            
            Swal.fire({
                title: 'Data Peserta',
                html: `
                    <p><strong>Nama:</strong> ${nama}</p>
                    <p><strong>NIK:</strong> ${nik}</p>
                    <p><strong>Jumlah Tiket:</strong> ${ticket}</p>
                    <p><strong>Ikut Bis :</strong> ${bus}</p>
                `,
                icon: 'info',
                confirmButtonText: 'Konfirmasi',
                showDenyButton: true,
                denyButtonText: 'Alihkan ke gate lain',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('execute-scan'); // Panggil method scan()
                } else {
                    Livewire.dispatch('reject-scan');
                }
            });
        });
    </script>
    @endpush
</div>
