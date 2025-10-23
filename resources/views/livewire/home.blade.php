<style>

.card {
  width: 320px;
  height: 320px;
  perspective: 1000px;
}

.card-inner {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  transition: transform 0.999s;
}

.card:hover .card-inner {
  transform: rotateY(180deg);
}

.card-front,
.card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
}

.card-front {
  /* background-color: #4b53ce; */
  color: #fff;
  /* display: flex;
  align-items: center;
  border: 10px solid #4b53ce; */
  border-radius: 10px;
  /* justify-content: center; */
  font-size: 14px;
  transform: rotateY(0deg);
  overflow: hidden;
}

.card-back {
  background-color: #F08A5D;
  color: #fff;
  display: flex;
  align-items: center;
  border: 10px solid #F08A5D;
  border-radius: 10px;
  justify-content: center;
  font-size: 14px;
  transform: rotateY(180deg);
  padding: 1rem;
}

</style>
<div>
  <h1 class="sr-only">Kursus Bahasa Inggris di Dumai – The Master of Dumai</h1>
    <div class="carousel w-full h-[700px] overflow-x-hidden relative" id="myCarousel">
  <div class="carousel-item relative w-full flex-shrink-0" id="slide1">
    <img src="{{ asset('images/11.jpg') }}" class="w-full object-cover" />

    <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center text-white px-4">
      <h2 class="text-4xl font-bold">Shape Your Future with Confidence</h2>
      <p class="mt-2 text-lg">
        Belajar bahasa Inggris mudah, menyenangkan, dan efektif bersama The Master Of Dumai.
      </p>
      <a href="https://wa.me/6281277704026?text=Hallo%20admin%20The%20Master%20of%20Dumai,%20saya%20berminat%20kursus%20bahasa%20inggris
" class="btn bg-orange-500 border-none mt-4">Daftar Kursus</a>
    </div>
  </div>

  <div class="carousel-item relative w-full flex-shrink-0" id="slide2">
    <img src="{{ asset('images/12.jpeg') }}" class="w-full object-cover" />
    <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center text-white px-4">
      <h2 class="text-4xl font-bold">Learn Anytime, Anywhere</h2>
      <p class="mt-2 text-lg">
        Belajar fleksibel dengan materi interaktif dan tutor berpengalaman
      </p>
      <a href="https://wa.me/6281277704026?text=Hallo%20admin%20The%20Master%20of%20Dumai,%20saya%20berminat%20kursus%20bahasa%20inggris
" class="btn bg-orange-500 border-none mt-4">Daftar Kursus</a>
    </div>
  </div>

  <div class="carousel-item relative w-full flex-shrink-0" id="slide3">
    <img src="{{ asset('images/13.jpeg') }}" class="w-full object-cover" />
    <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center text-white px-4">
      <h2 class="text-4xl font-bold">Boost Your Career with English</h2>
      <p class="mt-2 text-lg">
        Tingkatkan peluang karier dengan penguasaan bahasa Inggris profesional.
      </p>
      <a href="https://wa.me/6281277704026?text=Hallo%20admin%20The%20Master%20of%20Dumai,%20saya%20berminat%20kursus%20bahasa%20inggris
" class="btn bg-orange-500 border-none mt-4">Daftar Kursus</a>
    </div>
  </div>
