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

                {{-- Loop untuk setiap artikel dari database --}}
                @forelse ($articles as $article)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                        <a href="#" class="block relative h-48 bg-slate-200">
                            {{-- Tampilkan gambar jika ada, jika tidak tampilkan placeholder --}}
                            @if ($article->image)
                                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-200"></div>
                            @endif
                        </a>
                        <div class="p-6 flex flex-col flex-grow">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-1 rounded-full">News</span>
                            <h2 class="mt-4 text-xl font-bold text-slate-800 flex-grow">
                                {{-- Judul Artikel --}}
                                <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-indigo-600 transition-colors">{{ $article->title }}</a>
                            </h2>
                            <p class="mt-2 text-base text-slate-600 indent-8">
                                {{-- Ringkasan Artikel --}}
                                {{ $article->excerpt }}
                            </p>
                            <div class="mt-6 flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover" src="https://i.pravatar.cc/40" alt="Author avatar">
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-gray-900">Admin</p>
                                    {{-- Tanggal Publikasi --}}
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($article->published_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    {{-- Tampilan jika tidak ada artikel --}}
                    <div class="col-span-full text-center py-12">
                        <p class="text-slate-500">Belum ada artikel yang dipublikasikan.</p>
                    </div>
                @endforelse

            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        </div>
    </main>
</div>