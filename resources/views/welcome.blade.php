<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAWARA - Kelola Warung, Lebih Mudah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                            800: '#2E4238',
                            900: '#24362D', // Deep Forest Pine from PPT
                            950: '#1A2821',
                        },
                        terracotta: {
                            50: '#FDF6F4',
                            100: '#FAECE7',
                            200: '#F4D4CB',
                            300: '#EDB7A9',
                            400: '#E09280',
                            500: '#C97A65',
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

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FBF5DB;
            color: #222E1B;
        }
        [x-cloak] { display: none !important; }
        .bg-white {
            background-color: #FFFDF8 !important;
        }
        .bg-surface-50, .bg-surface-100 {
            background-color: #F6EFCF !important;
        }
        .border-surface-200, .border-slate-200, .border-slate-100 {
            border-color: rgba(192, 182, 172, 0.45) !important;
        }
        .shadow-sm {
            box-shadow: 0 1px 3px rgba(70, 60, 45, 0.05), 0 1px 2px rgba(70, 60, 45, 0.03) !important;
        }
        .shadow-xl {
            box-shadow: 0 14px 32px -4px rgba(70, 60, 45, 0.09), 0 4px 10px -2px rgba(70, 60, 45, 0.04) !important;
        }
        .hero-pattern {
            background-color: #162015;
            background-image: radial-gradient(rgba(200, 218, 166, 0.16) 1px, transparent 1px);
            background-size: 26px 26px;
        }
    </style>
