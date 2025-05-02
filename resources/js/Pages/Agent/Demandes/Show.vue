<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AgentDPAF from '@/Layouts/AgentDPAF.vue';
import { ref } from 'vue';
import AdminToast from '@/Components/AdminToast.vue';

const props = defineProps({
    demande: Object,
    structures: Array
});

const toast = ref(null);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const getStatusColor = (statut) => {
    switch (statut) {
        case 'En attente':
            return 'text-yellow-600 bg-yellow-100';
        case 'En cours':
            return 'text-blue-600 bg-blue-100';
        case 'Approuvée':
            return 'text-green-600 bg-green-100';
        case 'Refusée':
            return 'text-red-600 bg-red-100';
        default:
            return 'text-gray-600 bg-gray-100';
    }
};

const showAffectationModal = ref(false);
const selectedStructure = ref(props.demande.structure_id || '');

const openAffectationModal = () => {
    showAffectationModal.value = true;
};

const closeAffectationModal = () => {
    showAffectationModal.value = false;
};

const submitAffectation = () => {
    if (!selectedStructure.value) return;

    router.post(route('agent.demandes.affecter', props.demande.id), {
        structure_id: selectedStructure.value
    }, {
        onSuccess: () => {
            closeAffectationModal();
            if (toast.value) {
                toast.value.addToast({
                    type: 'success',
                    title: 'Affectation réussie',
                    message: `La demande '${props.demande.code_suivi}' a été affectée à la structure '${props.structures.find(s => s.id === selectedStructure.value)?.libelle}' avec succès.`
                });
            }
        }
    });
};

const submit = (action) => {
    if (!confirm(`Êtes-vous sûr de vouloir ${action === 'approve' ? 'approuver' : 'refuser'} cette demande ?`)) {
        return;
    }

    router.post(route(`agent.demandes.${action}`, props.demande.id));
};
</script>

<template>
    <Head title="Détails de la demande" />
    <AgentDPAF>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Détails de la demande</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6 flex justify-between items-center">
                            <h1 class="text-2xl font-bold text-gray-800">Détails de la demande #{{ demande.code_suivi }}</h1>
                            <Link :href="route('agent.demandes')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Retour
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informations sur la demande -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informations de la demande</h2>
                                
                                <div class="space-y-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Statut</span>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block w-fit" :class="getStatusColor(demande.statut)">
                                            {{ demande.statut }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Structure</span>
                                        <div class="flex items-center gap-4">
                                            <div class="font-medium">
                                                {{ props.demande.structure ? props.demande.structure.libelle : 'Non spécifiée' }}
                                            </div>
                                            <button 
                                                v-if="demande.statut === 'En attente'"
                                                @click="openAffectationModal"
                                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center gap-2"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                                </svg>
                                                Affecter une structure
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Type de stage</span>
                                        <span class="font-medium">{{ demande.type }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Nature</span>
                                        <span class="font-medium">{{ demande.nature }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Période</span>
                                        <span class="font-medium">Du {{ formatDate(demande.date_debut) }} au {{ formatDate(demande.date_fin) }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Date de soumission</span>
                                        <span class="font-medium">{{ formatDate(demande.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations du stagiaire -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informations du stagiaire</h2>
                                
                                <div class="space-y-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Nom complet</span>
                                        <span class="font-medium">{{ demande.stagiaire?.user?.nom }} {{ demande.stagiaire?.user?.prenom }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Email</span>
                                        <span class="font-medium">{{ demande.stagiaire?.user?.email }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Téléphone</span>
                                        <span class="font-medium">{{ demande.stagiaire?.user?.telephone }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Université</span>
                                        <span class="font-medium">{{ demande.stagiaire?.universite }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Filière</span>
                                        <span class="font-medium">{{ demande.stagiaire?.filiere }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Niveau d'étude</span>
                                        <span class="font-medium">{{ demande.stagiaire?.niveau_etude }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documents attachés -->
                        <div class="mt-6 bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Documents attachés</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Document principal (CV ou Lettre de recommandation) -->
                                <div v-if="demande.lettre_cv_path" class="flex flex-col gap-2">
                                    <span class="text-sm text-gray-500">{{ demande.type === 'Académique' ? 'Lettre de recommandation' : 'CV' }}</span>
                                    <a :href="'/storage/' + demande.lettre_cv_path"
                                        target="_blank"
                                        class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Télécharger le {{ demande.type === 'Académique' ? 'document' : 'CV' }}
                                    </a>
                                </div>
                            </div>

                            <!-- Message si aucun document -->
                            <div v-if="!demande.lettre_cv_path" 
                                class="text-center text-gray-500 py-4">
                                Aucun document n'a été attaché à cette demande
                            </div>
                        </div>

                        <!-- Actions sur la demande -->
                        <!-- <div class="mt-6 bg-gray-50 p-6 rounded-lg shadow-sm" v-if="demande.statut === 'En attente'">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Actions</h2>
                            
                            <div class="flex gap-4">
                                <button 
                                    @click="submit('approve')"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Approuver
                                </button>
                                
                                <button 
                                    @click="submit('reject')"
                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors flex items-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Refuser
                                </button>
                            </div>
                        </div> -->
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
                            Affecter une structure
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
                            v-model="selectedStructure"
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
                        :disabled="!selectedStructure"
                        :class="[
                            'px-4 py-2 rounded-lg transition-colors flex items-center gap-2',
                            selectedStructure 
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
    
    <!-- Composant Toast pour les notifications -->
    <AdminToast ref="toast" />
</template> 