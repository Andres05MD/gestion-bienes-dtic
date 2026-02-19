@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div 
        x-data="{ 
            show: true,
            type: '{{ session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : 'success')) }}',
            message: @js(session('error') ?? session('warning') ?? session('info') ?? session('success')),
            init() {
                setTimeout(() => {
                    this.show = false;
                }, 5000);
            }
        }"
        x-show="show"
        x-transition:enter="transitiom ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
        x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
        x-transition:leave-end="opacity-0 translate-y-2 translate-x-2"
        @click="show = false"
        class="fixed bottom-5 right-5 z-50 flex items-center w-full max-w-xs p-4 space-x-3 text-gray-500 bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl border border-gray-100 dark:border-[#333] cursor-pointer"
        role="alert"
    >
        <!-- Icon Based on Type -->
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
             :class="{
                 'text-green-500 bg-green-100 dark:bg-green-800/30 dark:text-green-400': type === 'success',
                 'text-red-500 bg-red-100 dark:bg-red-800/30 dark:text-red-400': type === 'error',
                 'text-orange-500 bg-orange-100 dark:bg-orange-800/30 dark:text-orange-400': type === 'warning',
                 'text-blue-500 bg-blue-100 dark:bg-blue-800/30 dark:text-blue-400': type === 'info'
             }">
            
            <!-- Success Icon -->
            <svg x-show="type === 'success'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>

            <!-- Error Icon -->
            <svg x-show="type === 'error'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
            </svg>

             <!-- Warning Icon -->
             <svg x-show="type === 'warning'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
            </svg>

             <!-- Info Icon -->
             <svg x-show="type === 'info'" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
        </div>
        
        <div class="px-2 text-sm font-normal" x-text="message"></div>
        
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-[#1a1a1a] dark:hover:bg-gray-700" @click="show = false" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
@endif
