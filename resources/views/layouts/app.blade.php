<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{ config('app.name', 'JAI - Event') }} {{ isset($title) ? '| '.$title : '' }} </title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto p-4">
        {{ $slot }}
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on("swal-fired", (params) => {
                const { title, message, type, footer = null , redirect = null} = params[0];

                Swal.fire({
                    title: title,
                    text: message,
                    icon: type,
                    confirmButtonText: "Ok",
                    footer: footer,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed && redirect !== null) {
                        window.location.href = redirect; 
                    }
                });
            });

            document.addEventListener("confirmation-fired", function (event) {
                const { eventName, rowId = null, params = null, title = "Yakin ?", message =  "Tekan Ya jika Anda sudah yakin."} = event.detail

                Swal.fire({
                    title: title,
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(eventName, { id: rowId, parameters: params });
                    }
                });
            });

            Livewire.on("toast-fired", (params) => {
                const { title, icon } = params[0];

                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });

                Toast.fire({
                    icon: icon,
                    title: title,
                });
            });
        });
    </script>
</body>
</html>
