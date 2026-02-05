<div>
    <div class="pt-5">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Berita & Informasi Terkini</h1>
            <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-slate-600">
                Ikuti perkembangan terbaru seputar dunia pendidikan, program kursus, dan kegiatan The Master of Dumai. Dapatkan informasi terkini agar kamu tidak ketinggalan berita penting.
            </p>
        </div>
    </div>
    
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- LOOPING ARTIKEL DIMULAI DI SINI --}}
                @forelse ($articles as $article)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                        
                        {{-- GAMBAR UTAMA ARTIKEL --}}
                        <a href="{{ route('articles.show', $article->slug) }}" class="block relative h-48 bg-slate-200">
                            
                            @if ($article->image)
                            
                                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-6 flex flex-col flex-grow">
                            @php
                            $badgeColor = match ($article->type) {
                                'article' => 'bg-emerald-100 text-emerald-800', 
                                'news' => 'bg-indigo-100 text-indigo-800',       
                                default => 'bg-gray-100 text-gray-800',
                            };

                            $badgeLabel = match ($article->type) {
                                'article' => 'Artikel',
                                'news' => 'Berita',
                                default => 'Info',
                            };
                        @endphp

               
                        <span class="inline-block {{ $badgeColor }} bg-indigo-800 text-white text-xs font-semibold px-2.5 py-1 rounded-full w-fit uppercase tracking-wide">
                            {{ $badgeLabel }}
                        </span>
                            
                            <h2 class="mt-4 text-xl font-bold text-slate-800 flex-grow">
                                <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            
                            <p class="mt-2 text-base text-slate-600 line-clamp-3">
                                {{ $article->excerpt }}
                            </p>
                            
                            
                            <div class="mt-6 flex items-center">
                                <div class="flex-shrink-0">
                                    @if ($article->user && $article->user->photo)
                                        <img class="h-10 w-10 rounded-full object-cover border border-slate-200" 
                                             src="{{ Storage::url($article->user->photo) }}" 
                                             alt="{{ $article->user->name }}">
                                    @else
                                        
                                        <img class="h-10 w-10 rounded-full object-cover border border-slate-200" 
                                             src="{{ asset('images/isroq.jpg') }}" 
                                             alt="{{ $article->user->name ?? 'Admin' }}">
                                    @endif
                                </div>
                                
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $article->user->name ?? 'Admin' }}
                                    </p>
                                    
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($article->published_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>


                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada artikel</h3>
                        <p class="mt-1 text-sm text-gray-500">Nantikan informasi terbaru dari kami.</p>
                    </div>
                @endforelse

            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        </div>
    </main>
</div>