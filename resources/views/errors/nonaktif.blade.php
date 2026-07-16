@extends('layouts.admin')

@section('title', 'Akun Nonaktif')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="text-center max-w-md">
            <div class="mb-6">
                <i class="fas fa-lock text-6xl text-red-500"></i>
            </div>
            <h1 class="text-3xl font-bold text-red-600 mb-2">Akun Nonaktif</h1>
            <p class="text-gray-600 mb-6">
                Akun Anda telah dinonaktifkan oleh administrator.
                <br>Hubungi support untuk informasi lebih lanjut.
            </p>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="inline-block px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
@endsection