</div>
    
        <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg p-6 mb-5">
              <p class="mt-2 text-1xl text-center mb-3">
                    Apa Targetmu?
                </p>
                <h3 class="text-3xl font-medium text-gray-800 italic text-center ">Tentukan Target Belajar Bahasa Inggris!</h3>
                <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
              <p class="mt-2 text-1xl text-center">
                Belajar Bahasa Inggris bisa untuk banyak tujuan. Mulai dari Mengejar mimpi kuliah di kampus internasional, memperluas karier, menaklukkan tes bahasa, dan membangun kepercayaan diri.
              </p>
            </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
    
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
              </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Persiapan Ujian Internasional</h3>
              <p class="text-gray-600 mt-2 text-sm">
                Raih skor terbaik TOEFL, IELTS, dan ujian bahasa Inggris lainnya dengan bimbingan terarah serta simulasi intensif.
              </p>
            </div>

            
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Program Reguler</h3>
              <p class="text-gray-600 mt-2 text-sm">
                Ikuti kelas Bahasa Inggris dengan kurikulum terpercaya yang dirancang untuk semua level, mulai dari pemula hingga mahir.
              </p>
            </div>

            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
              </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Pengembangan Karier</h3>
              <p class="text-gray-600 mt-2 text-sm">
                Tingkatkan keterampilan komunikasi profesional untuk mendukung pekerjaan, presentasi, hingga peluang karier global.
              </p>
            </div>

            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Peningkatan Kompetensi</h3>
              <p class="text-gray-600 mt-2 text-sm">
                Perdalam pemahaman dan keterampilan berbahasa Inggris untuk menunjang kebutuhan akademik maupun profesional.
              </p>
            </div>

          </div>
        </div>

        <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg p-6 mb-5">
                <h3 class="text-3xl font-medium text-gray-800 text-center ">Strategi Belajar</h3>
                <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
                <p class="mt-2 text-1xl text-center">
                  Tentukan Program Bahasa Inggris Terbaik untuk Mencapai Tujuanmu
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1549490121-3e414c81a62e?q=80&w=2070&auto=format&fit=crop" 
               alt="Master Pre-School" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white">Master Pre-Scool</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program yang dirancang untuk anak usia dini (4-6 tahun) yang bertujuan untuk memberikan pengetahun penggunaan bahasa inggris dalam sehari-hariyang berbasis fun learning atau bermain dan belajar. Program ini cocok diikuit sebelum anak memulai sekolah TK dan SD</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1503944583232-2d94c0400451?q=80&w=2070&auto=format&fit=crop" 
               alt="Master Kids" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white">Master Kids</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program yang dirancang untuk anak sekolah dasar (6-12 tahun) dengan tujuan memberikan eksponsor penggunaan bahasa inggris sehari-hari. Pelajaran diampaikan secara komunikatif dan menyenangkan dengan media game di setiap sesi pertemuan. Sehingga anak merasa senang dalam mempelajarai bahasa inggris</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2071&auto=format&fit=crop" 
               alt="Master Conversation" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white">Master Conversation</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program yang dirancang untuk siswa tingkat SMP, SMA danUmum dengan tujuan meningkatkan kemampuan komunikasi dan percaya diri dalam bahasa inggris baik secara lisan maupun tulisan</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1516534778568-b6c41144a406?q=80&w=2070&auto=format&fit=crop" 
               alt="Master Private" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white">Master Private</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program yang dirancang untuk siswa tingkat SMP, SMA dan Umum dengan tujaun meningkatkan kemampuan komunikasi dan percaya diri dalam bahasa inggris baik secara lisan maupun tulisan. Program ini merupakan program yang ditujukan untuk siswa yang ingin belajar bahasa inggris dengan materi dan kurikulum yang dapat disesuaikan dengan kebutuhan siswa baik dari tingkat pemula ataupun lanjutan</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070&auto=format&fit=crop" 
               alt="Master TOEFL" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white text-center">Master TOEFL Preparation</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program ini utnuk mereka yang sedang mempersiapkan diri untuk mengambil test TOEFL, baik untuk keperluan studi lanjut di dalam dan luar negeri maupun untuk tujuan-tujuan lainnya seperti persyaratan penerimaan pegawai, promosi jabatan dan sebagainya.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex flex-col items-center text-center">
    <div class="card p-4">
      <div class="card-inner">
        <div class="card-front">
          <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop" 
               alt="On-Site Training" 
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 flex flex-col items-center justify-center w-full h-full p-4">
            <div class="text-lg font-bold text-white text-center">Master On-Site Training</div>
          </div>
        </div>
        <div class="card-back">
          <div>Program ini ditawarkan kepada perusahaan yang membutuhkan peningkatan skill SDM terkait oenguasaan bahasa inggris terhadap karyawan. kami menyediakan program ini dengan sistem yang mudah dipahami dan cepat untuk dipelajari</div>
        </div>
      </div>
    </div>
  </div>

