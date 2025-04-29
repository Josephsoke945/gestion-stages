<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
//import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    demandes: Object,
    error: {
        type: String,
        default: null
    }
});

// Fonction pour obtenir la couleur du badge selon le statut
const getStatusColor = (status) => {
    switch (status) {
        case 'En attente': return 'yellow';
        case 'Accepté': return 'green';
        case 'Refusé': return 'red';
        case 'En cours': return 'blue';
        case 'Terminé': return 'gray';
        default: return 'yellow';
    }
};

// Fonction pour traduire le statut
const translateStatus = (status) => {
    switch (status) {
        case 'en_attente': return 'En attente';
        case 'approuvee': return 'Accepté';
        case 'rejetee': return 'Refusé';
        case 'en_cours': return 'En cours';
        case 'terminee': return 'Terminé';
        default: return 'En attente';
    }
};
</script>

<template>
    <Head title="Liste des demandes" />

    <Admin>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Liste des demandes de stage
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Message d'erreur -->
                <div v-if="error" class="mb-6 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <font-awesome-icon icon="exclamation-circle" class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stagiaire
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Structure
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date de début
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date de fin
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="demande in demandes.data" :key="demande.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ demande.code || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ demande.stagiaire ? (demande.stagiaire.nom + ' ' + demande.stagiaire.prenom) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ demande.structure ? demande.structure.nom : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ demande.date_debut || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ demande.date_fin || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="{
                                                    'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                    'bg-yellow-100 text-yellow-800': ['en_attente', 'En attente'].includes(demande.statut),
                                                    'bg-green-100 text-green-800': ['approuvee', 'Accepté'].includes(demande.statut),
                                                    'bg-red-100 text-red-800': ['rejetee', 'Refusé'].includes(demande.statut),
                                                    'bg-blue-100 text-blue-800': ['en_cours', 'En cours'].includes(demande.statut),
                                                    'bg-gray-100 text-gray-800': ['terminee', 'Terminé'].includes(demande.statut)
                                                }">
                                                {{ translateStatus(demande.statut) }}
                                            </span>
                                        </td>
                                        
                                        
                                    </tr>
                                    <tr v-if="demandes.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Aucune demande trouvée
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="demandes.links && demandes.links.length > 3" class="mt-4">
                            <Pagination :links="demandes.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Admin>
</template>