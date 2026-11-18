<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Masuk' }} - BAWARA</title>
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
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FBF5DB;
            color: #222E1B;
        }
        .bg-white {
            background-color: #FFFDF8 !important;
        }
        .border-slate-100, .border-slate-200 {
            border-color: rgba(192, 182, 172, 0.45) !important;
        }
        input {
            background-color: #FFFFFF !important;
            border-color: rgba(192, 182, 172, 0.6) !important;
            color: #1E2718 !important;
        }
        input:focus {
            border-color: #76944C !important;
            box-shadow: 0 0 0 3px rgba(118, 148, 76, 0.22) !important;
        }
    </style>
</head>
<body class="bg-[#FBF5DB] text-slate-800 antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ url('/') }}" class="inline-block mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo BAWARA" class="w-24 h-24 mx-auto object-cover rounded-full shadow-lg border-2 border-[#76944C]/30">
        </a>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">BAWARA</h2>
        <p class="mt-1 text-sm text-slate-600 font-medium">Kelola Warung, Lebih Mudah &bull; Sistem Informasi Persediaan & Kasir</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:rounded-3xl sm:px-10 border border-[#C0B6AC]/40 shadow-[0_12px_36px_-6px_rgba(85,75,60,0.1)]">
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} BAWARA (Kelola Warung, Lebih Mudah). Proyek Capstone Teknik Informatika.
        </p>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
