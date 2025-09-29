<div>
    <section class="py-12 md:py-20">
        <div class="container mx-auto px-4">

            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800">Tim Pengajar Profesional Kami</h2>
                <p class="mt-4 text-lg text-gray-600">Dipandu oleh para ahli yang berdedikasi dan berpengalaman.</p>
                
                <div class="w-24 h-1 bg-sky-500 mx-auto mt-4 rounded"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach ($teachers as $teacher)
                <div class="bg-white rounded-lg shadow-lg p-6 text-center transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-48 h-48 rounded-full mx-auto mb-4 object-cover border-4 border-sky-200" 
                         src="{{ $teacher->photo ? Storage::url($teacher->photo) : asset('images/default-avatar.png') }}"
    alt="Foto {{ $teacher->name }}">
                    
                    <h3 class="text-xl font-bold text-gray-800">{{ $teacher->name }}</h3>
                    
                    @if ($teacher->position)
                        <p class="text-sky-600 font-medium mb-4">{{ $teacher->position }}</p>
                    @endif

                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                             <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.389 0-1.601 1.086-1.601 2.206v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.225-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-3.096 0 1.548 1.548 0 013.096 0zM6.55 8.014v8.59H3.455v-8.59H6.55zM18 1H6a5 5 0 00-5 5v12a5 5 0 005 5h12a5 5 0 005-5V6a5 5 0 00-5-5z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                    
                </div>
            @endforeach

                {{-- <div class="bg-white rounded-lg shadow-lg p-6 text-center transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-sky-200" src="https://img.daisyui.com/images/profile/demo/1.jpg" alt="Foto Jane Doe">
                    <h3 class="text-xl font-bold text-gray-800">Jane Doe</h3>
                    <p class="text-sky-600 font-medium mb-4">Lead Instructor & Founder</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                             <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.389 0-1.601 1.086-1.601 2.206v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.225-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-3.096 0 1.548 1.548 0 013.096 0zM6.55 8.014v8.59H3.455v-8.59H6.55zM18 1H6a5 5 0 00-5 5v12a5 5 0 005 5h12a5 5 0 005-5V6a5 5 0 00-5-5z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div> --}}

                {{-- <div class="bg-white rounded-lg shadow-lg p-6 text-center transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-sky-200" src="https://img.daisyui.com/images/profile/demo/2.jpg" alt="Foto John Smith">
                    <h3 class="text-xl font-bold text-gray-800">John Smith</h3>
                    <p class="text-sky-600 font-medium mb-4">Academic Coordinator</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                             <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.389 0-1.601 1.086-1.601 2.206v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.225-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-3.096 0 1.548 1.548 0 013.096 0zM6.55 8.014v8.59H3.455v-8.59H6.55zM18 1H6a5 5 0 00-5 5v12a5 5 0 005 5h12a5 5 0 005-5V6a5 5 0 00-5-5z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 text-center transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-sky-200" src="https://img.daisyui.com/images/profile/demo/3.jpg" alt="Foto Emily White">
                    <h3 class="text-xl font-bold text-gray-800">Emily White</h3>
                    <p class="text-sky-600 font-medium mb-4">English Tutor</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-sky-500 transition">
                             <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.389 0-1.601 1.086-1.601 2.206v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.225-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-3.096 0 1.548 1.548 0 013.096 0zM6.55 8.014v8.59H3.455v-8.59H6.55zM18 1H6a5 5 0 00-5 5v12a5 5 0 005 5h12a5 5 0 005-5V6a5 5 0 00-5-5z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div> --}}
            
            </div>
        </div>
    </section>
</div>

{{-- <div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Tim Pengajar Profesional Kami</h2>
            <p class="mt-4 text-lg text-gray-600">Kenali para pendidik berpengalaman kami.</p>
        </div>

        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($teachers as $teacher)
                <div class="bg-white rounded-lg shadow-lg p-6 text-center transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-48 h-48 rounded-full mx-auto mb-4 object-cover border-4 border-sky-200" 
                         src="{{ $teacher->photo ? Storage::url($teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&color=7F9CF5&background=EBF4FF' }}" 
                         alt="Foto {{ $teacher->name }}">
                    
                    <h3 class="text-xl font-bold text-gray-800">{{ $teacher->name }}</h3>
                    
                    @if ($teacher->position)
                        <p class="text-sky-600 font-medium mb-4">{{ $teacher->position }}</p>
                    @endif

                    
                </div>
            @endforeach
        </div>
    </div>
</div> --}}
