<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
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
const selectedDemandes = ref([]);

// Ajout des refs pour le modal d'affectation
const showAffectationModal = ref(false);
const selectedDemande = ref(null);
const selectedStructureId = ref('');

// Fonction pour vérifier si une demande a une structure spécifiée
const hasStructure = (demande) => {
    return demande.structure !== null && demande.structure !== undefined;
};

// Fonction pour gérer la sélection de toutes les demandes avec structure
const selectAllWithStructure = () => {
    const demandesWithStructure = props.demandes.data.filter(hasStructure);
    if (selectedDemandes.value.length === demandesWithStructure.length) {
        selectedDemandes.value = [];
    } else {
        selectedDemandes.value = demandesWithStructure.map(d => d.id);
    }
};

// Fonction pour vérifier si toutes les demandes avec structure sont sélectionnées
const allWithStructureSelected = computed(() => {
    const demandesWithStructure = props.demandes.data.filter(hasStructure);
    return demandesWithStructure.length > 0 && 
           demandesWithStructure.every(d => selectedDemandes.value.includes(d.id));
});

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
    { value: 'En cours', label: 'En cours' },
    { value: 'Approuvée', label: 'Approuvée' },
    { value: 'Refusée', label: 'Refusée' }
];

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

// Fonction pour ouvrir le modal d'affectation
const openAffectationModal = (demande = null) => {
    if (demande) {
        selectedDemande.value = demande;
        selectedStructureId.value = demande.structure_id || '';
    } else {
        selectedDemande.value = null;
        selectedStructureId.value = '';
    }
    showAffectationModal.value = true;
};

// Fonction pour fermer le modal d'affectation
const closeAffectationModal = () => {
    showAffectationModal.value = false;
    selectedDemande.value = null;
    selectedStructureId.value = '';
};

// Fonction pour l'affectation groupée directe
const submitDirectGroupAffectation = () => {
    if (selectedDemandes.value.length === 0) return;

    // Récupérer les demandes sélectionnées avec leurs structures
    const demandesWithStructures = props.demandes.data
        .filter(d => selectedDemandes.value.includes(d.id))
        .filter(d => d.structure);

    // Créer un tableau de promesses pour chaque affectation
    const promises = demandesWithStructures.map(demande => {
        return router.post(route('agent.demandes.affecter', demande.id), {
            structure_id: demande.structure.id
        });
    });

    // Exécuter toutes les promesses
    Promise.all(promises).then(() => {
        selectedDemandes.value = [];
    });
};

// Fonction pour soumettre l'affectation
const submitAffectation = () => {
    if (!selectedStructureId.value) return;

    if (selectedDemande.value) {
        // Affectation individuelle
        router.post(route('agent.demandes.affecter', selectedDemande.value.id), {
            structure_id: selectedStructureId.value
        }, {
            onSuccess: () => {
                closeAffectationModal();
            }
        });
    } else {
        // Affectation groupée
        submitDirectGroupAffectation();
    }
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

                        <!-- Actions pour les demandes sélectionnées -->
                        <div v-if="selectedDemandes.length > 0" class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">
                                    {{ selectedDemandes.length }} demande(s) sélectionnée(s)
                                </span>
                                <button 
                                    @click="submitDirectGroupAffectation"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                    </svg>
                                    Affecter
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input
                                                type="checkbox"
                                                :checked="allWithStructureSelected"
                                                @change="selectAllWithStructure"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stagiaire</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de soumission</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="demande in demandes.data" :key="demande.id" 
                                        :class="{'bg-gray-50': selectedDemandes.includes(demande.id)}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input
                                                v-if="hasStructure(demande)"
                                                type="checkbox"
                                                :value="demande.id"
                                                v-model="selectedDemandes"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                        </td>
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
                                                'bg-green-100 text-green-800': demande.statut === 'Acceptée',
                                                'bg-red-100 text-red-800': demande.statut === 'Refusée',
                                                'bg-blue-100 text-blue-800': demande.statut === 'En cours'
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
                                                    @click="openAffectationModal(demande)"
                                                    class="text-green-600 hover:text-green-900 font-medium flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                                    </svg>
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
                                        <span v-else-if="link.label === 'Next &raquo;'">Suivant  →</span>
                                        <span v-else>{{ link.label }}</span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal d'affectation -->
        <div v-if="showAffectationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ selectedDemande ? 'Affecter une structure' : `Affecter une structure (${selectedDemandes.length} demandes)` }}
                        </h3>
                        <button @click="closeAffectationModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Structure</label>
                        <select 
                            v-model="selectedStructureId"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Sélectionner une structure</option>
                            <option v-for="structure in structures" :key="structure.id" :value="structure.id">
                                {{ structure.libelle }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button 
                        @click="closeAffectationModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Annuler
                    </button>
                    <button 
                        @click="submitAffectation"
                        :disabled="!selectedStructureId"
                        :class="[
                            'px-4 py-2 rounded-lg transition-colors flex items-center gap-2',
                            selectedStructureId 
                                ? 'bg-blue-600 text-white hover:bg-blue-700' 
                                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Affecter
                    </button>
                </div>
            </div>
        </div>
    </AgentDPAF>
</template> 