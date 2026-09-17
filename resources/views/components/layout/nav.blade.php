<nav class="border-b border-border px-6 py-2">
    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Idea logo" width="120">
            </a>
        </div>

        <div class="flex items-center gap-2">
            @auth

            <div x-data="{ open: false, logoutDialogOpen: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
                <button
                    type="button"
                    @click="open = ! open"
                    :aria-expanded="open"
                    aria-haspopup="menu"
                    aria-label="Account menu"
                    class="group flex items-center gap-2.5 rounded-full border border-border py-1 pl-1 pr-3 transition hover:bg-card"
                    :class="open && 'bg-card'"
                >
                    <x-user.avatar :user="auth()->user()" />
                    <span class="hidden text-sm font-medium sm:inline">{{ auth()->user()->first_name }}</span>
                    <svg class="h-4 w-4 text-muted-foreground transition-transform duration-200" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    role="menu"
                    class="absolute right-0 z-50 mt-2 w-64 origin-top-right overflow-hidden rounded-xl border border-border bg-card shadow-2xl"
                >
                    <div class="flex items-center gap-3 border-b border-border px-4 py-3">
                        <x-user.avatar :user="auth()->user()" size="h-10 w-10 text-sm" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ auth()->user()->fullName }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <div class="p-1">
                        <a href="{{ route('profile.show') }}" role="menuitem" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-muted-foreground transition hover:bg-white/5 hover:text-foreground">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                            </svg>
                            Profile
                        </a>

                        <a href="{{ route('profile.show') }}#settings" role="menuitem" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-muted-foreground transition hover:bg-white/5 hover:text-foreground">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" />
                            </svg>
                            Settings
                        </a>
                    </div>

                    <div class="border-t border-border p-1">
                        <button type="button" role="menuitem" @click="open = false; logoutDialogOpen = true" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-error/90 transition hover:bg-error/10 hover:text-error">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                            </svg>
                            Log out
                        </button>
                    </div>
                </div>

                <x-dialog.confirm-dialog
                    title="Log out?"
                    message="You'll need to sign in again to access your ideas."
                    confirmLabel="Log out"
                    confirmClass="bg-error text-white hover:brightness-110"
                    method="DELETE"
                    :action="route('logout')"
                    state="logoutDialogOpen"
                />
            </div>

            @else
            <a href="/login" class="btn btn-outlined">
                Login
            </a>

            <a href="/register" class="btn">
                Get Started
            </a>

            @endauth

        </div>
    </div>
</nav>