<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Login • SUMUX</title>

</head>

<body class="bg-[#EDF1F6] min-h-screen">

    <div class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT -->

        <div class="hidden lg:flex bg-[#071638] text-white p-20 flex-col justify-between">

            <div>

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-x flex items-center justify-center">

                        <img src="{{ asset('assets/logo-sumux.png') }}" alt="Logo" class="w-13">

                    </div>

                    <div>

                        <h1 class="text-4xl font-bold">

                            SUMUX

                        </h1>

                        <p class="text-slate-300">

                            Property & Interior

                        </p>

                    </div>

                </div>

            </div>


            <div>

                <h2 class="text-5xl font-bold leading-tight">

                    Kelola Pesanan
                    dan Operasional
                    lebih mudah

                </h2>

                <p class="mt-6 text-slate-300">

                    Dashboard modern untuk mengelola
                    customer, transaksi, ongkir,
                    dan laporan.

                </p>

            </div>


            <div class="text-slate-400">

                © 2026 SUMUX

            </div>

        </div>


        <!-- RIGHT -->

        <div class="flex items-center justify-center p-10">

            <div class="bg-white rounded-[40px] p-10 shadow-sm w-full max-w-xl">

                <div class="mb-10">

                    <h2 class="text-4xl font-bold text-[#071638]">

                        Masuk

                    </h2>

                    <p class="text-slate-500 mt-2">

                        Masuk ke dashboard SUMUX

                    </p>

                </div>


                <form method="POST" action="{{ route('login.submit') }}">

                    @csrf

                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-5">
                        <label class="text-sm text-slate-600">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full mt-2 border rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="admin@sumux.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="text-sm text-slate-600">Password</label>
                        <input type="password" name="password"
                            class="w-full mt-2 border rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="••••••••" required>
                    </div>

                    <div class="flex justify-between items-center mb-8">
                        <label class="flex gap-2 items-center">
                            <input type="checkbox" name="remember"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-600">Ingat saya</span>
                        </label>

                        <a href="#" class="text-blue-600">

                            Lupa Password?

                        </a>

                    </div>


                    <button class="w-full py-4 rounded-2xl bg-[#071638] text-white hover:opacity-95">
                        Masuk
                    </button>

                    <div class="mt-8 text-center text-slate-500">
                        SUMUX Property & Interior
                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>
