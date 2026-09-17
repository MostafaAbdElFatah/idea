<x-layout>
    <x-form.container action="{{ route('login.store') }}" method="POST" title=" Welcome back"
        description="Sign in to continue to your account">

        <x-form.input label="Email" name="email" type="email" />

        <x-form.password />

        <x-form.checkbox name="remember" label="Remember me" description="Stay signed in on this device" />

        <button type="submit" class="btn mt-2 h-10 w-full">
            Sign In
        </button>
        <p class="mt-6 text-center text-sm text-base-content/60">
            New to Idea?
            <a href="/register" class="font-semibold text-primary hover:underline">Create an account</a>
        </p>
    </x-form.container>
</x-layout>