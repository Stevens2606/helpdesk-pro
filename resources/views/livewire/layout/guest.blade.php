<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-indigo-100">
        <div class="text-center">
            <a href="/" wire:navigate class="inline-flex flex-col items-center">
                <x-application-logo class="w-16 h-16 fill-current text-indigo-600" />
                <span class="mt-2 text-2xl font-bold text-gray-800 tracking-tight">HelpDesk <span class="text-indigo-600">Pro</span></span>
            </a>
            <p class="mt-1 text-sm text-gray-500">Sistem tiket dukungan pelanggan</p>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-gray-400">
            Demo: admin@example.com / staff@example.com / customer@example.com, password: <code>password</code>
        </p>
    </div>
</body>