<div class="mt-6">
    <form method="GET" action="/ideas">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

            {{-- Status Filter --}}
            <div class="w-full sm:w-[calc(50%-12px)]"> <label for="status"
                    class="mb-2 block text-xs font-semibold uppercase tracking-widest text-gray-400">
                    Filter by status
                </label>

                <div class="relative">
                    <select id="status" name="status" onchange="this.form.submit()" class="w-full appearance-none rounded-xl border border-white/10
                               bg-white/4 px-4 py-3 pr-10 text-sm font-medium
                               text-white shadow-sm outline-none backdrop-blur-sm
                               transition-all duration-200
                               hover:border-white/20 hover:bg-white/[0.07]
                               focus:border-white/30 focus:bg-white/8
                               focus:ring-2 focus:ring-white/10">
                        <option value="" class="bg-gray-950 text-white">
                            All statuses
                        </option>

                        @foreach (\App\Enums\IdeaStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(strtolower(trim(request('status')))===$status->
                            value)
                            class="bg-gray-950 text-white sm:justify-between"
                            >
                            <div class="flex items-center justify-between">
                                <span>{{ $status->label() }}</span>
                                <span class="text-xs text-gray-400">2</span>
                            </div>
                        </option>
                        @endforeach
                    </select>

                    {{-- Chevron --}}
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Active Filter --}}
            @if (request('status'))
            <a href="/ideas" class="group inline-flex h-11 items-center gap-2 rounded-xl
                           border border-white/10 bg-white/4 px-4
                           text-sm font-medium text-gray-400
                           transition-all duration-200
                           hover:border-red-400/20 hover:bg-red-400/10
                           hover:text-red-300 mx-2">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-90" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path
                        d="M5.23 5.23a.75.75 0 011.06 0L10 8.94l3.71-3.71a.75.75 0 111.06 1.06L11.06 10l3.71 3.71a.75.75 0 11-1.06 1.06L10 11.06l-3.71 3.71a.75.75 0 01-1.06-1.06L8.94 10 5.23 6.29a.75.75 0 010-1.06z" />
                </svg>

                Clear filter
            </a>
            @endif

        </div>
    </form>
</div>