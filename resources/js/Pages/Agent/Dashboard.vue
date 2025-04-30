<template>
    <AgentDPAF>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tableau de bord DPAF
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Message de débogage -->
                <!-- <div v-if="debug" class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4">
                    <pre>{{ debug }}</pre>
                </div> -->

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Demandes en attente -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Demandes en attente</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.demandesEnAttente }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Demandes traitées -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Demandes traitées</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.demandesTraitees }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Demandes affectées -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Demandes affectées</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.demandesAffectees }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Structures actives -->
                   <!--  <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Structures actives</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.structuresActives }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- Dernières demandes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Dernières demandes</h3>
                            <Link :href="route('agent.demandes')" class="text-blue-600 hover:text-blue-800">
                                Voir tout
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stagiaire</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de soumission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
<!--                                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
 -->                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="demande in dernieresDemandes" :key="demande.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ demande.stagiaire.user.nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(demande.date_soumission) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="demande.structure">{{ demande.structure.libelle }}</span>
                                            <span v-else class="text-gray-400 italic">Non assignée</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(demande.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ demande.statut }}
                                            </span>
                                        </td>
                                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <Link :href="route('agent.demandes.show', demande.id)" class="text-blue-600 hover:text-blue-900">
                                                Voir détails
                                            </Link>
                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </AgentDPAF>
</template>

<script setup>
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AgentDPAF from '@/Layouts/AgentDPAF.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    dernieresDemandes: {
        type: Array,
        required: true
    },
    structures: {
        type: Array,
        required: true
    },
    debug: {
        type: Object,
        required: false
    }
});

onMounted(() => {
    console.log('Agent Dashboard mounted', props.debug);
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const getStatusClass = (status) => {
    const classes = {
        'En attente': 'bg-yellow-100 text-yellow-800',
        'Approuvée': 'bg-green-100 text-green-800',
        'Refusée': 'bg-red-100 text-red-800',
        'En cours': 'bg-blue-100 text-blue-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script> 