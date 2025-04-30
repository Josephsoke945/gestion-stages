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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Demandes en attente -->
                    <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl border border-gray-100">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-yellow-100 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Demandes en attente</h3>
                                            <div class="flex items-baseline">
                                                <p class="text-3xl font-bold text-gray-900">{{ stats.demandesEnAttente }}</p>
                                                <p class="ml-2 text-sm text-gray-600">demandes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 px-6 py-3">
                            <div class="text-sm text-yellow-600">
                                Nécessitent une affectation
                            </div>
                        </div>
                    </div>

                    <!-- Demandes traitées -->
                    <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl border border-gray-100">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-green-100 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Demandes traitées</h3>
                                            <div class="flex items-baseline">
                                                <p class="text-3xl font-bold text-gray-900">{{ stats.demandesTraitees }}</p>
                                                <p class="ml-2 text-sm text-gray-600">demandes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 px-6 py-3">
                            <div class="text-sm text-green-600">
                                Traitées avec succès
                            </div>
                        </div>
                    </div>

                    <!-- Demandes affectées -->
                    <div class="bg-white overflow-hidden rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl border border-gray-100">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-blue-100 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Demandes affectées</h3>
                                            <div class="flex items-baseline">
                                                <p class="text-3xl font-bold text-gray-900">{{ stats.demandesAffectees }}</p>
                                                <p class="ml-2 text-sm text-gray-600">demandes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 px-6 py-3">
                            <div class="text-sm text-blue-600">
                                En cours de traitement
                            </div>
                        </div>
                    </div>
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