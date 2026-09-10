<x-layout>
    <x-form action="{{ route('register.store') }}" method="POST" title="Register an account"
        description="Start tracking your ideas today">


        <x-form.input name="name" label="Name" />

        <x-form.input label="Email" name="email" type="email" />


        <x-form.password name="password" label="Password" autocomplete="new-password" :show-strength="true" />
        <x-form.password name="password_confirmation" label="Confirm password" autocomplete="new-password" />
            
        <button type="submit" class="btn mt-2 h-10 w-full">
            create Account
        </button>
    </x-form>
</x-layout>