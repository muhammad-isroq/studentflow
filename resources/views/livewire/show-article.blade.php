<div class="">
    <main class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <article>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ $article->title }}</h1>

                <div class="mt-4 text-slate-600">
                    By The Master of Dumai / {{ \Carbon\Carbon::parse($article->published_at)->format('d M Y H:i') }}
                </div>

                @if ($article->image)
                    <figure class="mt-8">
                        <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="w-full h-120 rounded-lg shadow-md object-cover">
                    </figure>
                @endif

                <div class="prose prose-lg max-w-none mt-8 indent-8">
                    {!! $article->body !!}
                </div>
                <div class="mt-12 border-t pt-6">
    <p class="text-sm text-slate-600 mb-3">Bagikan artikel ini:</p>
    <div class="flex space-x-4">
        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
           target="_blank"
           class="text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 5.002 3.657 9.128 8.438 9.878v-6.987H7.898v-2.89h2.54V9.797c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.462h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.89h-2.33V21.88C18.343 21.128 22 17.002 22 12Z"/>
            </svg>
        </a>

        <!-- Twitter / X -->
        <a href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}"
           target="_blank"
           class="text-sky-500 hover:text-sky-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.2 4.2 0 0 0 1.85-2.32 8.27 8.27 0 0 1-2.64 1.01 4.13 4.13 0 0 0-7.06 3.76A11.7 11.7 0 0 1 3.16 4.9a4.13 4.13 0 0 0 1.28 5.5 4.07 4.07 0 0 1-1.87-.52v.05a4.14 4.14 0 0 0 3.32 4.05 4.22 4.22 0 0 1-1.85.07 4.14 4.14 0 0 0 3.86 2.87A8.31 8.31 0 0 1 2 19.54a11.7 11.7 0 0 0 6.29 1.84c7.55 0 11.68-6.26 11.68-11.68 0-.18-.01-.35-.02-.53A8.36 8.36 0 0 0 22.46 6z"/>
            </svg>
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->fullUrl()) }}"
           target="_blank"
           class="text-green-500 hover:text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 .5a11.5 11.5 0 0 0-9.93 17.32L.5 23.5l5.84-1.54A11.5 11.5 0 1 0 12 .5Zm0 21A9.5 9.5 0 0 1 6.24 5.18a9.5 9.5 0 0 1 13.52 13.52A9.46 9.46 0 0 1 12 21.5Zm4.67-7.52c-.25-.12-1.48-.73-1.7-.82s-.39-.12-.55.12-.63.82-.77.98-.28.18-.53.06a7.77 7.77 0 0 1-2.28-1.41 8.59 8.59 0 0 1-1.59-1.98c-.17-.29 0-.45.13-.57.13-.13.29-.34.43-.5.14-.17.18-.29.27-.48s.04-.36-.02-.5c-.06-.12-.55-1.32-.76-1.8-.2-.48-.4-.42-.55-.43h-.47c-.16 0-.42.06-.64.29-.22.24-.84.82-.84 2s.86 2.3.98 2.46c.12.17 1.69 2.58 4.1 3.62.57.25 1.01.4 1.36.51.57.18 1.1.15 1.51.09.46-.07 1.48-.6 1.7-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.29Z"/>
            </svg>
        </a>
    </div>
</div>

            </article>
        </div>
    </main>
</div>