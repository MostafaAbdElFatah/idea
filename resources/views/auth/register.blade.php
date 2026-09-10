<x-layout>
    <x-form action="{{ route('register.store') }}" method="POST" title="Register an account"
        description="Start tracking your ideas today">

        <div class="grid gap-5 sm:grid-cols-2">
            <x-form.input name="first_name" label="First name" autocomplete="given-name" />
            <x-form.input name="last_name" label="Last name" autocomplete="family-name" />
        </div>

        <x-form.input label="Email" name="email" type="email" />


        <x-form.password name="password" label="Password" autocomplete="new-password" :show-strength="true" />
        <x-form.password name="password_confirmation" label="Confirm password" autocomplete="new-password" />

        <button type="submit" class="btn mt-2 h-10 w-full">
            create Account
        </button>
    </x-form>
</x-layout>