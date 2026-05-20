<?php
use App\Http\Controllers\PrintReportController;
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
use App\Livewire\Pendaftaran;
use App\Livewire\PendaftaranSiswa;
use App\Livewire\StatusPendaftaran;
use App\Livewire\LoginSiswa;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use App\Models\ClassSession;
use App\Models\Siswa;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Assessment;

use App\Http\Controllers\ReceiptController;

Route::post('/api/save-multiple-receipt-proof', function (Illuminate\Http\Request $request) {
    try {
        $img = $request->image;
        $billIds = $request->bill_ids; // Menerima banyak ID sekaligus dalam bentuk array

        if (!$img || empty($billIds)) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        // Gunakan ID tagihan pertama atau timestamp untuk nama file gambarnya
        $randomName = 'receipt-bulk-' . time() . '.png';
        $fullPath = 'proofs/' . $randomName;
        
        if (!Storage::disk('public')->exists('proofs')) {
            Storage::disk('public')->makeDirectory('proofs');
        }

        // Simpan file fisik (cukup 1 file gambar struk gabungan untuk menghemat penyimpanan VPS)
        Storage::disk('public')->put($fullPath, $data);

        // UPDATE SEMUA TAGIHAN YANG TERLIBAT
        \App\Models\Bill::whereIn('id', $billIds)->update([
            'proof_of_payment' => $fullPath
        ]);

        return response()->json(['success' => true, 'path' => $fullPath]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('api.save-multiple-receipt-proof');
Route::post('/api/save-receipt-proof/{bill}', function (App\Models\Bill $bill, Illuminate\Http\Request $request) {
    try {
        $img = $request->image;
        if (!$img) return response()->json(['error' => 'No image data'], 400);

        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        // Buat nama file unik murni
        $randomName = 'receipt-' . $bill->id . '-' . time() . '.png';
        $fullPath = 'proofs/' . $randomName;
        
        // Pastikan folder 'proofs' ada di disk public
        if (!Storage::disk('public')->exists('proofs')) {
            Storage::disk('public')->makeDirectory('proofs');
        }

        // Simpan file fisik ke storage/app/public/proofs/receipt-xxx.png
        Storage::disk('public')->put($fullPath, $data);

        // UPDATE DATABASE
        // Simpan nilai path relatif yang dikenali oleh Filament FileUpload
        $bill->update([
            'proof_of_payment' => $fullPath
        ]);

        return response()->json(['success' => true, 'path' => $fullPath]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('api.save-receipt-proof');

Route::get('/print-receipt-collective', [ReceiptController::class, 'printCollective'])->name('print.receipt.collective');

Route::get('/print-receipt/{bill}', [App\Http\Controllers\ReceiptController::class, 'printSingle'])->name('print.receipt');
Route::get('/test-aja', function() { return "OK"; });
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
        ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('daily'))
        ->add(Url::create('/artikel')->setPriority(0.9)->setChangeFrequency('weekly'))
        ->add(Url::create('/master-preschool')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/master-kids')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/master-conversation')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/master-privat')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/master-toefl-preparation')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/master-onsite-training')->setPriority(0.8)->setChangeFrequency('monthly'))
        ->add(Url::create('/testimoni')->setPriority(0.7)->setChangeFrequency('yearly'))
        ->add(Url::create('/tentang-kami')->setPriority(0.7)->setChangeFrequency('yearly'))
        ->add(Url::create('/kontak')->setPriority(0.7)->setChangeFrequency('yearly'))
        ->add(Url::create('/staff')->setPriority(0.6)->setChangeFrequency('yearly'))
        ->add(Url::create('/instruktur')->setPriority(0.6)->setChangeFrequency('yearly'))
        ->add(Url::create('/visi-misi')->setPriority(0.6)->setChangeFrequency('yearly'));

    // Tambahkan artikel
    $articles = Article::all();
    foreach ($articles as $article) {
        $sitemap->add(
            Url::create("/articles/{$article->slug}")
                ->setLastModificationDate($article->updated_at ?? now())
                ->setPriority(0.7)
                ->setChangeFrequency('weekly')
        );
    }

    $sitemap->writeToFile(public_path('sitemap.xml'));

    return '✅ Sitemap berhasil dibuat!';
});

Route::middleware('auth')->group(function () {
    // Rute Cetak Laporan Keuangan
    Route::get('/print/finance', [PrintReportController::class, 'printFinance'])
        ->name('print.finance');
    Route::get('/print/cash-book', [PrintReportController::class, 'printCashBook'])
    ->name('print.cash_book');
        
    // (Opsional) Anda juga bisa memindahkan rute inventory ke sini agar kumpul jadi satu
    // Route::get('/print/inventory', [PrintReportController::class, 'printInventory'])->name('print.inventory');
});

Route::get('/pendaftaran', Pendaftaran::class)->name('pendaftaran.home');
Route::get('/pendaftaran/register', PendaftaranSiswa::class)->name('pendaftaran.register');
Route::get('/pendaftaran/status', StatusPendaftaran::class)
    ->middleware('auth:registration') // Hanya bisa dibuka jika sudah login/daftar
    ->name('pendaftaran.status');
Route::post('/pendaftaran/logout', function () {
    Auth::guard('registration')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/pendaftaran');
})->name('logout');

Route::get('/pendaftaran/login', LoginSiswa::class)->name('login');
Route::impersonate();

Route::get('/impersonate/leave-custom', function () {
   
    app(\Lab404\Impersonate\Services\ImpersonateManager::class)->leave();

    
    return redirect()->to('/studentflow/users'); 
})->name('impersonate.leave.custom');

Route::get('/print-attendance/{program}', function (Program $program) {
    // 1. Ambil Sesi
    $sessions = ClassSession::where('program_id', $program->id)
        ->orWhere('is_ramadhan_session', true)
        ->orderBy('session_date')
        ->get();

    $sessionIds = $sessions->pluck('id');

    // 2. Ambil Siswa
    $siswas = Siswa::where('program_id', $program->id)
        ->orderBy('nama')
        ->get();

    // 3. Ambil Data Absensi
    $attendances = Attendance::whereIn('siswa_id', $siswas->pluck('id'))
        ->whereIn('class_session_id', $sessionIds)
        ->get();

    $attendanceData = $attendances->groupBy('siswa_id')
        ->map(fn($item) => $item->pluck('status', 'class_session_id'))
        ->toArray();

    // --- MULAI LOGIKA PERHITUNGAN RATE ---
    $attendanceScores = [];

    foreach ($siswas as $siswa) {
        $dataAbsenSiswa = $attendanceData[$siswa->id] ?? [];
        $sessionIdsPernahAbsen = array_keys($dataAbsenSiswa);

        // Cari Jendela Waktu
        $tanggalSesiPertama = $sessions->whereIn('id', $sessionIdsPernahAbsen)->min('session_date');
        $startLimit = $tanggalSesiPertama ?: $siswa->created_at->format('Y-m-d');
        $endLimit = $sessions->max('session_date') ?: now()->format('Y-m-d');

        // Cari Sesi Efektif
        $sesiEfektif = $sessions->filter(function ($session) use ($startLimit, $endLimit, $program, $sessionIdsPernahAbsen) {
            $isDateValid = $session->session_date >= $startLimit && $session->session_date <= $endLimit;
            $isRegularSession = $session->program_id === $program->id;
            $isFollowedRamadhan = $session->is_ramadhan_session && in_array($session->id, $sessionIdsPernahAbsen);

            return $isDateValid && ($isRegularSession || $isFollowedRamadhan);
        });

        $totalSesiEfektif = $sesiEfektif->count();
        $sesiEfektifIds = $sesiEfektif->pluck('id');

        // Hitung Hadir
        $hadirCount = collect($dataAbsenSiswa)
            ->filter(fn($status, $sessionId) => $status === 'Hadir' && $sesiEfektifIds->contains($sessionId))
            ->count();

        $score = $totalSesiEfektif > 0 ? ($hadirCount / $totalSesiEfektif) * 100 : 0;
        
        $attendanceScores[$siswa->id] = [
            'score' => round(min($score, 100)),
            'total' => $totalSesiEfektif,
            'hadir' => $hadirCount,
        ];
    }
    // --- SELESAI LOGIKA PERHITUNGAN RATE ---

    return view('print.attendance-recap', [
        'program' => $program,
        'sessions' => $sessions,
        'siswas' => $siswas,
        'attendanceData' => $attendanceData,
        'attendanceScores' => $attendanceScores, // Pastikan ini dikirim
    ]);
})->name('print.attendance')->middleware('auth');

Route::get('/print-grades/{program}', function (Program $program) {
    $assessments = $program->assessments()->orderBy('order')->get();
    $students = $program->siswas()->orderBy('nama')->get();
    
    // Identifikasi Ujian Semester dan Review
    $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
    $semesterTestId = $semesterTest ? $semesterTest->id : null;
    $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');

    $scoringData = $students->map(function ($student) use ($reviewIds, $semesterTestId) {
        $allGrades = Grade::where('student_id', $student->id)->get();
        $reviewGrades = $allGrades->whereIn('assessment_id', $reviewIds);
        $semesterGrade = $semesterTestId ? $allGrades->where('assessment_id', $semesterTestId)->first() : null;

        $calc = function($col) use ($reviewGrades, $semesterGrade) {
            $avgReview = (float)($reviewGrades->avg($col) ?? 0);
            $scoreSem = (float)($semesterGrade->$col ?? 0);

            // Jika ada nilai semester, gunakan rumus: (Avg Review + Semester) / 2
            if ($semesterGrade) {
                return ($avgReview + $scoreSem) / 2;
            }
            return $avgReview;
        };

        $l = $calc('listening'); $r = $calc('reading'); $w = $calc('writing');
        $s = $calc('speaking'); $g = $calc('grammar');
        $total = $l + $r + $w + $s + $g;
        $final = $total / 5;

        return [
            'nama' => $student->nama,
            'l' => $l, 'r' => $r, 'w' => $w, 's' => $s, 'g' => $g,
            'total' => $total,
            'final' => $final,
        ];
    })->sortByDesc('final')->values();

    return view('print.scoring-sheet', [
        'program' => $program,
        'data' => $scoringData,
    ]);
})->name('print.grades')->middleware('auth');

Route::get('/print-all-reviews/{program}', function (Program $program) {
    // Ambil semua ujian (Review & Semester)
    $assessments = Assessment::where('program_id', $program->id)
        ->orderBy('order')
        ->get();

    // Ambil semua siswa (Maksimal 7-8 siswa per tabel agar muat ke samping)
    $students = $program->siswas()->orderBy('nama')->get();

    // Ambil data nilai
    $grades = Grade::whereIn('assessment_id', $assessments->pluck('id'))
        ->get();

    return view('print.all-reviews', [
        'program' => $program,
        'assessments' => $assessments,
        'students' => $students,
        'grades' => $grades,
    ]);
})->name('print.all.reviews')->middleware('auth');

