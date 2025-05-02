<template>
  <Head title="Détails de la demande" />

  <RSLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Détails de la demande</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-800">Détails de la demande #{{ demande.code_suivi }}</h1>
              <Link :href="route('agent.rs.demandes')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors flex items-center gap-2">
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
                    <span class="font-medium">{{ demande.structure.libelle }}</span>
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

                  <div v-if="demande.date_traitement" class="flex flex-col">
                    <span class="text-sm text-gray-500">Date de traitement</span>
                    <span class="font-medium">{{ formatDate(demande.date_traitement) }}</span>
                  </div>

                  <div v-if="demande.motif_rejet" class="flex flex-col">
                    <span class="text-sm text-gray-500">Motif de rejet</span>
                    <span class="font-medium text-red-600">{{ demande.motif_rejet }}</span>
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
            </div>

            <!-- Actions -->
            <div v-if="demande.statut === 'En cours'" class="mt-6 bg-gray-50 p-6 rounded-lg shadow-sm">
              <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Actions</h2>
              <div class="flex gap-4">
                <button
                  @click="showApproveModal = true"
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  Acepter la demande
                </button>
                <button
                  @click="showRejectModal = true"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                  Rejeter la demande
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de rejet -->
    <Modal :show="showRejectModal" @close="closeRejectModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Rejeter la demande
        </h2>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Motif du rejet
          </label>
          <textarea
            v-model="rejectForm.motif_refus"
            rows="4"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            placeholder="Veuillez expliquer le motif du rejet..."
          ></textarea>
          <p v-if="rejectForm.errors.motif_refus" class="mt-2 text-sm text-red-600">
            {{ rejectForm.errors.motif_refus }}
          </p>
        </div>
        <div class="flex justify-end gap-4">
          <button
            @click="closeRejectModal"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
          >
            Annuler
          </button>
          <button
            @click="rejectDemande"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
            :disabled="rejectForm.processing"
          >
            Confirmer le rejet
          </button>
        </div>
      </div>
    </Modal>

    <!-- Modal de confirmation d'approbation -->
    <Modal :show="showApproveModal" @close="closeApproveModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Confirmer l'approbation
        </h2>
        <p class="text-sm text-gray-600 mb-4">
          Êtes-vous sûr de vouloir approuver cette demande de stage ?
        </p>
        <div class="flex justify-end gap-4">
          <button
            @click="closeApproveModal"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
          >
            Annuler
          </button>
          <button
            @click="approveDemande"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
            :disabled="approveForm.processing"
          >
            Confirmer l'approbation
          </button>
        </div>
      </div>
    </Modal>
  </RSLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import RSLayout from '@/Layouts/RSLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  demande: Object,
});

const showRejectModal = ref(false);
const showApproveModal = ref(false);

const rejectForm = useForm({
  motif_refus: '',
});

const approveForm = useForm({});

function closeRejectModal() {
  showRejectModal.value = false;
  rejectForm.reset();
}

function closeApproveModal() {
  showApproveModal.value = false;
  approveForm.reset();
}

function rejectDemande() {
  rejectForm.post(route('agent.rs.demandes.reject', props.demande.id), {
    onSuccess: () => {
      closeRejectModal();
    },
  });
}

function approveDemande() {
  approveForm.post(route('agent.rs.demandes.approve', props.demande.id), {
    onSuccess: () => {
      closeApproveModal();
    },
  });
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function getStatusColor(status) {
  switch (status) {
    case 'En attente':
      return 'text-yellow-600 bg-yellow-100';
    case 'En cours':
      return 'text-blue-600 bg-blue-100';
    case 'Acceptée':
      return 'text-green-600 bg-green-100';
    case 'Refusée':
      return 'text-red-600 bg-red-100';
    default:
      return 'text-gray-600 bg-gray-100';
  }
}
</script> 