<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
const dobYear = currentYear - 20;

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: '',
    dob: `${dobYear}-01-01`,
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' }
];

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="first-name" value="First Name" />

                <TextInput id="first-name" type="text" class="mt-1 block w-full" v-model="form.first_name" required
                    autofocus autocomplete="first-name" />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="last-name" value="Last Name" />

                <TextInput id="last-name" type="text" class="mt-1 block w-full" v-model="form.last_name" required
                    autocomplete="last-name" />

                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                    autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="d-o-b" value="Date of Birth" />

                <TextInput id="d-o-b" type="date" class="mt-1 block w-full" v-model="form.dob" required
                    autocomplete="d-o-b" />

                <InputError class="mt-2" :message="form.errors.dob" />
            </div>

            <div class="mt-4">
                <InputLabel for="gender" value="Gender" />

                <SelectInput id="gender" v-model="form.gender" :options="genderOptions" placeholder="Select gender"
                    required />

                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link :href="route('login')" class="auth-links underline">
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
