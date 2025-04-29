<template>
  <Admin>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre uniquement (bouton d'ajout supprimé) -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Stagiaires</h1>
          <!-- Bouton d'ajout supprimé -->
        </div>

        <!-- Liste des stagiaires avec état vide -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des stagiaires</h2>
          </div>

          <div v-if="stagiaires.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
              class="mx-auto mb-4 text-gray-400">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <p class="text-gray-500 text-lg">Aucun stagiaire n'a été ajouté</p>
            <!-- Bouton d'ajout supprimé -->
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Nom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Prénom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Email</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Niveau</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Université</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Filière</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stagiaire in stagiaires" :key="stagiaire.id_stagiaire"
                  class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.nom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.prenom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.email ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.niveau_etude }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.universite }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.filiere }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <!-- Remplacer les boutons d'édition et de suppression par un bouton "Voir" -->
                      <button @click="viewStagiaire(stagiaire)"
                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                        Voir
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modale pour voir les détails du stagiaire -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <!-- En-tête de la modale -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">
                Détails du stagiaire
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Contenu de la modale -->
            <div class="px-6 py-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-500">Nom</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.user?.nom || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Prénom</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.user?.prenom || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Email</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.user?.email || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Téléphone</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.user?.telephone || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Date de naissance</p>
                  <p class="text-lg font-medium text-gray-900">
                    {{ formatDate(currentStagiaire?.user?.date_de_naissance) || '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Sexe</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.user?.sexe || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Niveau d'étude</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.niveau_etude || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Université</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.universite || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Filière</p>
                  <p class="text-lg font-medium text-gray-900">{{ currentStagiaire?.filiere || '-' }}</p>
                </div>
              </div>
            </div>

            <!-- Bouton pour fermer la modale -->
            <div class="flex justify-end p-4 border-t mt-6">
              <button type="button" @click="closeModal()"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 6 6 18M6 6l12 12" />
                </svg>
                Fermer
              </button>
            </div>
          </div>
        </div>

        <!-- Composant Toast pour les notifications -->
        <AdminToast ref="toast" />
      </div>
    </div>
  </Admin>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Admin from '@/Layouts/Admin.vue';
import AdminToast from '@/Components/AdminToast.vue';

const props = defineProps({
  stagiaires: Array
});

const page = usePage();
const toast = ref(null);
const isModalOpen = ref(false);
const currentStagiaire = ref(null);
// Dans le script du composant
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR'); // Format: JJ/MM/AAAA
};

// Surveiller les messages flash et les afficher automatiquement
onMounted(() => {
  // Vérifier si des messages flash existent au chargement
  setTimeout(() => {
    const { flash } = page.props;
    if (flash) {
      if (flash.success && toast.value) {
        toast.value.addToast({
          type: 'success',
          title: 'Succès',
          message: flash.success
        });
      }

      if (flash.error && toast.value) {
        toast.value.addToast({
          type: 'error',
          title: 'Erreur',
          message: flash.error
        });
      }
    }
  }, 100);
});

const viewStagiaire = (stagiaire) => {
  currentStagiaire.value = stagiaire;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  currentStagiaire.value = null;
};
</script>