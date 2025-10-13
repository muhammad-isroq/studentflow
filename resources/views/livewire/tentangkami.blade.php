<div class="">
    <div
    class="hero min-h-screen"
    style="background-image: url({{ asset('images/1.jpg') }});"
    >
    <div class="hero-overlay"></div>
        <div class="hero-content text-neutral-content text-center">
            <div class="max-w-md">
            <h1 class="mb-5 text-4xl font-bold">The Master of Dumai</h1>
            <p>Since 2010</p>
            </div>
        </div>
    </div>

    <div class="w-full py-12">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="rounded-lg ">
                <p class="text-3xl font-bold p-6 text-left md:text-center">Tentang The Master of Dumai</p>
                <p class="indent-8 p-6">Tepatnya pada awal Maret 2010, kami mengadakan survei ke kota
Dumai untuk melihat potensi dan perkembangan kota Dumai kedepan.
Kami menemukan bahwa benar kota Dumai mempunyai potensi besar
untuk menjadi sebuah kota yang besar, ramai, modern dan maju.
Mengingat letaknya yang berdekatan dengan negara Malaysia dan
Singapura, dekat dengan selat Malaka yang merupakan jalur pelayaran
terpadat di dunia dan terdapat pelabuhan internasional, serta memiliki
sumber daya alamnya yang kaya, kota Dumai mempunyai potensi untuk
berkembang. Keinginan pemerintah pusat maupun daerah serta
masyarakat yang kuat untuk menjadikan Dumai sebagai salah satu pusat
industri dan perdagangan akan membuat kota Dumai menjadi kota
tujuan oleh banyak orang dari berbagai daerah dan negara.</p>
                <p class="indent-8 p-6">Maka dalam rangka mempersiapkan SDM (Sumber Daya
Manusia) di kota Dumai kami merasa terpanggil untuk
ambil bagian demi kemajuan kota ini dan masyarakatnya.
Adapun yang bisa kami lakukan adalah di bidang
keterampilan berbahasa Inggris. Kami percaya ini adalah
salah satu kunci keberhasilan masyarakat Dumai
kedepan. Dengan menguasai Bahasa Inggris maka
masyarakat di kota ini lebih siap bersaing di masa
mendatang. Karena sudah menjadi kenyataan bahwa
banyak terjadi kelemahan SDM dibidang kemampuan
berbahasa Inggris, kami tidak mau melihat terjadi di kota
ini. Maka dengan keyakinan dan kepercayaan kami
membuka lembaga ini di Dumai. Adapun nama yang kami
pilih THE MASTER OF DUMAI (tempat lursusnya orangorang Dumai). Dengan hadirnya lembaga kursus Bahasa
Inggris ini kiranya dapat menciptakan master-master
Bahasa Inggris di kota Dumai.</p>
                <div class="flex justify-center p-6">
                    <a href="" class="rounded bg-sky-600 p-3 text-white transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                        Download Company Profile
                    </a>
                </div>
            </div>
            <div class="rounded-lg p-6">
                <img src="{{ asset('images/4.jpg') }}" alt="" class="rounded border shadow-xl">
            </div>
        </div>
       <div class="max-w-7xl mx-auto py-12 px-4 text-center">
        <h2 class="text-4xl font-bold text-gray-800">Our Gallery</h2>
        <div class="w-20 h-1 bg-gray-800 mx-auto mt-2 mb-8 rounded"></div>

        <div id="slider-wrapper" class="relative overflow-hidden">
            <div id="slider" class="flex transition-transform duration-500 ease-in-out">
                
                <div class="w-full flex-shrink-0">
                    <img src="{{ asset('images/1.jpg') }}" alt="Image 1" class="w-full h-96 object-cover">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="{{ asset('images/2.jpg') }}" alt="Image 2" class="w-full h-96 object-cover">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="{{ asset('images/3.jpg') }}" alt="Image 3" class="w-full h-96 object-cover">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="{{ asset('images/5.jpg') }}" alt="Image 4" class="w-full h-96 object-cover">
                </div>

            </div>
            
            <button id="prev" class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/75 transition">
                &#10094;
            </button>
            <button id="next" class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/75 transition">
                &#10095;
            </button>
        </div>
        
        <div id="dots-container" class="flex justify-center mt-4 space-x-2">
            </div>
    </div>
    </div>
</div>


    <script>
        const slider = document.getElementById('slider');
        const prevButton = document.getElementById('prev');
        const nextButton = document.getElementById('next');
        const dotsContainer = document.getElementById('dots-container');
        
        const slides = slider.children;
        const totalSlides = slides.length;
        let currentIndex = 0;
        let slideInterval;

        
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.classList.add('w-3', 'h-3', 'bg-gray-400', 'rounded-full', 'transition', 'hover:bg-gray-600');
            dot.addEventListener('click', () => {
                goToSlide(i);
            });
            dotsContainer.appendChild(dot);
        }

        const dots = dotsContainer.children;

        
        function updateSlider() {
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            
            
            for (let i = 0; i < totalSlides; i++) {
                dots[i].classList.remove('bg-gray-800');
                dots[i].classList.add('bg-gray-400');
            }
            dots[currentIndex].classList.remove('bg-gray-400');
            dots[currentIndex].classList.add('bg-gray-800');
        }

        function goToSlide(index) {
            currentIndex = index;
            if (currentIndex < 0) {
                currentIndex = totalSlides - 1;
            } else if (currentIndex >= totalSlides) {
                currentIndex = 0;
            }
            updateSlider();
            resetInterval();
        }

        
        prevButton.addEventListener('click', () => {
            goToSlide(currentIndex - 1);
        });

        nextButton.addEventListener('click', () => {
            goToSlide(currentIndex + 1);
        });

        
        function startInterval() {
            slideInterval = setInterval(() => {
                goToSlide(currentIndex + 1);
            }, 4000); 
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        
        updateSlider();
        startInterval();
    </script> 
