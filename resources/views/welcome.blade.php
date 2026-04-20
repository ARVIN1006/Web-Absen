<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRD & Face Attendance System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased overflow-x-hidden selection:bg-brand-500 selection:text-white dark:bg-slate-900 dark:text-white">
    
    <!-- Background Elements -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-500/20 blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-500/20 blur-[100px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[50%] h-[50%] rounded-full bg-emerald-500/20 blur-[100px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="glass rounded-2xl px-6 py-3 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center text-white font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight">Presensi<span class="text-brand-500">AI</span></span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#features" class="hover:text-brand-500 transition-colors">Fitur</a>
                    <a href="#tech" class="hover:text-brand-500 transition-colors">Teknologi</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium hover:text-brand-500 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:text-brand-500 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium px-5 py-2.5 rounded-xl bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all hover:-translate-y-0.5">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 font-medium text-sm mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                Sistem HRD Masa Depan
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight mb-6 animate-fade-in-up" style="animation-delay: 0.2s; line-height: 1.1;">
                Manajemen Kehadiran dengan <br class="hidden lg:block"/>
                <span class="text-gradient">Pengenalan Wajah AI</span>
            </h1>
            
            <p class="max-w-2xl mx-auto text-lg lg:text-xl text-slate-600 dark:text-slate-400 mb-10 animate-fade-in-up" style="animation-delay: 0.3s;">
                Solusi komprehensif untuk HRD modern. Lacak kehadiran akurat dengan Face Recognition, kelola shift, dan pantau cuti karyawan dalam satu platform intuitif.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-xl shadow-brand-500/30 transition-all hover:-translate-y-1">
                    Mulai Sekarang
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-xl glass font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    Pelajari Fitur
                </a>
            </div>

            <!-- Dashboard Preview -->
            <div class="mt-20 relative mx-auto max-w-5xl animate-fade-in-up" style="animation-delay: 0.6s;">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-500 to-purple-600 rounded-2xl blur opacity-30"></div>
                <div class="relative glass rounded-2xl border border-slate-200/50 dark:border-slate-700/50 p-2 shadow-2xl">
                    <div class="rounded-xl overflow-hidden bg-slate-900 border border-slate-800">
                        <div class="flex items-center gap-2 px-4 py-3 bg-slate-800 border-b border-slate-700">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop" alt="Dashboard Preview" class="w-full h-auto opacity-80 mix-blend-luminosity hover:mix-blend-normal transition-all duration-700">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 px-6 relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Fitur Unggulan</h2>
                <p class="text-slate-600 dark:text-slate-400">Dirancang khusus untuk mempermudah pekerjaan HRD.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Face Recognition</h3>
                    <p class="text-slate-600 dark:text-slate-400">Absensi akurat menggunakan teknologi Face-api.js. Mencegah manipulasi lokasi dan penitipan absen.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Geofencing & Lokasi</h3>
                    <p class="text-slate-600 dark:text-slate-400">Validasi lokasi presensi secara real-time. Memastikan karyawan berada di area kantor yang ditentukan.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Manajemen Shift & Cuti</h3>
                    <p class="text-slate-600 dark:text-slate-400">Kelola shift kerja kompleks dan alur persetujuan cuti dengan sistem terpadu yang transparan.</p>
                </div>

                <!-- Feature 4 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Dashboard Analitik</h3>
                    <p class="text-slate-600 dark:text-slate-400">Visualisasi data presensi dengan Chart.js. Pantau tren kehadiran dan laporan karyawan dengan mudah.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi-Departemen</h3>
                    <p class="text-slate-600 dark:text-slate-400">Dukung struktur organisasi skala besar dengan pemisahan departemen dan lokasi yang rapi.</p>
                </div>

                <!-- Feature 6 -->
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-100 dark:bg-cyan-900/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Ekspor Laporan</h3>
                    <p class="text-slate-600 dark:text-slate-400">Unduh rekapitulasi data absensi ke format PDF dan CSV untuk kebutuhan arsip dan penggajian.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section id="tech" class="py-24 px-6 bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl border-y border-slate-200/50 dark:border-slate-700/50">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-2xl font-bold mb-10">Dibangun dengan Teknologi Modern</h2>
            <div class="flex flex-wrap justify-center items-center gap-12 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <!-- Laravel -->
                <div class="flex items-center gap-2 font-bold text-xl"><span class="text-[#FF2D20]">Laravel</span> 11</div>
                <!-- Tailwind -->
                <div class="flex items-center gap-2 font-bold text-xl"><span class="text-[#38B2AC]">Tailwind</span> CSS</div>
                <!-- Alpine -->
                <div class="flex items-center gap-2 font-bold text-xl"><span class="text-[#8BC0D0]">Alpine</span>.js</div>
                <!-- Face API -->
                <div class="flex items-center gap-2 font-bold text-xl">Face-api.js</div>
                <!-- MySQL -->
                <div class="flex items-center gap-2 font-bold text-xl"><span class="text-[#4479A1]">MySQL</span></div>
                <!-- Railway -->
                <div class="flex items-center gap-2 font-bold text-xl"><span class="text-slate-900 dark:text-white">Railway</span> Deploy</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 text-center text-slate-500 text-sm">
        <p>&copy; {{ date('Y') }} PresensiAI Showcase Portofolio. All rights reserved.</p>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('bg-white/80', 'dark:bg-slate-900/80', 'backdrop-blur-md', 'shadow-sm');
                nav.querySelector('.glass').classList.remove('glass', 'shadow-sm');
            } else {
                nav.classList.remove('bg-white/80', 'dark:bg-slate-900/80', 'backdrop-blur-md', 'shadow-sm');
                nav.querySelector('.glass').classList.add('glass', 'shadow-sm');
            }
        });
    </script>
</body>
</html>
