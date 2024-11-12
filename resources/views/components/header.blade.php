<header class="bg-white shadow-sm border-b">
    <div class="flex justify-between items-center px-8 py-4">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
            <div class="text-primary font-bold text-xl">{{ config('app.name', 'Laravel') }}</div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center space-x-6">
            <!-- Notifications -->
            <button class="text-gray-500 hover:text-primary relative">
                <span
                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-4 h-4 flex items-center justify-center">
                    3
                </span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </button>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-3">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"
                        alt="Profile">
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2"
                    style="display: none;">
                    <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
