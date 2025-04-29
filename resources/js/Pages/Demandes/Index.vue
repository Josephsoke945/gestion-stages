<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Stagiaire from '@/Layouts/Stagiaire.vue';

const page = usePage();
const demandes = ref(page.props.demandes);

// Fonction pour annuler une demande
const annulerDemande = async (id) => {
    if (confirm('Êtes-vous sûr de vouloir annuler cette demande ?')) {
        try {
            await axios.delete(route('mes.demandes.annuler', id));
            fetchDemandes(); // Rafraîchir les demandes après l'annulation
        } catch (error) {
            console.error('Erreur lors de l’annulation :', error);
        }
    }
};

// Fonction pour récupérer les demandes
const fetchDemandes = async () => {
    try {
        const response = await axios.get(route('mes.demandes'));
        demandes.value = response.data;
    } catch (error) {
        console.error('Erreur lors de la récupération des demandes :', error);
    }
};
</script>

<template>
    <Stagiaire>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-md sm:rounded-lg p-6">
                    <!-- Titre -->
                    <h1 class="text-3xl font-bold mb-6 flex items-center gap-2 text-gray-800">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        Mes Demandes
                    </h1>

                    <!-- Table responsive -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm text-gray-600 uppercase tracking-wider">
                                    <th class="border p-3">Structure</th>
                                    <th class="border p-3">Nature</th>
                                    <th class="border p-3">Statut</th>
                                    <th class="border p-3">Date de Soumission</th>
                                    <th class="border p-3">Code de Suivi</th>
                                    <th class="border p-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="demande in demandes"
                                    :key="demande.id"
                                    class="hover:bg-gray-50 transition-all duration-300 transform hover:scale-[1.01] border-b"
                                >
                                    <td class="p-4 text-gray-800 font-medium">{{ demande.structure.libelle }}</td>
                                    <td class="p-4 text-gray-700">{{ demande.nature }}</td>
                                    <td class="p-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': demande.statut === null || demande.statut === 'En attente',
                                                'bg-green-100 text-green-800': demande.statut === 'Acceptée',
                                                'bg-red-100 text-red-800': demande.statut === 'Refusée',
                                            }"
                                        >
                                            {{ demande.statut || 'En attente' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-600">
                                        {{ new Date(demande.created_at).toLocaleDateString() }}
                                    </td>
                                    <!-- Affichage du code de suivi -->
                                    <td class="p-4 text-gray-800 font-mono">
                                        {{ demande.code_suivi || 'Non attribué' }}
                                    </td>
                                    <td class="p-4">
                                        <button
                                            v-if="!demande.statut || demande.statut === 'En attente'"
                                            @click="annulerDemande(demande.id)"
                                            class="flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Annuler
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="demandes.length === 0">
                                    <td colspan="6" class="text-center py-6 text-gray-500">
                                        Aucune demande soumise pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Stagiaire>
</template>
