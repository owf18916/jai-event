<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-400 via-orange-500 to-yellow-400 p-6">
    <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-lg p-6 items-center md:items-start max-w-5xl w-full">
        <!-- Left: Form -->
        <div class="w-full md:w-1/2 pr-0 md:pr-6">
            <h1 class="text-2xl font-bold mb-4">Gate Setting</h1>
            <form wire:submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Event</label>
                    <input type="text" value="{{ $event->name ?? '' }}" disabled class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Lokasi</label>
                    <input type="text" value="{{ $event->place ?? '' }}" disabled class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Acara</label>
                    <input type="text" value="{{ \Carbon\Carbon::parse($event->date)->format('d F Y') ?? '' }}" disabled class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gate Number</label>
                    <input type="number" wire:model="gate_number" class="w-full border rounded p-2">
                    @error('gate_number') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">NIK Panitia</label>
                    <input type="text" wire:model="employee_code" class="w-full border rounded p-2">
                    @error('employee_code') <span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full flex justify-center items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="submit">

                    <!-- Spinner -->
                    <svg wire:loading wire:target="submit"
                        class="animate-spin h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>

                    <!-- Text -->
                    <span>
                        <span wire:loading.remove wire:target="submit">Mulai Scan Registrasi</span>
                        <span wire:loading wire:target="submit">Loading...</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- Right: Image -->
        <div class="w-full md:w-1/2 flex justify-center">
            <img src="/images/event.png" alt="Event Poster" class="max-w-full h-auto rounded-lg object-contain">
        </div>

        <!-- Full Screen Loading Overlay -->
        <div wire:loading wire:target="submit"
            class="fixed inset-0 bg-gradient-to-br from-purple-600 via-indigo-600 to-pink-500 animate-gradient-x bg-size-200 flex flex-col items-center justify-center z-50 text-white p-4">
            
            <!-- Fun Animation -->
            <div class="flex flex-col items-center space-y-6">
                <!-- Emoji / Icon -->
                <div class="text-6xl animate-bounce">🚪</div>

                <!-- Teks animasi -->
                <h2 class="text-3xl font-bold animate-pulse text-center">
                    Menyiapkan Gate... <br/> Tunggu Sebentar!
                </h2>

                <!-- Spinner -->
                <svg class="animate-spin h-14 w-14 text-yellow-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>

                <!-- Text kecil -->
                <p class="mt-4 text-lg italic">Sebentar lagi kamu diarahkan ke halaman registrasi 🎟️</p>
            </div>
        </div>
    </div>
</div>
