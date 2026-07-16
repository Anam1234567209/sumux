<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>403 - Akses Ditolak</title>
</head>

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Akses Ditolak</h2>
            <p class="text-gray-600 mb-8">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="{{ route('admin.dashboard') }}"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>
