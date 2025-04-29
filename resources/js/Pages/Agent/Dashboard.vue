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

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-900 text-lg font-semibold mb-2">Demandes en attente</div>
                        <div class="text-3xl font-bold text-blue-600">{{ stats.demandesEnAttente }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-900 text-lg font-semibold mb-2">Demandes traitées</div>
                        <div class="text-3xl font-bold text-green-600">{{ stats.demandesTraitees }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-900 text-lg font-semibold mb-2">Structures actives</div>
                        <div class="text-3xl font-bold text-purple-600">{{ stats.structuresActives }}</div>
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