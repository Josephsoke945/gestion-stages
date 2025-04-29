<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AgentDPAF from '@/Layouts/AgentDPAF.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    demandes: Object,
    filters: Object,
    structures: Array
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const structure_id = ref(props.filters?.structure_id || '');

// Debounced search function
const debouncedSearch = debounce(() => {
    router.get(route('agent.demandes'), { 
        search: search.value,
        status: status.value,
        structure_id: structure_id.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
}, 300);

// Watch for changes in filters
watch(search, debouncedSearch);
watch(status, debouncedSearch);
watch(structure_id, debouncedSearch);

const statusOptions = [
    { value: '', label: 'Tous les statuts' },
    { value: 'En attente', label: 'En attente' },
    { value: 'Approuvée', label: 'Approuvée' },
    { value: 'Refusée', label: 'Refusée' }
];

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};
</script>

<template>
    <AgentDPAF>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Demandes de stage
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filtres -->
                        <div class="mb-6 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Rechercher un stagiaire..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                            </div>
                            <div class="w-full sm:w-48">
                                <select
                                    v-model="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="w-full sm:w-64">
                                <select
                                    v-model="structure_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                    <option value="">Toutes les structures</option>
                                    <option v-for="structure in structures" :key="structure.id" :value="structure.id">
                                        {{ structure.libelle }}
                                    </option>
                                </select>
                                <div v-if="!structures || structures.length === 0" class="text-sm text-gray-500 mt-1">
                                    Chargement des structures...
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stagiaire</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de soumission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="demande in demandes.data" :key="demande.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ demande.stagiaire.user.nom }} {{ demande.stagiaire.user.prenom }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ demande.stagiaire.user.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(demande.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ demande.structure ? demande.structure.libelle : 'Non spécifiée' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                'bg-yellow-100 text-yellow-800': demande.statut === 'En attente',
                                                'bg-green-100 text-green-800': demande.statut === 'Approuvée',
                                                'bg-red-100 text-red-800': demande.statut === 'Refusée'
                                            }">
                                                {{ demande.statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-3">
                                                <Link :href="route('agent.demandes.show', demande.id)" 
                                                    class="text-blue-600 hover:text-blue-900 font-medium">
                                                    Voir détails
                                                </Link>
                                                <button v-if="demande.statut === 'En attente'"
                                                    class="text-green-600 hover:text-green-900 font-medium">
                                                    Affecter
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Message si aucune demande -->
                                    <tr v-if="demandes.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Aucune demande trouvée
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="demandes.links && demandes.links.length > 3" class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Affichage de {{ demandes.from }} à {{ demandes.to }} sur {{ demandes.total }} résultats
                            </div>
                            <div class="flex gap-1">
                                <template v-for="(link, i) in demandes.links" :key="i">
                                    <Link v-if="link.url"
                                        :href="link.url"
                                        class="px-3 py-1 rounded"
                                        :class="{
                                            'bg-blue-600 text-white': link.active,
                                            'text-gray-700 hover:bg-gray-100': !link.active
                                        }"
                                    >
                                        <span v-if="link.label === '&laquo; Previous'">← Précédent</span>
                                        <span v-else-if="link.label === 'Next &raquo;'">Suivant →</span>
                                        <span v-else>{{ link.label }}</span>
                                    </Link>
                                    <span v-else
                                        class="px-3 py-1 rounded text-gray-400 cursor-not-allowed"
                                    >
                                        <span v-if="link.label === '&laquo; Previous'">← Précédent</span>
                                        <span v-else-if="link.label === 'Next &raquo;'">Suivant →</span>
                                        <span v-else>{{ link.label }}</span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AgentDPAF>
</template> 