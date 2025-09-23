<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home;
use App\Livewire\ArticlePage;
use App\Livewire\Preschool;
use App\Livewire\Kids;
use App\Livewire\Privat;
use App\Livewire\Conversation;
use App\Livewire\Toefl;
use App\Livewire\Onsite;
use App\Livewire\Testimoni;
use App\Livewire\Tentangkami;
use App\Livewire\Kontak;
use App\Livewire\Staff;
use App\Livewire\Instruktur;
use App\Livewire\Visimisi;
use App\Livewire\ShowArticle;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Article;

Route::get('/', Home::class);
Route::get('/artikel', ArticlePage::class)->name('artikel');
Route::get('/master-preschool', Preschool::class)->name('master-preschool');
Route::get('/master-kids', Kids::class)->name('master-kids');
Route::get('/master-conversation', Conversation::class)->name('master-conversation');
Route::get('/master-privat', Privat::class)->name('master-privat');
Route::get('/master-toefl-preparation', Toefl::class)->name('master-toefl-preparation');
Route::get('/master-onsite-training', Onsite::class)->name('master-onsite-training');
Route::get('/testimoni', Testimoni::class)->name('testimoni');
Route::get('/tentang-kami', Tentangkami::class)->name('tentang-kami');
Route::get('/kontak', Kontak::class)->name('kontak');
Route::get('/staff', Staff::class)->name('staff');
Route::get('/instruktur', Instruktur::class)->name('instruktur');
Route::get('/visi-misi', Visimisi::class)->name('visi-misi');
Route::get('/articles/{slug}', ShowArticle::class)->name('articles.show');

Route::get('/generate-sitemap', function () {

    $sitemap = Sitemap::create()
        // Menambahkan semua halaman statis dari website Anda
        ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
        ->add(Url::create('/artikel')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
        ->add(Url::create('/master-preschool')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/master-kids')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/master-conversation')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/master-privat')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/master-toefl-preparation')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/master-onsite-training')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/testimoni')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
        ->add(Url::create('/tentang-kami')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
        ->add(Url::create('/kontak')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
        ->add(Url::create('/staff')->setPriority(0.6)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
        ->add(Url::create('/instruktur')->setPriority(0.6)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
        ->add(Url::create('/visi-misi')->setPriority(0.6)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY));

    // Mengambil semua artikel dari database untuk ditambahkan ke sitemap
    // Pastikan Model 'Article' dan kolom 'slug' & 'updated_at' ada.
    $articles = Article::all();
    foreach ($articles as $article) {
        $sitemap->add(Url::create("/articles/{$article->slug}")->setLastModificationDate($article->updated_at));
    }

    // Menyimpan file sitemap.xml ke folder public
    $sitemap->writeToFile(public_path('sitemap.xml'));

    return 'Sitemap berhasil dibuat!';
});

