<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showModal = ref(false);

const form = useForm({
    stagiaire_id: '', // Récupéré dynamiquement
    structure_id: '',
    nature: 'Individuel',
    structure_souhaitee: '',
    lettre_cv_path: null,
});

const submitRequest = () => {
    form.post(route('demande_stages.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
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
                            @click="showModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Soumettre une demande
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale pour le formulaire -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-6 rounded shadow-lg w-1/2">
                <h2 class="text-xl font-bold mb-4">Soumettre une demande</h2>
                <form @submit.prevent="submitRequest">
                    <!-- Structure -->
                    <div class="mb-4">
                        <label for="structure_id" class="block text-sm font-medium text-gray-700">Structure</label>
                        <select v-model="form.structure_id" id="structure_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sélectionnez la structure souhaitée</option>
                            <option v-for="structure in $page.props.structures" :key="structure.id" :value="structure.id">
                                {{ structure.libelle }} <!-- Utilisation de 'libelle' -->
                            </option>
                        </select>
                    </div>

                    <!-- Nature -->
                    <div class="mb-4">
                        <label for="nature" class="block text-sm font-medium text-gray-700">Nature</label>
                        <select v-model="form.nature" id="nature" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="Individuel">Individuel</option>
                            <option value="Groupe">Groupe</option>
                        </select>
                    </div>

                    <!-- Structure souhaitée -->
                    <!-- <div class="mb-4">
                        <label for="structure_souhaitee" class="block text-sm font-medium text-gray-700">Structure souhaitée (ID)</label>
                        <input type="number" v-model="form.structure_souhaitee" id="structure_souhaitee" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    </div> -->

                    <!-- Lettre et CV -->
                    <div class="mb-4">
                        <label for="lettre_cv_path" class="block text-sm font-medium text-gray-700">Lettre et CV</label>
                        <input type="file" @change="form.lettre_cv_path = $event.target.files[0]" id="lettre_cv_path" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Soumettre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>