<script setup>
import { Head, router } from '@inertiajs/vue3';
import Stagiaire from '@/Layouts/Stagiaire.vue';

defineProps({
  demandes: Array,
});

// Fonction pour formater une date
const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('fr-FR');
};

// Fonction pour obtenir une couleur selon le statut
const getStatusColor = (statut) => {
  switch (statut) {
    case 'En attente':
      return 'text-yellow-600 bg-yellow-100';
    case 'Acceptée':
      return 'text-green-600 bg-green-100';
    case 'Refusée':
      return 'text-red-600 bg-red-100';
    default:
      return 'text-gray-600 bg-gray-100';
  }
};

// Fonction pour annuler une demande
const annulerDemande = (id) => {
  if (confirm('Êtes-vous sûr de vouloir annuler cette demande ?')) {
    router.delete(route('mes.demandes.annuler', id));
  }
};

// Fonction pour voir les détails d'une demande
const voirDetails = (demandeId) => {
  // Rediriger vers la page de détails avec l'ID de la demande
  router.visit(route('mes.demandes.show', demandeId));
};
</script>

<template>
  <Head title="Mes Demandes de Stage" />
  <Stagiaire>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Mes Demandes de Stage</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h1 class="mb-6 text-2xl font-bold">Historique de mes demandes</h1>
            
            <div v-if="demandes && demandes.length > 0">
              <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Code de suivi</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Structure</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Type</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Nature</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Période</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Statut</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Date de soumission</th>
                      <th class="p-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="demande in demandes" :key="demande.id" class="hover:bg-gray-50">
                      <td class="p-3 text-sm font-medium text-indigo-600">{{ demande.code_suivi }}</td>
                      <td class="p-3 text-sm text-gray-900">{{ demande.structure?.libelle || '-' }}</td>
                      <td class="p-3 text-sm text-gray-900">{{ demande.type }}</td>
                      <td class="p-3 text-sm text-gray-900">{{ demande.nature }}</td>
                      <td class="p-3 text-sm text-gray-900">
                        {{ formatDate(demande.date_debut) }} - {{ formatDate(demande.date_fin) }}
                      </td>
                      <td class="p-3 text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusColor(demande.statut)">
                          {{ demande.statut }}
                        </span>
                      </td>
                      <td class="p-3 text-sm text-gray-900">{{ formatDate(demande.date_soumission) }}</td>
                      <td class="p-3 text-sm">
                        <div class="flex space-x-2">
                          <button
                            class="text-indigo-600 hover:text-indigo-900 hover:underline"
                            @click="voirDetails(demande.id)"
                          >
                            Détails
                          </button>
                          <button 
                            v-if="demande.statut === 'En attente'"
                            class="text-red-600 hover:text-red-900 hover:underline"
                            @click="annulerDemande(demande.id)"
                          >
                            Annuler
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div v-else class="p-6 text-center bg-gray-50 rounded-lg">
              <p class="text-lg text-gray-600">Vous n'avez pas encore soumis de demande de stage.</p>
              <button 
                @click="router.visit(route('dashboard'))" 
                class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
              >
                Soumettre une demande
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Stagiaire>
</template>

<style scoped>
/* Styles spécifiques si nécessaire */
</style> 