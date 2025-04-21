<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
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
      <h2 class="text-2xl font-semibold text-gray-800">Mes Demandes de Stage</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-8">
            <h1 class="mb-8 text-3xl font-bold">Historique de mes demandes</h1>
            
            <div v-if="demandes && demandes.length > 0">
              <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Code de suivi</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Structure</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Type</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Nature</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Période</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Statut</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Date de soumission</th>
                      <th class="p-4 text-sm font-medium tracking-wider text-left text-gray-500 uppercase border-b">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="demande in demandes" :key="demande.id" class="hover:bg-gray-50">
                      <td class="p-4 text-base font-medium text-indigo-600">{{ demande.code_suivi }}</td>
                      <td class="p-4 text-base text-gray-900">{{ demande.structure?.libelle || '-' }}</td>
                      <td class="p-4 text-base text-gray-900">{{ demande.type }}</td>
                      <td class="p-4 text-base text-gray-900">{{ demande.nature }}</td>
                      <td class="p-4 text-base text-gray-900">
                        {{ formatDate(demande.date_debut) }} - {{ formatDate(demande.date_fin) }}
                      </td>
                      <td class="p-4 text-base">
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full" :class="getStatusColor(demande.statut)">
                          {{ demande.statut }}
                        </span>
                      </td>
                      <td class="p-4 text-base text-gray-900">{{ formatDate(demande.date_soumission) }}</td>
                      <td class="p-4">
                        <div class="flex space-x-3">
                          <button
                            class="p-3 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition duration-300 flex items-center justify-center"
                            @click="voirDetails(demande.id)"
                            title="Voir les détails"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                          <button 
                            v-if="demande.statut === 'En attente'"
                            class="p-3 bg-white border border-red-600 text-red-600 rounded-md shadow hover:bg-red-50 transition duration-300 flex items-center justify-center"
                            @click="annulerDemande(demande.id)"
                            title="Annuler la demande"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div v-else class="p-8 text-center bg-gray-50 rounded-lg">
              <p class="text-xl text-gray-600">Vous n'avez pas encore soumis de demande de stage.</p>
              <button 
                @click="router.visit(route('dashboard'))" 
                class="px-5 py-3 mt-6 text-lg text-white bg-blue-600 rounded-lg hover:bg-blue-700"
              >
                Soumettre une demande
              </button>
            </div>
            
            <!-- Pagination -->
            <div v-if="demandes && demandes.length > 0" class="mt-6 px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-white sm:px-6">
              <!-- Version desktop de la pagination -->
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Affichage de <span class="font-medium">{{ demandes.from || 1 }}</span> à 
                    <span class="font-medium">{{ demandes.to || demandes.length }}</span> sur 
                    <span class="font-medium">{{ demandes.total || demandes.length }}</span> demandes
                  </p>
                </div>
                <div v-if="demandes.links && demandes.links.length > 3">
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Bouton "Précédent" -->
                    <Link 
                      v-if="demandes.prev_page_url" 
                      :href="demandes.prev_page_url" 
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Précédent</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </Link>
                    <span 
                      v-else 
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed"
                    >
                      <span class="sr-only">Précédent</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </span>
                    
                    <!-- Numéros de pages -->
                    <template v-for="(link, i) in demandes.links.slice(1, -1)" :key="i">
                      <Link 
                        v-if="link.url" 
                        :href="link.url" 
                        :class="[
                          link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                        ]"
                      >
                        {{ link.label }}
                      </Link>
                      <span 
                        v-else 
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                      >
                        {{ link.label }}
                      </span>
                    </template>
                    
                    <!-- Bouton "Suivant" -->
                    <Link 
                      v-if="demandes.next_page_url" 
                      :href="demandes.next_page_url" 
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                      <span class="sr-only">Suivant</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </Link>
                    <span 
                      v-else 
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed"
                    >
                      <span class="sr-only">Suivant</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </span>
                  </nav>
                </div>
              </div>
              
              <!-- Version mobile de la pagination -->
              <div v-if="demandes.links && demandes.links.length > 3" class="flex items-center justify-between w-full sm:hidden">
                <Link 
                  v-if="demandes.prev_page_url" 
                  :href="demandes.prev_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Précédent
                </Link>
                <span 
                  v-else 
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed"
                >
                  Précédent
                </span>
                
                <span class="text-sm text-gray-700">
                  Page {{ demandes.current_page || 1 }} sur {{ demandes.last_page || 1 }}
                </span>
                
                <Link 
                  v-if="demandes.next_page_url" 
                  :href="demandes.next_page_url" 
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Suivant
                </Link>
                <span 
                  v-else 
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed"
                >
                  Suivant
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Stagiaire>
</template>

<style scoped>
/* Styles supplémentaires pour améliorer la lisibilité */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

th {
  font-weight: 600;
  letter-spacing: 0.05em;
}

td, th {
  white-space: nowrap;
}

@media (max-width: 768px) {
  .overflow-x-auto {
    padding-bottom: 16px;
  }
  
  td, th {
    padding: 12px 8px;
    font-size: 14px;
  }
}
</style>