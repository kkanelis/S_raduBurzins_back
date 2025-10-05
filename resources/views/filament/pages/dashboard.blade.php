<x-filament::page>
    <div class="space-y-8">

        {{-- 🎄 Sveiciens --}}
        <div class="relative overflow-hidden rounded-2xl shadow-xl p-8 bg-gradient-to-r from-purple-800 via-purple-700 to-indigo-800 text-white">
            <div class="absolute inset-0 bg-[url('/images/snow-pattern.svg')] opacity-10"></div>
            <div class="relative z-10">
                <h1 class="text-4xl font-extrabold tracking-tight">
                    Sveiks atpakaļ, {{ auth()->user()->first_name ?? 'Lietotāj' }}! 😎
                </h1>
                <p class="mt-3 text-purple-200 text-lg">
                    Lai tev mierīga un produktīva diena <span class="font-semibold">Radu Burziņš</span> administrācijā.
                </p>
            </div>
        </div>

        {{-- 📊 Statistika --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Lietotāji --}}
            <div class="group p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border border-transparent hover:border-purple-400">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Lietotāji</h2>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-1 transition group-hover:text-purple-600 dark:group-hover:text-purple-400">
                            {{ \App\Models\User::count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-full group-hover:scale-110 transition-transform">
                        <x-heroicon-o-user class="w-7 h-7 text-purple-700 dark:text-purple-300" />
                    </div>
                </div>
            </div>

            {{-- Piemēram: Notikumi --}}
            <div class="group p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border border-transparent hover:border-purple-400">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Notikumi</h2>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-1 transition group-hover:text-purple-600 dark:group-hover:text-purple-400">
                            {{ \App\Models\SpecialDay::count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-full group-hover:scale-110 transition-transform">
                        <x-heroicon-o-calendar class="w-7 h-7 text-purple-700 dark:text-purple-300" />
                    </div>
                </div>
            </div>

            {{-- Ziemassvētku loterija --}}
            <div class="group p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 border border-transparent hover:border-purple-400">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Ziemassvētku Loterija</h2>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-1 transition group-hover:text-purple-600 dark:group-hover:text-purple-400">
                            {{ \App\Models\ChristmasLottery::count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-full group-hover:scale-110 transition-transform">
                        <x-heroicon-o-gift class="w-7 h-7 text-purple-700 dark:text-purple-300" />
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-filament::page>

