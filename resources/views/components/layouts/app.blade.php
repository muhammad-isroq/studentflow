<!doctype html>
<html data-theme="cupcake">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
  </head>
  <body class="pt-7 scroll-smooth">

    <div class="navbar bg-base-100 shadow-sm fixed top-0 left-0 w-full z-50" data-theme="valentine">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                    <li><a href="/" wire:navigate>Beranda</a></li>
                    <li><a href="/artikel" wire:navigate>Berita</a></li>
                    
                    <li>
                        <details>
                            <summary>Program</summary>
                            <ul class="p-2">
                                <li><a class="whitespace-nowrap" href="/master-preschool" wire:navigate>Master PRE-school</a></li>
                                <li><a class="whitespace-nowrap" href="/master-kids" wire:navigate>Master Kids</a></li>
                                <li><a class="whitespace-nowrap" href="/master-conversation" wire:navigate>Master Conversation</a></li>
                                <li><a class="whitespace-nowrap" href="/master-privat" wire:navigate>Master Private</a></li>
                                <li><a class="whitespace-nowrap" href="/master-toefl-preparation" wire:navigate>Master Toefl Preparation</a></li>
                                <li><a class="whitespace-nowrap" href="/master-onsite-training" wire:navigate>Master On-site Training</a></li>
                            </ul>
                        </details>
                    </li>
                    <li><a href="/testimoni" wire:navigate>Testimoni</a></li>
                    <li><a href="/tentang-kami" wire:navigate>Tentang Kami</a></li>
                    <li><a href="/instruktur" wire:navigate>Instruktur</a></li>
                    <li><a href="/staff" wire:navigate>Staff</a></li>
                    <li><a href="/kontak" wire:navigate>Kontak</a></li>
                </ul>
            </div>
            <a href="/" class="btn btn-ghost text-xl">The Master of Dumai</a>
        </div>
        <div class="navbar-center hidden lg:flex ml-[-40px]">
        <ul class="menu menu-horizontal px-1">
            <li><a href="/" wire:navigate>Beranda</a></li>
            <li><a href="/artikel" wire:navigate>Berita</a></li>
            <li>
                <details>
                    <summary>Program</summary>
                    <ul class="p-2 z-[1]">  
                        <li><a class="whitespace-nowrap" href="/master-preschool" wire:navigate>Master PRE-school</a></li>
                        <li><a class="whitespace-nowrap" href="/master-kids" wire:navigate>Master Kids</a></li>
                        <li><a class="whitespace-nowrap" href="/master-conversation" wire:navigate>Master Conversation</a></li>
                        <li><a class="whitespace-nowrap" href="/master-privat" wire:navigate>Master Private</a></li>
                        <li><a class="whitespace-nowrap" href="/master-toefl-preparation" wire:navigate>Master Toefl Preparation</a></li>
                        <li><a class="whitespace-nowrap" href="/master-onsite-training" wire:navigate>Master On-site Training</a></li>
                    </ul>
                </details>
            </li>
            <li><a href="/testimoni" wire:navigate>Testimoni</a></li>
            <li><a href="/tentang-kami" wire:navigate>Tentang Kami</a></li>
            <li><a href="/instruktur" wire:navigate>Instruktur</a></li>
            <li><a href="/staff" wire:navigate>Staff</a></li>
            <li><a href="/kontak" wire:navigate>Kontak</a></li>
        </ul>
            </div>
            {{-- <div class="navbar-end">
                <a class="btn">Button</a>
            </div> --}}
    </div>

    <main>
            {{ $slot }}
     </main>

    <footer class="bg-fuchsia-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <div class="space-y-4">
                <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full bg-white p-1" src="{{ asset('images/logo.png') }}" alt="The Master of Dumai Logo">
                    <span class="ml-3 text-white text-2xl font-bold">The Master of Dumai</span>
                </div>
                <p class="text-white text-sm leading-relaxed indent-8">
                    Sebagai salah satu Pusat Bahasa Inggris di Dumai, tujuan kami adalah
