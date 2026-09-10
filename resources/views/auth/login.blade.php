<x-layout>
    <x-form action="{{ route('register.store') }}" method="POST" title="Register an account"
        description="Start tracking your ideas today">

        <x-form.input label="Email" name="email" type="email" />

        <x-form.password />

        <button type="submit" class="btn mt-2 h-10 w-full">
            create Account
        </button>
        <p class="mt-6 text-center text-sm text-base-content/60">
            New to Idea?
            <a href="/register" class="font-semibold text-primary hover:underline">Create an account</a>
        </p>
    </x-form>
</x-layout>