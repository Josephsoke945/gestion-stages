<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nom: '',           // Ajout du champ nom
    prenom: '',        // Ajout du champ prenom
    telephone: '',     // Ajout du champ telephone
    adresse: '',       // Ajout du champ adresse
    email: '',
    password: '',
    password_confirmation: '',
});

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
                <InputLabel for="nom" value="Nom" />
                <TextInput
                    id="nom"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.nom"
                    required
                    autofocus
                    autocomplete="nom"
                />
                <InputError class="mt-2" :message="form.errors.nom" />
            </div>

            <div class="mt-4">
                <InputLabel for="prenom" value="Prénom" />
                <TextInput
                    id="prenom"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.prenom"
                    required
                    autocomplete="prenom"
                />
                <InputError class="mt-2" :message="form.errors.prenom" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4">
                <InputLabel for="telephone" value="Téléphone" />
                <TextInput
                    id="telephone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.telephone"
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.telephone" />
            </div>

            <div class="mt-4">
                <InputLabel for="adresse" value="Adresse" />
                <TextInput
                    id="adresse"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.adresse"
                    autocomplete="address-level1"
                />
                <InputError class="mt-2" :message="form.errors.adresse" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