</div>
        </div>

      <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg p-6 mb-5">
          <h3 class="text-3xl font-medium text-gray-800 text-left ps-5 pb-6">
            Pembelajaran Sesuai Tingkat Kemampuan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="flex flex-col items-center text-center">
              <div class="relative w-full max-w-lg mx-auto text-center">
              <img src="{{ asset('images/14.jpg') }}" class="w-full h-auto rounded-lg block">

              <h4 class="absolute bottom-5 left-5 text-white font-bold text-2xl drop-shadow-md">
                Master Pre-School
                <br>
                (4-6 tahun)
              </h4>
            </div>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="relative w-full max-w-lg mx-auto text-center">
                <img src="{{ asset('images/15.jpg') }}" class="w-full h-auto rounded-lg block">
                <h4 class="absolute bottom-5 left-5 text-white font-bold text-2xl drop-shadow-md">
                  Master Kids
                  <br>
                  (Anak-anak dan remaja)
                </h4>
              </div>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="relative w-full max-w-lg mx-auto text-center">
                <img src="{{ asset('images/16.jpg') }}" class="w-full h-auto rounded-lg block">
                <h4 class="absolute bottom-5 left-5 text-white font-bold text-2xl drop-shadow-md">
                  Master Conversation
                  <br>
                  
                </h4>
              </div>
            </div>
          </div>
        </div>
      </div>  
        
      <div class="w-full mx-auto py-8 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg p-6 mb-5">
              <h3 class="text-3xl font-medium text-gray-800 text-center ">Instruktur Kami</h3>
              <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
          </div>
          <div class="overflow-hidden max-w-6xl mx-auto" id="cardSlider">
            <div class="flex transition-transform duration-700 ease-in-out" id="cardTrack">
              @forelse ($teachers as $teacher)
              <div class="flex-none w-1/3 h-1/3 px-2">
                <div class="flex flex-col items-center text-center p-4 bg-white rounded-lg shadow-md">
                  <div class="avatar">
                    <div class="w-28 sm:w-40 rounded-full">
                      <img class="transition-transform duration-300 hover:scale-110"
                        src="{{ Storage::url($teacher->photo) }}"  alt="Foto {{ $teacher->name }}"/>
                    </div>
                  </div>
                  <p class="pt-3 font-bold">{{ $teacher->name }}</p>
                </div>
              </div>
              @empty
                
                <div class="w-full text-center py-12">
                    <p class="text-gray-500">Saat ini belum ada data instruktur yang bisa ditampilkan.</p>
                </div>
            @endforelse

            </div>
          </div>
      </div>

      <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg p-6 mb-5">
            <h3 class="text-3xl font-medium text-gray-800 text-center ">Kenapa Memilih Kursus Bahasa Inggris di The Master of Dumai?</h3>
            <p class="text-1xl font-medium text-gray-600 text-center pt-3">Inilah Alasan Utama yang Membuat Kami Berbeda dari Lainnyai.</p>
            <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
          </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Lembaga Kursus yang Terpercaya</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Sejak awal hadir di Dumai, kami konsisten menjadi pilihan utama masyarakat untuk belajar bahasa Inggris dengan kualitas terbaik.
              </p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>


              </div>
              <h3 class="text-lg font-semibold text-gray-800">Instruktur Berpengalaman</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Pengajar kami memiliki pengalaman panjang dalam mengajar dan membimbing siswa sehingga metode pembelajaran lebih efektif dan mudah dipahami.
              </p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                </svg>


              </div>
              <h3 class="text-lg font-semibold text-gray-800">Pembelajaran Interaktif</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Kelas dirancang dengan metode yang menarik dan variatif agar suasana belajar lebih hidup, menyenangkan, serta mudah diserap siswa.
              </p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Program Belajar Fleksibel</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Kami menyediakan berbagai pilihan program yang dapat menyesuaikan kebutuhan, mulai dari anak-anak hingga persiapan tes internasional.
              </p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Akses Mudah ke Lembaga Kami</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Berlokasi di titik strategis kota, kursus kami mudah dijangkau sehingga siswa dapat belajar dengan lebih nyaman tanpa hambatan jarak.
              </p>
            </div>
            <div class="flex flex-col items-center text-center">
              <div class="rounded-full bg-blue-100 p-4 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

              </div>
              <h3 class="text-lg font-semibold text-gray-800">Investasi Belajar yang Efisien</h3>
              <p class="text-gray-600 mt-2 text-sm px-6 md:px-0 lg:px-0">
                Dengan biaya kursus yang bersahabat, Anda bisa mendapatkan kualitas pembelajaran terbaik tanpa harus mengeluarkan biaya berlebihan.
              </p>
            </div>
        </div>
      </div>

      <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg p-6 mb-5">
          <p class="text-2xl font-medium text-gray-700 text-center mb-3">Ulasan</p>
          <h3 class="text-3xl font-medium text-gray-800 text-center">
            Suara Siswa tentang The Master of Dumai
          </h3>
          <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
        </div>
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-2">

      <div class="rounded-lg bg-white p-8 shadow-lg">
        <div class="flex items-center gap-4 sm:p-3">
          <img class="h-16 w-16 rounded-full object-cover" src="https://plus.unsplash.com/premium_photo-1671656349322-41de944d259b?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8bWFsZSUyMHByb2ZpbGV8ZW58MHx8MHx8fDA%3D" alt="Foto Karina Aulia">
          <div>
            <p class="font-bold text-slate-800">Iqbal Ramadhan</p>
            <p class="text-sm text-gray-500">Marketing Specialist</p>
          </div>
        </div>

        <h3 class="mt-6 text-xl font-bold text-slate-900">Mudah Dipahami</h3>
        <p class="mt-4 leading-relaxed text-gray-600">
          Awalnya saya ragu untuk ikut kursus lagi karena jadwal kerja yang padat. Tapi di sini, jadwalnya sangat fleksibel dan materi yang diajarkan benar-benar relevan dengan dunia kerja. Kemampuan presentasi dan menulis email dalam bahasa Inggris saya meningkat drastis. Para pengajar sangat profesional dan tahu apa yang kami butuhkan untuk karir. Sangat direkomendasikan untuk para profesional yang ingin upgrade skill!
        </p>

        <div class="mt-6 flex items-center gap-1">
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
        </div>
      </div>

      <div class="rounded-lg bg-white p-8 shadow-lg">
        <div class="flex items-center gap-4 sm:p-3">
          <img class="h-16 w-16 rounded-full object-cover" src="https://images.unsplash.com/photo-1564564321837-a57b7070ac4f?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8bWFsZSUyMHByb2ZpbGV8ZW58MHx8MHx8fDA%3D" alt="Foto M Oqbal Sutomo">
          <div>
            <p class="font-bold text-slate-800">Bintang Saputra</p>
            <p class="text-sm text-gray-500">Mahasiswa Teknik Informatika</p>
          </div>
        </div>

        <h3 class="mt-6 text-xl font-bold text-slate-900">Sangat Mengasyikkan</h3>
        <p class="mt-4 leading-relaxed text-gray-600">
          Sebagai mahasiswa, bahasa Inggris itu wajib untuk memahami jurnal internasional dan materi kuliah. Kursus ini benar-benar 'membuka mata' saya. Cara mengajarnya seru, tidak monoton, dan banyak sekali sesi diskusi kelompok yang melatih keberanian untuk berbicara. Dulu saya paling takut kalau disuruh presentasi dalam bahasa Inggris, sekarang malah jadi lebih percaya diri. Nilai plusnya, biayanya sangat ramah di kantong mahasiswa!
        </p>

        <div class="mt-6 flex items-center gap-1">
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
          </div>
      </div>

        </div>
      </div>


      <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-lg p-6 mb-5">
          <h3 class="text-3xl font-medium text-gray-800 text-center">
            Pertanyaan Umum / FAQ (Frequently Asked Questions)
          </h3>
          <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <div class="hidden lg:block">
          <img 
            src="{{ asset('images/20.png') }}" 
            alt="Staff" 
            class="w-full h-auto rounded-lg scale-x-[-1] -mt-20"
          >
        </div>

        <div class="px-6">
          <p class="text-red-600 font-semibold mb-2">Pertanyaan Umum</p>
          <h2 class="text-2xl font-bold text-gray-900 mb-8">
            You Can Find All Answers Here
          </h2>

          <div class="join join-vertical w-full">
            
            <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" checked="checked" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                <svg xmlns="{http://www.w3.org/2000/svg}" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Apakah saya harus bisa bahasa Inggris dulu sebelum ikut kursus?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Tidak perlu. Kami menyediakan kelas dari level dasar (Beginner) hingga mahir, jadi siapa pun bisa mulai sesuai kemampuan masing-masing.</p>
              </div>
            </div>
            
            <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>Berapa lama waktu yang dibutuhkan untuk bisa lancar bahasa Inggris?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Tergantung target dan intensitas belajar Anda. Rata-rata, dengan mengikuti kursus rutin 3–6 bulan, siswa sudah bisa berkomunikasi dalam percakapan sehari-hari dengan baik.</p>
              </div>
            </div>
            
             <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.284-1.255-.778-1.652M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.284-1.255.778-1.652M12 12a3 3 0 100-6 3 3 0 000 6z" /></svg>
                <span>Apakah ada kelas online atau hanya tatap muka?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Kami menyediakan dua pilihan: kelas tatap muka (offline) dan kelas online (via Zoom/Google Meet). Anda bisa pilih sesuai kebutuhan dan kenyamanan.</p>
              </div>
            </div>

            <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>

                <span>Apakah saya akan dapat sertifikat setelah selesai kursus?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Ya, setiap peserta yang menyelesaikan program kursus akan mendapatkan sertifikat resmi dari lembaga kami.</p>
              </div>
            </div>

            <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Apakah jadwal kursus bisa fleksibel?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Bisa. Kami memiliki jadwal pagi, siang, sore, hingga malam. Bahkan ada kelas privat dengan waktu yang bisa disesuaikan.</p>
              </div>
            </div>
            <div class="collapse collapse-plus join-item bg-indigo-950 text-white">
              <input type="radio" name="faq-accordion" /> 
              <div class="collapse-title text-lg font-medium flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span>Berapa biaya kursus bahasa Inggris di sini?</span>
              </div>
              <div class="collapse-content"> 
                <p class="indent-8">Biaya bervariasi tergantung program (dasar, remaja, profesional, atau persiapan tes internasional). Untuk info detail, silakan hubungi admin kami.</p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <section class="flex flex-col md:flex-row shadow-xl rounded-lg overflow-hidden">
        
        <div class="w-full md:w-5/12 bg-sky-800 text-white p-8 md:p-12">
            <div class="max-w-md mx-auto">
                <p class="text-blue-300 font-semibold">Get In Touch!</p>
                <h2 class="text-4xl font-bold mt-2">Ada Pertanyaan? Jangan Ragu untuk Menghubungi!</h2>

                <div class="mt-10 space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
                            </svg>
                        </div>
                        <p class="ml-4 text-lg">0812-7770-4026</p>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <p class="ml-4 text-lg">0812-7770-4026</p>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <p class="ml-4 text-lg">
                            Jl. Sultan Hasanuddin, Ratu Sima, Kec. Dumai Bar., Kota Dumai, Riau 28811
                        </p>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="https://wa.me/6281277704026?text=Hallo%20admin%20The%20Master%20of%20Dumai,%20saya%20berminat%20kursus%20bahasa%20inggris
" class="inline-block bg-pink-600 text-white font-bold text-lg py-3 px-8 rounded-md hover:bg-pink-700 transition-colors duration-300">
                        Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full md:w-7/12 h-96 md:h-auto">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.1225895790285!2d101.41994207473859!3d1.6705323983139817!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d3a921391fbd13%3A0x31834eaf56bf9257!2sThe%20Master%20of%20Dumai!5e0!3m2!1sen!2sid!4v1757574683232!5m2!1sen!2sid" width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </section>
    </div>

    <div class="w-full mx-auto py-6 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-lg p-6 mb-5">
        <h3 class="text-3xl font-medium text-gray-800 text-center">
            Cerita & Informasi Inspiratif
        </h3>
        <p class="text-1xl font-medium text-gray-700 py-3 text-center mb-3">Baca artikel bermanfaat yang kami siapkan khusus untuk Anda.</p>
        <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
      </div>

    <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <a href="#" class="block relative h-48 bg-slate-200">
                  <img src="{{ asset('images/1.jpg')}} " alt="">
                    <svg class="absolute bottom-0 left-0 w-full h-auto text-white" fill="currentColor" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1440,100H0V60c0,0,240-40,480,0s480,40,720,0s240-40,240-40V100z"></path>
                    </svg>
                </a>
                <div class="p-6">
                    <span class="inline-block bg-indigo-800 text-white text-xs font-bold uppercase px-3 py-1 rounded">Artikel</span>
                    <h2 class="mt-4 text-xl font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                        <a href="#">5 Alasan Mengapa Kursus Bahasa Inggris Bisa Mengubah Karier Anda</a>
                    </h2>
                    <p class="mt-2 text-base text-slate-600">
                        Bahasa Inggris bukan hanya sekadar keterampilan, tapi juga investasi. Pelajari bagaimana kemampuan bahasa Inggris dapat membuka peluang kerja baru, meningkatkan gaji, dan memperluas jaringan profesional Anda.
                    </p>
                </div>
            </article>

            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <a href="#" class="block relative h-48 bg-slate-200">
                  <img src="{{ asset('images/2.jpg')}} " alt="">
                    <svg class="absolute bottom-0 left-0 w-full h-auto text-white" fill="currentColor" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1440,100H0V60c0,0,240-40,480,0s480,40,720,0s240-40,240-40V100z"></path>
                    </svg>
                </a>
                <div class="p-6">
                    <span class="inline-block bg-indigo-800 text-white text-xs font-bold uppercase px-3 py-1 rounded">Artikel</span>
                    <h2 class="mt-4 text-xl font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                        <a href="#">Tips Efektif Belajar Bahasa Inggris untuk Pemula</a>
                    </h2>
                    <p class="mt-2 text-base text-slate-600">
                        Banyak orang merasa belajar bahasa Inggris itu sulit. Artikel ini membahas cara belajar yang menyenangkan dan mudah dipahami, mulai dari latihan sehari-hari hingga penggunaan aplikasi pendukung.
                    </p>
                </div>
            </article>

            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <a href="#" class="block relative h-48 bg-slate-200">
                  <img src="{{ asset('images/3.jpg')}} " alt="">
                    <svg class="absolute bottom-0 left-0 w-full h-auto text-white" fill="currentColor" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1440,100H0V60c0,0,240-40,480,0s480,40,720,0s240-40,240-40V100z"></path>
                    </svg>
                </a>
                <div class="p-6">
                    <span class="inline-block bg-indigo-800 text-white text-xs font-bold uppercase px-3 py-1 rounded">Artikel</span>
                    <h2 class="mt-4 text-xl font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                        <a href="#">Perbedaan Belajar Bahasa Inggris di Kelas dan Belajar Mandiri</a>
                    </h2>
                    <p class="mt-2 text-base text-slate-600">
                        Apakah lebih baik ikut kursus atau belajar sendiri? Artikel ini mengulas kelebihan dan kekurangan keduanya, sehingga Anda bisa memilih metode belajar yang sesuai dengan kebutuhan.
                    </p>
                </div>
            </article>

            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <a href="#" class="block relative h-48 bg-slate-200">
                  <img src="{{ asset('images/1.jpg')}} " alt="">
                    <svg class="absolute bottom-0 left-0 w-full h-auto text-white" fill="currentColor" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1440,100H0V60c0,0,240-40,480,0s480,40,720,0s240-40,240-40V100z"></path>
                    </svg>
                </a>
                <div class="p-6">
                    <span class="inline-block bg-indigo-800 text-white text-xs font-bold uppercase px-3 py-1 rounded">Artikel</span>
                    <h2 class="mt-4 text-xl font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                        <a href="#">Mengapa Sertifikat Bahasa Inggris Penting untuk Masa Depan Anda</a>
                    </h2>
                    <p class="mt-2 text-base text-slate-600">
                        Bukan hanya untuk melamar pekerjaan, sertifikat bahasa Inggris juga bisa menjadi bukti kemampuan yang diakui secara internasional. Cari tahu manfaat dan jenis sertifikat yang bisa Anda dapatkan.
                    </p>
                </div>
            </article>

        </div>
    </div>
</div>
    </div>

</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.getElementById("myCarousel");
    const slides = carousel.querySelectorAll(".carousel-item");
    let index = 0;

    setInterval(() => {
      index = (index + 1) % slides.length;
      carousel.scrollTo({
        left: slides[index].offsetLeft,
        behavior: "smooth"
      });
    }, 3000); // 3 detik
  });

  document.addEventListener("DOMContentLoaded", () => {
    const track = document.getElementById("cardTrack");
    const cards = track.children.length;
    const visible = 3; // tampil 3 card
    let index = 0;

    setInterval(() => {
      index++;
      if (index > cards - visible) index = 0;
      track.style.transform = `translateX(-${index * (100 / visible)}%)`;
    }, 3000); // 3 detik
  });
</script>