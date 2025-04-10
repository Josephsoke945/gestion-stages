<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const submitRequest = () => {
    alert('Demande soumise avec succès !'); // Remplacez par une logique réelle
};
</script>

<template>
    <Head title="Tableau de bord - Stagiaire" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord - Stagiaire
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-bold mb-4">Bienvenue, {{ $page.props.auth.user.nom }}</h1>
                        <p class="mb-4">Ceci est votre tableau de bord en tant que stagiaire.</p>

                        <button
                            @click="submitRequest"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Soumettre une demande
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>