</head>
<body class="bg-[#FBF5DB] text-slate-800 antialiased selection:bg-brand-600 selection:text-white">

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-[#FBF5DB]/95 backdrop-blur-md border-b border-[#C0B6AC]/40 transition-all" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Brand Name -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo BAWARA" class="w-12 h-12 object-cover rounded-full shadow-md border-2 border-[#76944C]/30 group-hover:scale-105 transition-transform duration-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-brand-700 transition">BAWARA</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#C8DAA6] text-[#202f12] border border-[#76944C]/30">TI23B Capstone</span>
                        </div>
                        <span class="text-xs font-semibold text-slate-600 block leading-tight">Kelola Warung, Lebih Mudah</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#fitur" class="hover:text-brand-700 transition">Fitur Utama</a>
                    <a href="#matriks-peran" class="hover:text-brand-700 transition">Matriks Hak Akses</a>
                    <a href="#tentang" class="hover:text-brand-700 transition">Tim Capstone</a>
                </div>

                <!-- CTA Button -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold shadow-md shadow-brand-900/10 transition-all hover:-translate-y-0.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Buka Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold shadow-md shadow-brand-900/10 transition-all hover:-translate-y-0.5">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            <span>Masuk ke Sistem</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6" x-show="!mobileMenu"></i>
                        <i data-lucide="x" class="w-6 h-6" x-show="mobileMenu" x-cloak></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu" x-cloak class="md:hidden border-b border-surface-200 bg-white px-4 pt-3 pb-5 space-y-3">
            <a href="#fitur" @click="mobileMenu = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-700">Fitur Utama</a>
            <a href="#matriks-peran" @click="mobileMenu = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-700">Matriks Hak Akses</a>
            <a href="#tentang" @click="mobileMenu = false" class="block py-2 text-sm font-semibold text-slate-700 hover:text-brand-700">Tim Capstone</a>
            <div class="pt-2 border-t border-slate-100">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl bg-brand-700 text-white font-bold text-sm">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl bg-brand-700 text-white font-bold text-sm">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Masuk ke Sistem</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-pattern relative text-white py-20 lg:py-28 overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-sage-400/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left: Headline & Text -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-900/80 border border-brand-400/30 text-brand-200 text-xs font-semibold">
                        <i data-lucide="sparkles" class="w-4 h-4 text-brand-300"></i>
                        <span>Sistem Manajemen Operasional Warung Berbasis Web</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        Kelola Warung Lebih Mudah, <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-brand-200 to-emerald-100">Akurat, & Terintegrasi</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl font-normal leading-relaxed">
                        Satu platform terintegrasi untuk menyatukan pencatatan barang, persediaan, pembelian, penjualan tunai & utang tempo, prediksi belanja <strong class="text-brand-300 font-semibold">Moving Average</strong>, hingga prioritas kedaluwarsa <strong class="text-brand-300 font-semibold">FEFO</strong>.
                    </p>

                    <!-- CTA Buttons & Demo Quick Links -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white font-extrabold text-base shadow-xl shadow-brand-950/40 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                                <span>Lanjutkan ke Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white font-extrabold text-base shadow-xl shadow-brand-950/40 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i data-lucide="log-in" class="w-5 h-5"></i>
                                <span>Masuk ke Sistem Sekarang</span>
                            </a>
                            <a href="#fitur"
                               class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-navy-800/80 hover:bg-navy-800 text-slate-200 font-bold text-base border border-slate-700/80 transition flex items-center justify-center gap-2">
                                <i data-lucide="info" class="w-5 h-5 text-brand-300"></i>
                                <span>Pelajari Fitur</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Proof Badges -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-slate-800/80 text-left">
                        <div>
                            <div class="text-2xl font-black text-brand-200">100%</div>
                            <div class="text-xs text-slate-400">Terintegrasi Real-Time</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-brand-200">15 Jenis</div>
                            <div class="text-xs text-slate-400">Laporan PDF & Excel</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-brand-200">2 Peran</div>
                            <div class="text-xs text-slate-400">Admin & Kasir Teruji</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Dashboard Mockup Card -->
                <div class="lg:col-span-5 relative">
                    <div class="bg-gradient-to-b from-navy-800 to-navy-900 p-6 rounded-3xl border border-slate-700/80 shadow-2xl relative">
                        <div class="flex items-center justify-between border-b border-slate-700/80 pb-4 mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <span class="ml-2 text-xs font-mono text-slate-400">bawara.system.local</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">Live ERP</span>
                        </div>

                        <!-- Mini KPI Cards -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-navy-950/70 p-3.5 rounded-2xl border border-slate-700/60">
                                <span class="text-[10px] text-slate-400 font-semibold block">Status Stok Aman</span>
                                <span class="text-xl font-black text-brand-300">Terkontrol</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">&gt; Stok Minimum</span>
                            </div>
                            <div class="bg-navy-950/70 p-3.5 rounded-2xl border border-slate-700/60">
                                <span class="text-[10px] text-slate-400 font-semibold block">Deteksi Stok Habis</span>
                                <span class="text-xl font-black text-rose-300">Otomatis</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Log Durasi Buka-Tutup</span>
                            </div>
                            <div class="bg-navy-950/70 p-3.5 rounded-2xl border border-slate-700/60">
                                <span class="text-[10px] text-slate-400 font-semibold block">Buku Utang Pelanggan</span>
                                <span class="text-xl font-black text-amber-300">Jatuh Tempo</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Catat Cicilan Real-Time</span>
                            </div>
                            <div class="bg-navy-950/70 p-3.5 rounded-2xl border border-slate-700/60">
                                <span class="text-[10px] text-slate-400 font-semibold block">Prediksi Belanja</span>
                                <span class="text-xl font-black text-teal-300">Moving Avg</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Rekomendasi Cerdas</span>
                            </div>
                        </div>

                        <!-- Mini Mockup Transaction -->
                        <div class="bg-navy-950 p-4 rounded-2xl border border-slate-700/80 space-y-2 text-xs">
                            <div class="flex justify-between text-slate-400 text-[11px] font-mono border-b border-slate-800 pb-1.5">
                                <span>FAKTUR: PJ-20261105-0012</span>
                                <span class="text-brand-300 font-bold">LUNAS</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-300">
                                <span>Beras Ramos Setra (5 kg)</span>
                                <span class="font-bold text-white">Rp 72.500</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-300">
                                <span>Minyak Goreng Sawit (2 L)</span>
                                <span class="font-bold text-white">Rp 34.000</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-slate-800 text-slate-400 font-bold">
                                <span>Total Pembayaran:</span>
                                <span class="text-sm font-black text-brand-300">Rp 106.500</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FITUR UTAMA SECTION -->
    <section id="fitur" class="py-20 bg-surface-50 border-b border-surface-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-50 text-brand-800 border border-brand-200">
                    Fitur Unggulan Sistem
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Dirancang Khusus untuk Kebutuhan Operasional Warung
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Menjawab kendala utama warung: pencatatan barang habis, stok kedaluwarsa, buku utang yang tercecer, dan pembelian stok yang hanya mengira-ngira.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1: Deteksi Stok Habis Otomatis & Cepat -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-rose-100">
                        <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Deteksi Habis Otomatis & Cepat</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sistem mengetahui stok habis secara otomatis melalui akumulasi mutasi transaksi dan tombol pelaporan manual instan oleh pemilik warung saat barang di rak kosong.
                    </p>
                </div>

                <!-- Card 2: Prediksi Rencana Belanja Moving Average -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-teal-100">
                        <i data-lucide="trending-up" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Prediksi Moving Average</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mengolah data riwayat penjualan harian menjadi rekomendasi kuantitas belanja cerdas agar modal tidak tertimbun dan barang tidak pernah kehabisan.
                    </p>
                </div>

                <!-- Card 3: Manajemen Kedaluwarsa FEFO -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-amber-100">
                        <i data-lucide="calendar-clock" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Kedaluwarsa Sistem FEFO</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Metode <em>First Expired First Out</em> memprioritaskan penjualan barang dengan tanggal kedaluwarsa terdekat lengkap dengan peringatan dini (early warning).
                    </p>
                </div>

                <!-- Card 4: POS Kasir Transaksi Tunai & Tempo -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-brand-100">
                        <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Kasir POS Cepat & Akurat</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pencatatan kasir instan via pencarian nama atau kode internal. Fleksibel melayani pembayaran tunai uang pas, transfer, QRIS, maupun tempo utang konsumen.
                    </p>
                </div>

                <!-- Card 5: Pencatatan Buku Utang & Cicilan -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-indigo-100">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Buku Utang & Cicilan</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pelanggan yang berutang tercatat rapi beserta saldo sisa utang dan tanggal jatuh tempo. Kasir dapat mencatat penerimaan cicilan dengan cetak tanda terima.
                    </p>
                </div>

                <!-- Card 6: Pusat Laporan 15 Jenis (PDF & Excel) -->
                <div class="p-6 rounded-3xl bg-white border border-surface-200 hover:border-brand-400 hover:shadow-xl hover:shadow-brand-900/5 transition group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center mb-5 group-hover:scale-110 transition shrink-0 border border-slate-200">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">15 Jenis Laporan Bisnis</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ekspor laporan lengkap ke format cetak PDF dan spreadsheet Excel: penjualan, laba kotor, arus kas, pembelian, mutasi stok, hingga riwayat aktivitas sistem.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- MATRIKS HAK AKSES SECTION (SESUAI TABEL 2.3 PROPOSAL) -->
    <section id="matriks-peran" class="py-20 bg-surface-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-50 text-brand-800 border border-brand-200">
                    Otorisasi & Keamanan
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Matriks Hak Akses Berdasarkan Tabel 2.3 Proposal
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Sistem memisahkan wewenang antara Admin (pemilik/manajer) dan Kasir secara tegas di sisi antarmuka maupun validasi server-side.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Role Admin Card -->
                <div class="bg-white p-8 rounded-3xl border border-surface-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold border border-brand-100">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Peran: Admin</h3>
                            <span class="text-xs font-semibold text-brand-700">Wewenang Penuh (Owner & Manajer)</span>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Dashboard Lengkap:</strong> 10 metrik KPI, 4 grafik tren, peringatan stok, dan audit trail.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Kelola Data Barang & Kategori:</strong> Tambah, edit, nonaktifkan, dan tombol lapor habis.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Mutasi & Stok:</strong> Stok masuk, stok keluar, penyesuaian stok rusak/hilang.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Pengadaan & Pemasok:</strong> Pembelian barang dan rencana belanja Moving Average.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Kedaluwarsa FEFO:</strong> Notifikasi dan pemantauan batch kedaluwarsa.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Keuangan & Laporan:</strong> 15 jenis laporan keuangan serta audit log aktivitas.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 shrink-0 mt-0.5"></i>
                            <span><strong>Manajemen Pengguna & Pengaturan:</strong> Kelola akun pengguna dan backup SQL database.</span>
                        </li>
                    </ul>
                </div>

                <!-- Role Kasir Card -->
                <div class="bg-white p-8 rounded-3xl border border-surface-200 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold border border-teal-100">
                            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Peran: Kasir</h3>
                            <span class="text-xs font-semibold text-teal-700">Operasional Transaksi Harian</span>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                            <span><strong>Dashboard Kasir:</strong> Ringkasan transaksi harian, kas masuk, dan pintasan transaksi.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                            <span><strong>Mencatat Penjualan (POS):</strong> Layanan belanja pembeli dengan perhitungan otomatis.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                            <span><strong>Pembayaran Tunai / Utang:</strong> Pilihan pembayaran tunai atau utang dengan tanggal tempo.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                            <span><strong>Catat Pembayaran Utang:</strong> Menerima cicilan utang dan pelunasan tagihan konsumen.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                            <span><strong>Hanya Melihat Info Barang:</strong> Katalog barang <em>read-only</em> untuk cek harga dan stok rak.</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-slate-400">
                            <i data-lucide="x" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                            <span><del>Tidak dapat mutasi stok manual, pembelian barang, laporan, dan pengaturan sistem.</del></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG PROYEK CAPSTONE & TIM PENGEMBANG -->
    <section id="tentang" class="py-20 bg-white border-t border-surface-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-navy-900 to-navy-950 text-white p-8 sm:p-12 rounded-3xl shadow-xl">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-500/20 text-brand-200 border border-brand-500/30">
                        Capstone Project 2026 &bull; Teknik Informatika
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Pengembangan BAWARA: Sistem Informasi Manajemen Operasional Warung Berbasis Web
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed max-w-3xl">
                        Mata Kuliah Proyek Perangkat Lunak (PL) - Capstone Project. Dirancang dan dibangun oleh <strong>Kelompok 2 - Kelas TI23B Universitas Muhammadiyah Cirebon</strong> untuk memberikan solusi digitalisasi operasional warung yang terstruktur, akurat, dan mudah digunakan.
                    </p>

                    <!-- Student Team Grid from PPT (Slide 2 & 4) -->
                    <div class="pt-4 border-t border-slate-700/80">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-brand-300">Tim Pengembang (Kelompok 2 &bull; TI23B):</h4>
                            <span class="text-[11px] text-slate-300">Dosen Pengampu: <strong>Bapak Sugiyanto, M.Kom.</strong></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="bg-navy-800/80 p-3.5 rounded-2xl border border-slate-700 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 font-bold flex items-center justify-center text-[10px]">RS</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Product Owner</span>
                                </div>
                                <span class="font-bold text-white block text-sm">Reyna Santani</span>
                                <span class="text-[11px] text-slate-400 font-mono block">NIM: 230511038</span>
                                <p class="text-[10px] text-slate-400 leading-tight">Mengarahkan tujuan produk & kebutuhan pengguna.</p>
                            </div>

                            <div class="bg-navy-800/80 p-3.5 rounded-2xl border border-slate-700 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 font-bold flex items-center justify-center text-[10px]">NA</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">Scrum Master</span>
                                </div>
                                <span class="font-bold text-white block text-sm">Naylah Tazkiyatul Asna</span>
                                <span class="text-[11px] text-slate-400 font-mono block">NIM: 230511040</span>
                                <p class="text-[10px] text-slate-400 leading-tight">Mengoordinasikan kerja kelompok & alur kerja.</p>
                            </div>

                            <div class="bg-navy-800/80 p-3.5 rounded-2xl border border-slate-700 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-300 font-bold flex items-center justify-center text-[10px]">AR</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300">Developer</span>
                                </div>
                                <span class="font-bold text-white block text-sm">Ayu Rianti</span>
                                <span class="text-[11px] text-slate-400 font-mono block">NIM: 230511037</span>
                                <p class="text-[10px] text-slate-400 leading-tight">Menangani perancangan & pengembangan teknis.</p>
                            </div>

                            <div class="bg-navy-800/80 p-3.5 rounded-2xl border border-slate-700 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-300 font-bold flex items-center justify-center text-[10px]">KW</span>
                                    <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300">Developer</span>
                                </div>
                                <span class="font-bold text-white block text-sm">Klawdia Widareka Julian</span>
                                <span class="text-[11px] text-slate-400 font-mono block">NIM: 230511051</span>
                                <p class="text-[10px] text-slate-400 leading-tight">Menangani perancangan & pengembangan sistem.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('login') }}"
                           class="py-3 px-6 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm text-center shadow-lg transition">
                            Masuk ke Aplikasi
                        </a>
                        <span class="py-3 px-4 text-xs text-slate-400 flex items-center">
                            Universitas Muhammadiyah Cirebon &bull; Kurikulum 2026
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-navy-950 text-slate-400 text-xs py-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo BAWARA" class="w-9 h-9 object-cover rounded-full border border-brand-500/20">
                <div>
                    <span class="text-white font-black text-sm block">BAWARA</span>
                    <span class="text-[11px] text-slate-400">Kelola Warung, Lebih Mudah &bull; Capstone TI23B</span>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <p>&copy; {{ date('Y') }} BAWARA. Universitas Muhammadiyah Cirebon.</p>
                <p class="text-[10px] text-slate-400 mt-0.5">MariaDB 10.4 &bull; PHP 8.2 &bull; Laravel Framework &bull; Tailwind CSS &bull; Chart.js</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
