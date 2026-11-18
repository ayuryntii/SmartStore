<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BAWARA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        cream: {
                            DEFAULT: '#FBF5DB',
                            50: '#FFFEFA',
                            100: '#FDFBF2',
                            200: '#FBF5DB',
                            300: '#F6EFCF',
                            400: '#EFE7CB',
                        },
                        brand: {
                            50: '#F4F7F0',
                            100: '#E6EFE0',
                            200: '#C8DAA6', // Palette Soft Sage #C8DAA6
                            300: '#ADC683',
                            400: '#8FB060',
                            500: '#76944C', // Palette Forest Olive #76944C
                            600: '#647F40',
                            700: '#526934',
                            800: '#3F5227',
                            900: '#2D3B1B',
                            950: '#1A230F',
                        },
                        sage: {
                            50: '#F7F9F3',
                            100: '#ECF2EB',
                            200: '#DCE8DD',
                            300: '#C8DAA6',
                            400: '#9FB987',
                            500: '#76944C',
                            600: '#5E793B',
                            700: '#485E2C',
                        },
                        amber: {
                            50: '#FFFDF0',
                            100: '#FFF9D6',
                            200: '#FFF1A8',
                            300: '#FFE670',
                            DEFAULT: '#FFD21F',
                            400: '#FFD21F', // Palette Warm Yellow #FFD21F
                            500: '#E5B21E',
                            600: '#C29410',
                            700: '#946E00',
                            800: '#6B4F00',
                        },
                        stone: {
                            50: '#FAF8F5',
                            100: '#F2EFEA',
                            200: '#E6E1D9',
                            300: '#D7D1C7',
                            warm: '#C0B6AC', // Palette Warm Stone #C0B6AC
                            400: '#C0B6AC',
                            500: '#A4988C',
                            600: '#7E7367',
                            700: '#5E544A',
                        },
                        navy: {
                            800: '#2E3E34',
                            900: '#293830', // Softer Deep Forest Moss
                            950: '#1D2922',
                        },
                        terracotta: {
                            50: '#FDF6F4',
                            100: '#FAECE7',
                            200: '#F4D4CB',
                            300: '#EDB7A9',
                            400: '#E09280',
                            500: '#C97A65', // Soft Terracotta from PPT
                            600: '#B66551',
                            700: '#9A4F3D',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FBF5DB;
            color: #222E1B;
        }
        [x-cloak] { display: none !important; }
        
        /* Soft Aesthetic Surface Hierarchy */
        .bg-white {
            background-color: #FFFDF8 !important;
        }
        .bg-slate-50, .bg-surface-50, .bg-surface-100 {
            background-color: #F6EFCF !important;
        }
        .bg-slate-100 {
            background-color: #EFE7CB !important;
        }
        .border-slate-200, .border-slate-200\/80, .border-surface-200, .border-slate-100 {
            border-color: rgba(192, 182, 172, 0.45) !important;
        }
        .border-slate-300 {
            border-color: rgba(192, 182, 172, 0.7) !important;
        }
        input, select, textarea {
            background-color: #FFFFFF !important;
            border-color: rgba(192, 182, 172, 0.6) !important;
            color: #1E2718 !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #76944C !important;
            box-shadow: 0 0 0 3px rgba(118, 148, 76, 0.22) !important;
        }
        
        /* Soft Diffused Aesthetic Shadows */
        .shadow-xs, .shadow-sm {
            box-shadow: 0 1px 3px rgba(70, 60, 45, 0.05), 0 1px 2px rgba(70, 60, 45, 0.03) !important;
        }
        .shadow-md {
            box-shadow: 0 4px 14px -2px rgba(70, 60, 45, 0.07), 0 2px 6px -1px rgba(70, 60, 45, 0.04) !important;
        }
        .shadow-lg, .shadow-xl {
            box-shadow: 0 12px 28px -4px rgba(70, 60, 45, 0.09), 0 4px 10px -2px rgba(70, 60, 45, 0.04) !important;
        }

        /* Buttons: Softer, aesthetic olive primary buttons */
        .btn-primary, button[type="submit"]:not(.btn-danger):not(.btn-secondary):not(.btn-neutral) {
            background-color: #76944C !important;
            border-color: #76944C !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px -2px rgba(118, 148, 76, 0.3) !important;
            transition: all 0.2s ease !important;
        }
        .btn-primary:hover, button[type="submit"]:not(.btn-danger):not(.btn-secondary):not(.btn-neutral):hover {
            background-color: #647F40 !important;
            border-color: #647F40 !important;
            transform: translateY(-1px) !important;
        }

        /* Secondary & Soft Accent Buttons */
        .bg-brand-600, .bg-brand-700, .bg-emerald-600, .bg-emerald-700, .bg-teal-600 {
            background-color: #76944C !important;
        }
        .bg-brand-600:hover, .bg-brand-700:hover, .bg-emerald-600:hover, .bg-emerald-700:hover {
            background-color: #647F40 !important;
        }
        .text-brand-600, .text-brand-700, .text-emerald-600, .text-emerald-700 {
            color: #485E2C !important;
        }

        /* Danger / Out of Stock / Delete: Soft Terracotta instead of harsh neon red */
        .bg-rose-500, .bg-rose-600, .bg-red-500, .bg-red-600 {
            background-color: #C97A65 !important;
        }
        .bg-rose-500:hover, .bg-rose-600:hover, .bg-red-500:hover, .bg-red-600:hover {
            background-color: #B66551 !important;
        }
        .bg-rose-50, .bg-rose-100, .bg-red-50, .bg-red-100 {
            background-color: #FDF3F0 !important;
            color: #9A4F3D !important;
            border-color: #F4D4CB !important;
        }
        .text-rose-600, .text-rose-700, .text-red-600, .text-red-700 {
            color: #B66551 !important;
        }

        /* Blue / Indigo / Purple: Soft Sage-Slate or Muted Eucalyptus */
        .bg-blue-600, .bg-indigo-600, .bg-purple-600 {
            background-color: #557D68 !important;
        }
        .bg-blue-600:hover, .bg-indigo-600:hover, .bg-purple-600:hover {
            background-color: #466A57 !important;
        }
        .bg-blue-50, .bg-indigo-50, .bg-purple-50 {
            background-color: #EAF1EA !important;
            color: #244A3B !important;
            border-color: #DCE8DD !important;
        }
        .text-blue-600, .text-indigo-600, .text-purple-600,
        .text-blue-700, .text-indigo-700, .text-purple-700 {
            color: #385E4D !important;
        }
        .text-blue-900, .text-indigo-900, .text-purple-900 {
            color: #24362D !important;
        }

        /* Emerald / Green / Teal Text Tone */
        .text-emerald-900, .text-green-900, .text-teal-900 {
            color: #24362D !important;
        }
        .text-emerald-600, .text-emerald-700, .text-green-600, .text-green-700 {
            color: #485E2C !important;
        }

        /* Amber / Warning / Utang: Warm Golden Honey */
        .bg-amber-500, .bg-amber-400, .bg-yellow-500 {
            background-color: #E5B21E !important;
            color: #382700 !important;
        }
        .bg-amber-50, .bg-amber-100, .bg-yellow-50 {
            background-color: #FFF9E6 !important;
            color: #7A5B00 !important;
            border-color: #FFE670 !important;
        }
        .text-amber-600, .text-amber-700, .text-amber-800 {
            color: #8C6800 !important;
        }
        .text-amber-900, .text-yellow-900 {
            color: #4A3800 !important;
        }

        /* Rose / Red Text Tone */
        .text-rose-900, .text-red-900 {
            color: #7D2F20 !important;
        }

        /* Table header & alternating rows: Soft Warm Ivory / Oat */
        thead th {
            background-color: #EFECE2 !important;
            color: #2F3E34 !important;
            font-weight: 700 !important;
            border-bottom: 1px solid rgba(192, 182, 172, 0.35) !important;
        }
        tbody tr:hover {
            background-color: #F8F5EA !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #F6EFCF; }
        ::-webkit-scrollbar-thumb { background: #C0B6AC; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #76944C; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FBF5DB] text-slate-800 antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Backdrop for mobile sidebar -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- SIDEBAR -->
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'lg:w-64': !sidebarCollapsed, 'lg:w-20': sidebarCollapsed }"
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-navy-900 text-white w-64 transition-all duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl">
            
            <!-- Logo Section -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/80 bg-navy-900/90">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo BAWARA" class="w-10 h-10 object-cover rounded-full shadow-sm shrink-0 border border-brand-500/30">
                    <div x-show="!sidebarCollapsed" class="transition-opacity">
                        <span class="text-lg font-black tracking-tight text-white block leading-tight">BAWARA</span>
                        <span class="text-[10px] text-emerald-400 font-semibold tracking-wide uppercase block">Kelola Warung</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm font-medium">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-brand-700 text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed">Dashboard</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <!-- ADMIN: Persediaan & Barang -->
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Persediaan & Barang
                    </div>

                    <a href="{{ route('barang.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('barang.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Data Barang</span>
                    </a>

                    <a href="{{ route('kategori.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('kategori.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="tag" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Kategori Barang</span>
                    </a>

                    <a href="{{ route('satuan.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('satuan.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="scale" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Satuan</span>
                    </a>

                    <a href="{{ route('stok.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('stok.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="boxes" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Manajemen Stok</span>
                    </a>
                @else
                    <!-- KASIR: Informasi Barang (Katalog Read-Only Sesuai Tabel 2.3) -->
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Katalog
                    </div>
                    <a href="{{ route('barang.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('barang.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="package-search" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Informasi Barang</span>
                    </a>
                @endif

                <!-- Penjualan & Kasir (Admin & Kasir) -->
                <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Penjualan & Kasir
                </div>

                <a href="{{ route('penjualan.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('penjualan.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed">Penjualan (Kasir)</span>
                </a>

                <a href="{{ route('pelanggan.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('pelanggan.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed">Pelanggan</span>
                </a>

                <a href="{{ route('piutang.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('piutang.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="credit-card" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed">Pencatatan Utang</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <!-- ADMIN ONLY: Pengadaan & Pemasok -->
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Pengadaan & Pemasok
                    </div>

                    <a href="{{ route('pembelian.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('pembelian.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="truck" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Pembelian</span>
                    </a>

                    <a href="{{ route('pemasok.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('pemasok.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="building-2" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Pemasok</span>
                    </a>

                    <a href="{{ route('rencana-belanja.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('rencana-belanja.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Rencana Belanja</span>
                    </a>

                    <a href="{{ route('analisis.prediksi') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('analisis.prediksi') ? 'bg-brand-700 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="sparkles" class="w-5 h-5 shrink-0 text-[#FFD21F]"></i>
                            <span x-show="!sidebarCollapsed">Prediksi Kebutuhan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-[#C8DAA6] text-[#243317]">Moving Avg</span>
                    </a>

                    <!-- ADMIN ONLY: Analisis & Laporan -->
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Analisis & Laporan
                    </div>

                    <div x-data="{ open: {{ request()->routeIs('analisis.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition duration-150 text-slate-300 hover:bg-slate-800/80 hover:text-white">
                            <div class="flex items-center gap-3">
                                <i data-lucide="bar-chart-2" class="w-5 h-5 shrink-0"></i>
                                <span x-show="!sidebarCollapsed">Analisis Bisnis</span>
                            </div>
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open && !sidebarCollapsed" x-cloak class="pl-10 pr-2 py-1 space-y-1">
                            <a href="{{ route('analisis.sering-habis') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs {{ request()->routeIs('analisis.sering-habis') ? 'text-emerald-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                Sering Habis
                            </a>
                            <a href="{{ route('analisis.terlaris') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs {{ request()->routeIs('analisis.terlaris') ? 'text-emerald-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                Terlaris & Kurang Laku
                            </a>
                            <a href="{{ route('analisis.prediksi') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs {{ request()->routeIs('analisis.prediksi') ? 'text-emerald-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                Prediksi (Moving Avg)
                            </a>
                            <a href="{{ route('analisis.kedaluwarsa') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs {{ request()->routeIs('analisis.kedaluwarsa') ? 'text-emerald-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
                                Kedaluwarsa (FEFO)
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('laporan.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('laporan.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Pusat Laporan (15 Jenis)</span>
                    </a>

                    <!-- ADMIN ONLY: Pengaturan & Sistem -->
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Sistem & Admin
                    </div>

                    <a href="{{ route('pengguna.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('pengguna.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="user-cog" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Manajemen Pengguna</span>
                    </a>

                    <a href="{{ route('aktivitas.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('aktivitas.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="history" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Audit Log Aktivitas</span>
                    </a>

                    <a href="{{ route('pengaturan.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('pengaturan.*') ? 'bg-brand-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i data-lucide="settings" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed">Pengaturan & Backup</span>
                    </a>
                @endif
            </div>

            <!-- User Info in Sidebar Bottom -->
            <div class="p-3 border-t border-slate-800/80 bg-navy-900/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center font-bold text-white shrink-0">
                            {{ substr(auth()->user()->nama, 0, 1) }}
                        </div>
                        <div x-show="!sidebarCollapsed" class="truncate text-xs">
                            <span class="font-bold text-slate-100 block truncate">{{ auth()->user()->nama }}</span>
                            <span class="text-[10px] capitalize px-1.5 py-0.5 rounded font-semibold inline-block
                                {{ auth()->user()->isAdmin() ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : '' }}
                                {{ auth()->user()->isKasir() ? 'bg-blue-950 text-blue-400 border border-blue-800' : '' }}
                                {{ auth()->user()->isGudang() ? 'bg-amber-950 text-amber-400 border border-amber-800' : '' }}">
                                {{ auth()->user()->peran }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" x-show="!sidebarCollapsed">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- TOPBAR -->
            <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex p-2 rounded-xl text-slate-500 hover:bg-slate-100">
                        <i data-lucide="chevrons-left" class="w-5 h-5 transition-transform" :class="{ 'rotate-180': sidebarCollapsed }"></i>
                    </button>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">{{ $title ?? 'BAWARA' }}</h1>
                        @if(isset($breadcrumb))
                            <nav class="flex text-xs text-slate-400 gap-1.5 items-center">
                                <span>Home</span>
                                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                <span class="text-slate-600 font-medium">{{ $breadcrumb }}</span>
                            </nav>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Quick action: Report Out of Stock shortcut (Admin Only) -->
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('stok.index', ['status_stok' => 'habis']) }}"
                            title="Lihat Barang Habis"
                            class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                            <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-600"></i>
                            <span>Barang Habis</span>
                        </a>
                    @endif

                    <!-- User Account Dropdown -->
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition">
                            <div class="w-8 h-8 rounded-full bg-brand-700 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                {{ substr(auth()->user()->nama, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-slate-700 hidden md:inline-block">{{ auth()->user()->nama }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>
                        <div x-show="userMenu" x-cloak @click.away="userMenu = false"
                            class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 text-xs">
                                <span class="font-bold text-slate-800 block truncate">{{ auth()->user()->nama }}</span>
                                <span class="text-slate-400 block truncate">{{ auth()->user()->email }}</span>
                                <span class="inline-block mt-1 font-semibold text-[10px] text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full capitalize">
                                    Peran: {{ auth()->user()->peran }}
                                </span>
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400"></i>
                                <span>Dashboard</span>
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">
                                    <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                                    <span>Pengaturan Sistem</span>
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-semibold">
                                    <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <!-- Session Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start justify-between gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold block">Berhasil!</span>
                                <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start justify-between gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold block">Kesalahan!</span>
                                <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-start justify-between gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold block">Informasi</span>
                                <span class="text-xs sm:text-sm font-medium">{{ session('info') }}</span>
                            </div>
                        </div>
                        <button @click="show = false" class="text-blue-500 hover:text-blue-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- GLOBAL REPORT OUT OF STOCK MODAL -->
    <div id="modalLaporHabis" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center gap-3 text-rose-600 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Laporkan Barang Habis Fisik</h3>
                    <p class="text-xs text-slate-500">Pencatatan cepat saat fisik barang di rak kosong</p>
                </div>
            </div>

            <form id="formLaporHabis" method="POST" action="">
                @csrf
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 mb-4 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Barang:</span>
                        <span class="font-bold text-slate-800" id="laporNamaBarang">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Stok Sistem Saat Ini:</span>
                        <span class="font-bold text-slate-800" id="laporStokSaatIni">-</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="laporAlasan" class="block text-xs font-bold text-slate-700 mb-1.5">Alasan / Catatan Kondisi Fisik</label>
                    <textarea id="laporAlasan" name="alasan" rows="3" required
                        placeholder="Contoh: Stok di etalase sudah kosong secara fisik / rusak / tercecer"
                        class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-600"></textarea>
                    <p class="mt-1 text-[11px] text-slate-400">
                        *Sistem akan mengosongkan stok sistem, mencatat kejadian habis di riwayat, dan merekomendasikan pembelian pada rencana belanja.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModalLaporHabis()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md transition">
                        Konfirmasi Habis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openModalLaporHabis(barangId, namaBarang, stokSaatIni) {
            const modal = document.getElementById('modalLaporHabis');
            const form = document.getElementById('formLaporHabis');
            document.getElementById('laporNamaBarang').textContent = namaBarang;
            document.getElementById('laporStokSaatIni').textContent = stokSaatIni;
            form.action = "{{ url('/barang') }}/" + barangId + "/lapor-habis";
            modal.classList.remove('hidden');
        }

        function closeModalLaporHabis() {
            document.getElementById('modalLaporHabis').classList.add('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
