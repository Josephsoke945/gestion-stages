<template>
  <Head title="Gestion des Demandes" />

  <RSLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Demandes de Stage - {{ structure.libelle }}
        </h2>
        <div class="text-sm text-gray-500">
          Structure : {{ structure.sigle }}
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Messages flash -->
        <div v-if="$page.props.flash.success" 
          class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" 
          role="alert">
          <p class="font-medium">Succès!</p>
          <p>{{ $page.props.flash.success }}</p>
        </div>

        <div v-if="$page.props.flash.error" 
          class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" 
          role="alert">
          <p class="font-medium">Erreur!</p>
          <p>{{ $page.props.flash.error }}</p>
        </div>

        <!-- Filtres -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Filtre par statut -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Statut
                </label>
                <div class="relative">
                  <select 
                    v-model="filters.status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-3 pr-10 py-2"
                  >
                    <option value="">Tous les statuts</option>
                    <option value="En attente">En attente</option>
                    <option value="Approuvée">Approuvée</option>
                    <option value="Refusée">Refusée</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Recherche par nom -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Rechercher
                </label>
                <div class="relative">
                  <input 
                    v-model="filters.search"
                    type="text"
                    placeholder="Nom, prénom ou email..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-10 pr-4 py-2"
                  >
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                  <div v-if="isLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Filtre par date -->
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Période
                </label>
                <div class="relative">
                  <select 
                    v-model="filters.period"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-3 pr-10 py-2"
                  >
                    <option value="">Toutes les périodes</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Bouton de réinitialisation -->
              <div class="flex items-end">
                <button 
                  @click="resetFilters"
                  class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors flex items-center justify-center"
                  title="Réinitialiser tous les filtres"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Réinitialiser
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des demandes -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div v-if="demandes.data.length === 0" class="text-center py-12">
              <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune demande trouvée</h3>
              <p class="mt-2 text-sm text-gray-500">
                {{ filters.status || filters.search || filters.period ? 'Aucune demande ne correspond à vos critères de recherche.' : 'Aucune demande de stage n\'a été soumise pour votre structure.' }}
              </p>
            </div>

            <div v-else>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stagiaire</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de soumission</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="demande in demandes.data" :key="demande.id" class="hover:bg-gray-50 transition-colors duration-150">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                              <span class="text-gray-500 font-medium">
                                {{ demande.stagiaire.user.nom.charAt(0) }}{{ demande.stagiaire.user.prenom.charAt(0) }}
                              </span>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ demande.stagiaire.user.nom }} {{ demande.stagiaire.user.prenom }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ demande.stagiaire.user.email }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ formatDate(demande.created_at) }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ formatTime(demande.created_at) }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(demande.statut)" 
                          class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ demande.statut }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <Link 
                          :href="route('agent.rs.demandes.show', demande.id)"
                          class="text-blue-600 hover:text-blue-900 flex items-center transition-colors duration-150"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          Voir les détails
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="mt-6">
                <Pagination :links="demandes.links" @change="handlePageChange" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </RSLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import RSLayout from '@/Layouts/RSLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
  demandes: Object,
  structure: Object,
  filters: Object
});

const isLoading = ref(false);
const filters = ref({
  status: props.filters?.status || '',
  search: props.filters?.search || '',
  period: props.filters?.period || '',
  page: props.filters?.page || '1',
});

// Fonction pour mettre à jour les filtres et recharger les données
const updateFilters = debounce((newFilters) => {
  isLoading.value = true;
  router.get(
    route('agent.rs.demandes'),
    { ...newFilters, page: filters.value.page },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onFinish: () => {
        isLoading.value = false;
      },
      onError: () => {
        isLoading.value = false;
      }
    }
  );
}, 300);

// Observer les changements des filtres individuels
watch(() => filters.value.status, (newValue) => {
  filters.value.page = '1'; // Réinitialiser la page lors du changement de filtre
  updateFilters({ ...filters.value, status: newValue });
});

watch(() => filters.value.search, (newValue) => {
  filters.value.page = '1'; // Réinitialiser la page lors du changement de filtre
  updateFilters({ ...filters.value, search: newValue });
});

watch(() => filters.value.period, (newValue) => {
  filters.value.page = '1'; // Réinitialiser la page lors du changement de filtre
  updateFilters({ ...filters.value, period: newValue });
});

// Réinitialiser tous les filtres
function resetFilters() {
  filters.value = {
    status: '',
    search: '',
    period: '',
    page: '1',
  };
  updateFilters(filters.value);
}

// Gérer le changement de page
function handlePageChange(page) {
  filters.value.page = page;
  updateFilters(filters.value);
}

// Initialiser les filtres depuis l'URL au chargement
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  filters.value = {
    status: urlParams.get('status') || '',
    search: urlParams.get('search') || '',
    period: urlParams.get('period') || '',
    page: urlParams.get('page') || '1',
  };
});

function formatDate(date) {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function formatTime(date) {
  return new Date(date).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit',
  });
}

function getStatusClass(status) {
  const classes = {
    'En attente': 'bg-yellow-100 text-yellow-800',
    'En cours': 'bg-blue-100 text-blue-800',
    'Acceptée': 'bg-green-100 text-green-800',
    'Refusée': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
}
</script> 