berkontribusi dalam membantu sumber daya manusia di Dumai agar mampu
bersaing di bidang akademik, sosial, dan profesional melalui
kemampuan berbahasa Inggris. Kami berfokus pada praktik, teori, dan menjadikan
Bahasa Inggris semenyenangkan mungkin.
                </p>
                {{-- <p class="text-white text-sm leading-relaxed indent-8">
                    Mencari les privat Bahasa Inggris terbaik di Dumai? The Master of Dumai adalah jawabannya. Les privat Bahasa Inggris terbaik di Dumai dengan kurikulum lengkap dari level dasar hingga persiapan TOEFL.
                </p> --}}
            </div>

            <div>
                <h3 class="text-lg font-bold text-white">Navigasi</h3>
                <ul class="mt-4 space-y-2 ">
                    <li><a href="/tentang-kami" class="hover:text-blue-200 text-white hover:underline">Tentang The Master of Dumai</a></li>
                    <li><a href="/kontak" class="hover:text-blue-200 text-white hover:underline">Kontak Kami</a></li>
                    <li><a href="/visi-misi" class="hover:text-blue-200 text-white hover:underline">Visi & Misi</a></li>
                    <li><a href="/testimoni" class="hover:text-blue-200 text-white hover:underline">Testimonial</a></li>
                    <li><a href="/staff" class="hover:text-blue-200 text-white hover:underline">Tim Kami</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold text-white">Program Kami</h3>
                <ul class="mt-4 space-y-2">
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="//master-preschool" wire:navigate>Master PRE-school</a></li>
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="/kids" wire:navigate>Master Kids</a></li>
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="//master-conversation" wire:navigate>Master Conversation</a></li>
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="/master-privat" wire:navigate>Master Private</a></li>
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="/master-toefl-preparation" wire:navigate>Master Toefl Preparation</a></li>
                    <li><a class="whitespace-nowrap hover:text-blue-200 text-white hover:underline" href="/master-onsite-training" wire:navigate>Master On-site Training</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold text-white">Our Office</h3>
                <div class="mt-4 space-y-4 text-blue-200">
                    <div class="flex items-start">
                        <svg class="flex-shrink-0 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <p class="ml-3 text-white">Jl. Sultan Hasanuddin, Ratu Sima, Kec. Dumai Barat, Kota Dumai, Riau 28811</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="flex-shrink-0 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h8.25a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25H8.25a2.25 2.25 0 01-2.25-2.25v-9a2.25 2.25 0 012.25-2.25z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 6.75V4.5a2.25 2.25 0 10-4.5 0v2.25" /></svg>
                        <p class="ml-3 text-white">+62 812-7770-4026</p>
                    </div>
                    <div class="flex items-start">
                        <svg class="flex-shrink-0 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" /></svg>
                        <p class="ml-3 text-white">+62 812-7770-4026</p>
                    </div>
                </div>

                <h3 class="mt-8 text-lg font-bold text-white">Follow Us</h3>
                    <div class="flex mt-4 space-x-4">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/themasterofdumai" class="hover:text-blue-200 text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/themasterofdumai" class="hover:text-blue-200 text-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.341 4.47c.636-.247 1.363-.416 2.427-.465C9.795 2.013 10.148 2 12.315 2zm-1.002 6.363a4.951 4.951 0 100 9.9 4.951 4.951 0 000-9.9zm-3.004 4.951a3.003 3.003 0 116.006 0 3.003 3.003 0 01-6.006 0zm11.002-6.502a1.2 1.2 0 100-2.4 1.2 1.2 0 000 2.4z" clip-rule="evenodd" /></svg>
                        </a>
                        <!-- LinkedIn -->
                        <a href="#" class="hover:text-blue-200 text-white">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" /></svg>
                        </a>
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@themasterofdumaiglobal" class="hover:text-blue-200 text-white">
                            <span class="sr-only">TikTok</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 48 48"><path d="M41,15.5c-2.9,0-5.5-0.9-7.7-2.5v14.4c0,7.8-6.3,14.1-14.1,14.1S5,35.2,5,27.4S11.3,13.3,19.1,13.3h0.6v7.4c-0.2,0-0.4,0-0.6,0c-3.7,0-6.7,3-6.7,6.7s3,6.7,6.7,6.7c3.7,0,6.7-3,6.7-6.7V5h7.3c0,0.3,0,0.6,0,0.9c0,3.5,2.8,6.4,6.3,6.4c0.4,0,0.9,0,1.3-0.1V15.5z"/></svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://www.youtube.com/@themasterglobalchannel6129" class="hover:text-blue-200 text-white">
                            <span class="sr-only">YouTube</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a2.974 2.974 0 0 0-2.093-2.106C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.405.58A2.974 2.974 0 0 0 .502 6.186C0 8.092 0 12 0 12s0 3.908.502 5.814a2.974 2.974 0 0 0 2.093 2.106c1.9.58 9.405.58 9.405.58s7.505 0 9.405-.58a2.974 2.974 0 0 0 2.093-2.106C24 15.908 24 12 24 12s0-3.908-.502-5.814zM9.75 15.02v-6.04L15.5 12l-5.75 3.02z"/></svg>
                        </a>
                    </div>

            </div>

        </div>
    </div>

    <div class="w-full text-center bg-white py-5 text-black px-5">
        © Copyright 2010 – 2025 The Master of Dumai English Course
    </div>
</footer>

<div class="fab">
    <div class="tooltip tooltip-left" data-tip="Masih Bingung? Jangan Sungkan untuk Bertanya!">
        <a tabindex="0" href="https://web.whatsapp.com/send?phone=6281277704026&text=Hallo%20admin%20The Master of Dumai,%20saya%20berminat%20kursus%20bahasa%20inggris" role="button" class="btn btn-lg w-20 h-20 btn-circle bg-green-600" >
            <svg class="w-12 h-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
            </svg>
        </a>
    </div>
</div>



  </body>
</html>