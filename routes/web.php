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

