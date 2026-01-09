<div class="bg-slate-50 min-h-screen">
    <main class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="bg-white p-6 sm:p-10 rounded-2xl shadow-lg">
                
                {{-- Judul Artikel --}}
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $article->title }}
                </h1>

                {{-- Meta Info (Penulis & Tanggal) --}}
                <div class="mt-4 flex items-center text-slate-500 text-sm sm:text-base">
                    <span class="font-medium text-indigo-600">The Master of Dumai</span>
                    <span class="mx-2">•</span>
                    <time datetime="{{ $article->published_at }}">
                        {{ \Carbon\Carbon::parse($article->published_at)->format('d F Y, H:i') }} WIB
                    </time>
                </div>

                {{-- Gambar Utama --}}
                @if ($article->image)
                    <figure class="mt-8 overflow-hidden rounded-xl">
                        <img 
                            src="{{ Storage::url($article->image) }}" 
                            alt="{{ $article->title }}" 
                            class="w-full h-auto max-h-[500px] object-cover hover:scale-105 transition-transform duration-500"
                        >
                    </figure>
                @endif

                {{-- ISI ARTIKEL (BODY) --}}
                {{-- Class 'prose' wajib ada agar Bold, List, & Paragraf muncul rapi --}}
                <div class="prose prose-lg prose-slate max-w-none mt-10">
                    {!! $article->body !!}
                </div>

                {{-- Tombol Share --}}
                <div class="mt-12 border-t border-slate-200 pt-8">
                    <p class="text-sm font-semibold text-slate-700 mb-4 uppercase tracking-wide">Bagikan artikel ini:</p>
                    <div class="flex space-x-4">
                        
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                           target="_blank"
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 5.002 3.657 9.128 8.438 9.878v-6.987H7.898v-2.89h2.54V9.797c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.462h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.89h-2.33V21.88C18.343 21.128 22 17.002 22 12Z"/>
                            </svg>
                        </a>

                        {{-- Twitter / X --}}
                        <a href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}"
                           target="_blank"
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-800 hover:bg-black hover:text-white transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->fullUrl()) }}"
                           target="_blank"
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600 hover:bg-green-600 hover:text-white transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 .5a11.5 11.5 0 0 0-9.93 17.32L.5 23.5l5.84-1.54A11.5 11.5 0 1 0 12 .5Zm0 21A9.5 9.5 0 0 1 6.24 5.18a9.5 9.5 0 0 1 13.52 13.52A9.46 9.46 0 0 1 12 21.5Zm4.67-7.52c-.25-.12-1.48-.73-1.7-.82s-.39-.12-.55.12-.63.82-.77.98-.28.18-.53.06a7.77 7.77 0 0 1-2.28-1.41 8.59 8.59 0 0 1-1.59-1.98c-.17-.29 0-.45.13-.57.13-.13.29-.34.43-.5.14-.17.18-.29.27-.48s.04-.36-.02-.5c-.06-.12-.55-1.32-.76-1.8-.2-.48-.4-.42-.55-.43h-.47c-.16 0-.42.06-.64.29-.22.24-.84.82-.84 2s.86 2.3.98 2.46c.12.17 1.69 2.58 4.1 3.62.57.25 1.01.4 1.36.51.57.18 1.1.15 1.51.09.46-.07 1.48-.6 1.7-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.29Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </article>
        </div>
    </main>
</div>