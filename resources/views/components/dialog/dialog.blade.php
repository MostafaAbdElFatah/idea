@props(['name', 'title'])
<!-- modal -->
<div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/700 backdrop-blur-xs"
    x-data="{ 
        show: @js(session('open_modal') === $name), 
        name: @js($name) 
    }" 
    x-cloak 
    x-show="show" 
    x-effect="document.body.classList.toggle('overflow-hidden', show)"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4" 
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 -translate-y-4 -translate-x-4" 
    @open-model.window="show = $event.detail === name"
    @keydown.escape.window="show = false" 
    role="dialog" 
    aria-modal="true" 
    aria-labelledby="model-@js($name)-title"
    :aria-hidden="!show" 
    tabindex="-1">

    <x-layout.card 
        @click.outside="show = false" 
        class="shadow-wl max-w-2xl w-full max-h-[80dvh] flex flex-col overflow-hidden">
        <div class="flex shrink-0 justify-between">
            <h2 id="model-@js($name)-title" class="text-xl font-bold">{{ $title }}</h2>
            <button @click="show = false" aria-label="close button">
                <x-icons.close width=30 height=30 />
            </button>
        </div>
        <div class="mt-10 min-h-0 flex-1 overflow-y-auto dropdown-scrollbar overscroll-contain">
            {{ $slot }}
        </div>

    </x-layout.card>
</div>