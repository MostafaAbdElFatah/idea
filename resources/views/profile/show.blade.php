<x-layout>
    <div class="mx-auto max-w-4xl py-10">
        {{-- Hero --}}
        <section x-data class="relative overflow-hidden rounded-2xl border border-border bg-card">
            <div class="group relative h-40 overflow-hidden border-b border-border bg-white/2 sm:h-52">
                @if ($user->bannerImageUrl)
                <img src="{{ $user->bannerImageUrl }}" alt="Profile cover" class="h-full w-full object-cover">
                <div class="pointer-events-none absolute inset-0 bg-black/10"></div>
                @else
                <div class="absolute inset-0 bg-[repeating-linear-gradient(135deg,transparent_0_14px,rgb(255_255_255/0.025)_14px_15px)]" aria-hidden="true"></div>
                <div class="absolute inset-0 flex items-center justify-center text-muted-foreground/40" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="m21 15-5-5L5 21" />
                    </svg>
                </div>
                @endif

                {{-- Cover controls --}}
                <div class="absolute right-3 top-3 flex items-center gap-2">
                    <x-form id="update-banner" :action="route('profile.banner.update')" method="POST" enctype="multipart/form-data">
                        <label for="banner_image" class="flex cursor-pointer items-center gap-1.5 rounded-full bg-black/60 px-3 py-1.5 text-xs font-medium text-white ring-1 ring-white/15 backdrop-blur-md transition hover:bg-black/80">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 8h3l2-3h6l2 3h3v11H4z" />
                                <circle cx="12" cy="13" r="3.5" />
                            </svg>
                            {{ $user->bannerImageUrl ? 'Change cover' : 'Add cover' }}
                        </label>
                        <input id="banner_image" name="banner_image" type="file" accept="image/*" class="sr-only" @change="$el.form.requestSubmit()">
                    </x-form>

                    @if ($user->bannerImageUrl)
                    <x-form :action="route('profile.banner.destroy')" method="DELETE">
                        <button type="submit" class="rounded-full bg-black/60 px-3 py-1.5 text-xs font-medium text-white ring-1 ring-white/15 backdrop-blur-md transition hover:bg-error" aria-label="Remove cover">
                            Remove
                        </button>
                    </x-form>
                    @endif
                </div>
            </div>

            <div class="relative px-6 pb-6 sm:px-10">
                <x-form.error name="banner_image" bag="updateBanner" />

                <div class="-mt-14 flex flex-col items-center gap-4 sm:-mt-16 sm:flex-row sm:items-end sm:gap-6">
                    <x-user.avatar :user="$user" size="h-28 w-28 text-3xl sm:h-32 sm:w-32" class="border-4 border-card" />

                    <div class="min-w-0 flex-1 text-center sm:pb-2 sm:text-left">
                        <h1 class="truncate text-3xl font-bold tracking-tight">{{ $user->fullName }}</h1>
                        <p class="mt-1 truncate text-sm text-muted-foreground">{{ $user->email }}</p>
                    </div>

                    <span class="inline-flex items-center gap-2 rounded-full border border-border bg-background/60 px-3 py-1.5 text-xs text-muted-foreground sm:mb-3">
                        <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                        Member since {{ $user->created_at->format('M Y') }}
                    </span>
                </div>

                {{-- Stats --}}
                <dl class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ([
                        ['label' => 'Ideas', 'value' => $stats['ideas'], 'dot' => 'bg-primary'],
                        ['label' => 'Completed', 'value' => $stats['completed'], 'dot' => 'bg-success'],
                        ['label' => 'In progress', 'value' => $stats['inProgress'], 'dot' => 'bg-info'],
                        ['label' => 'Steps done', 'value' => $stats['stepsDone'], 'dot' => 'bg-warning'],
                    ] as $stat)
                    <div class="rounded-xl border border-border bg-background/40 p-4 transition hover:-translate-y-0.5 hover:border-white/15">
                        <dt class="flex items-center gap-2 text-xs font-medium uppercase tracking-widest text-muted-foreground">
                            <span class="h-1.5 w-1.5 rounded-full {{ $stat['dot'] }}"></span>
                            {{ $stat['label'] }}
                        </dt>
                        <dd class="mt-2 text-3xl font-bold tabular-nums">{{ $stat['value'] }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- Settings --}}
        <div id="settings" x-data class="mt-8 grid scroll-mt-8 gap-6 lg:grid-cols-[260px_1fr]">
            <x-form id="update-profile" :action="route('profile.update')" method="PATCH" class="contents">
                <aside class="rounded-2xl border border-border bg-card p-6 lg:row-span-3 lg:self-start">
                    <h2 class="text-sm font-semibold">Profile photo</h2>
                    <p class="mt-1 text-xs text-muted-foreground">This is how you appear across Idea.</p>

                    <x-user.avatar-picker
                        class="mt-6"
                        :current-url="$user->profileImageUrl"
                        :initials="$user->initials"
                        :remove-form="$user->profileImageUrl ? 'remove-profile-image' : null"
                    />
                </aside>

                <section class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="text-sm font-semibold">Personal information</h2>
                    <p class="mt-1 text-xs text-muted-foreground">Update your name. Your email is changed separately.</p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <x-form.input name="first_name" label="First name" :value="$user->first_name" autocomplete="given-name" />
                        <x-form.input name="last_name" label="Last name" :value="$user->last_name" autocomplete="family-name" />

                        <div class="flex flex-col items-start space-y-2 sm:col-span-2">
                            <label for="current_email" class="label">Email</label>
                            <div class="flex w-full gap-3">
                                <input id="current_email" type="email" value="{{ $user->email }}" disabled class="input min-w-0 flex-1 cursor-not-allowed opacity-60">
                                <button type="button" class="btn btn-outlined shrink-0" @click="$dispatch('open-model', 'change-email')">
                                    Change email
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn h-11 px-8">Save changes</button>
                    </div>
                </section>
            </x-form>

            <section class="flex flex-col gap-4 rounded-2xl border border-border bg-card p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold">Password</h2>
                    <p class="mt-1 text-xs text-muted-foreground">Use a strong password you don't use anywhere else.</p>
                </div>
                <button type="button" class="btn btn-outlined shrink-0" @click="$dispatch('open-model', 'change-password')">
                    Change password
                </button>
            </section>

            <section class="flex flex-col gap-4 rounded-2xl border border-error/40 bg-error/5 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-error">Delete account</h2>
                    <p class="mt-1 text-xs text-muted-foreground">Permanently delete your account, ideas, steps, and images. This cannot be undone.</p>
                </div>
                <button type="button" class="btn shrink-0 bg-error text-white hover:brightness-110" @click="$dispatch('open-model', 'delete-account')">
                    Delete account
                </button>
            </section>
        </div>

        @if ($user->profileImageUrl)
        <x-form id="remove-profile-image" :action="route('profile.image.destroy')" method="DELETE" class="hidden" />
        @endif
    </div>

    {{-- Change email --}}
    <x-dialog name="change-email" title="Change email">
        <x-form :action="route('profile.email.update')" method="PUT" class="space-y-5 p-1">
            <p class="text-sm text-muted-foreground">Your current email is <span class="font-medium text-foreground">{{ $user->email }}</span>.</p>

            <x-form.input name="email" label="New email" type="email" autocomplete="email" bag="updateEmail" />
            <x-form.password name="email_password" label="Current password" placeholder="Confirm it's you" bag="updateEmail" />

            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-outlined" @click="show = false">Cancel</button>
                <button type="submit" class="btn">Change email</button>
            </div>
        </x-form>
    </x-dialog>

    {{-- Change password --}}
    <x-dialog name="change-password" title="Change password">
        <x-form :action="route('profile.password.update')" method="PUT" class="space-y-5 p-1">
            <x-form.password name="current_password" label="Current password" placeholder="Your current password" bag="updatePassword" />
            <x-form.password name="password" label="New password" autocomplete="new-password" :show-strength="true" bag="updatePassword" />
            <x-form.password name="password_confirmation" label="Confirm new password" autocomplete="new-password" bag="updatePassword" />

            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-outlined" @click="show = false">Cancel</button>
                <button type="submit" class="btn">Change password</button>
            </div>
        </x-form>
    </x-dialog>

    {{-- Delete account --}}
    <x-dialog name="delete-account" title="Delete account">
        <x-form :action="route('profile.destroy')" method="DELETE" class="space-y-5 p-1">
            <div class="rounded-lg border border-error/40 bg-error/10 p-4 text-sm text-foreground">
                This permanently deletes your account, all {{ $stats['ideas'] }} {{ Str::plural('idea', $stats['ideas']) }}, their steps, and every image you uploaded.
            </div>

            <x-form.password name="delete_password" label="Password" placeholder="Enter your password to confirm" bag="deleteAccount" />

            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-outlined" @click="show = false">Cancel</button>
                <button type="submit" class="btn bg-error text-white hover:brightness-110">Delete my account</button>
            </div>
        </x-form>
    </x-dialog>
</x-layout>
