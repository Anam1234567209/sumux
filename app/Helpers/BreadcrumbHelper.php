<?php

namespace App\Helpers;

// use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class BreadcrumbHelper
{
    /**
     * Menghasilkan breadcrumb dari route saat ini
     * 
     * @return array
     */
    public static function getBreadcrumbs(): array
    {
        $routeName = Route::currentRouteName();

        if (!$routeName) {
            return [];
        }

        $breadcrumbs = [];
        $parts = explode('.', $routeName);

        // Bangun path untuk setiap segment
        $currentPath = '';
        $totalParts = count($parts);
        foreach ($parts as $index => $part) {
            $currentPath .= ($currentPath ? '.' : '') . $part;

            $label = self::formatLabel($part);
            $breadcrumbs[] = [
                'label' => $label,
                'route' => $currentPath,
                'isLast' => $index === $totalParts - 1,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Format label dari segment route
     * 
     * @param string $segment
     * @return string
     */
    private static function formatLabel(string $segment): string
    {
        $labels = [
            'admin' => 'Admin',
            'dashboard' => 'Dashboard',
            'monitoring' => 'Customer',
            'transactions' => 'Transaksi',
            'shipping' => 'Cek Ongkir',
            'reports' => 'Laporan',
            'settings' => 'Pengaturan',
            'interior-projects' => 'Interior Projects',
            'edit' => 'Edit',
            'create' => 'Tambah',
            'show' => 'Detail',
        ];

        return $labels[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
    }

    /**
     * Cek apakah route dapat diklik
     * 
     * @param string $routeName
     * @return bool
     */
    private static function canClickRoute(string $routeName): bool
    {
        try {
            Route::has($routeName);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
