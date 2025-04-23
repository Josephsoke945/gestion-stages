<template>
  <Head title="Structures" />

  <Admin :title="'Gestion des Structures'">
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre et bouton d'ajout -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Structures</h1>
          <button 
            @click="openModal()" 
            class="px-5 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition duration-200 flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            <span class="font-medium">Ajouter une structure</span>
          </button>
        </div>

        <!-- Liste des structures -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des structures</h2>
          </div>
          
          <div v-if="props.structures.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4 text-gray-400">
              <path d="M2 17a5 5 0 0 1 5-5h14a5 5 0 0 1 5 5v5H2z"></path>
              <path d="M6 9V2h12v7"></path>
              <path d="M2 17a5 5 0 0 1 5-5h14a5 5 0 0 1 5 5v5H2z"></path>
            </svg>
            <p class="text-gray-500 text-lg">Aucune structure n'a été ajoutée</p>
            <button 
              @click="openModal()" 
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200"
            >
              Ajouter votre première structure
            </button>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Sigle</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Libellé</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Description</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="structure in props.structures" :key="structure.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ structure.sigle }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ structure.libelle }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ structure.description ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <!-- Bouton Modifier -->
                      <button 
                        @click="openModal(structure)" 
                        class="text-blue-600 hover:text-blue-800 flex items-center"
                        title="Modifier"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        </svg>
                      </button>

                      <!-- Bouton Supprimer -->
                      <button 
                        @click="openDeleteModal(structure)" 
                        class="text-red-600 hover:text-red-800 flex items-center"
                        title="Supprimer"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18"/>
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal d'édition/création -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <!-- En-tête de la modale -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">
                {{ editingId ? 'Modifier une structure' : 'Ajouter une structure' }}
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="px-6 py-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sigle</label>
                  <input 
                    v-model="form.sigle" 
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ex: ONU"
                  />
                  <div v-if="form.errors.sigle" class="mt-1 text-sm text-red-600">{{ form.errors.sigle }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                  <input 
                    v-model="form.libelle" 
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Ex: Organisation des Nations Unies"
                  />
                  <div v-if="form.errors.libelle" class="mt-1 text-sm text-red-600">{{ form.errors.libelle }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea 
                    v-model="form.description" 
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    rows="3"
                    placeholder="Description de la structure..."
                  ></textarea>
                  <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                </div>
              </div>

              <!-- Boutons de navigation -->
              <div class="flex justify-between pt-6 border-t mt-6">
                <button 
                  type="button" 
                  @click="closeModal()" 
                  class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 flex items-center gap-1"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12"/>
                  </svg>
                  Annuler
                </button>

                <button 
                  type="submit" 
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1"
                  :disabled="form.processing"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-if="editingId">Mettre à jour</span>
                  <span v-else>Ajouter</span>
                  <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                  </svg>
                </button>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Modal de confirmation de suppression -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-100">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </div>
                <h3 class="text-lg font-medium text-red-800">Supprimer la structure</h3>
              </div>
            </div>
            
            <div class="px-6 py-4">
              <p class="text-gray-700 mb-4">
                Voulez-vous vraiment supprimer la structure "{{ structureToDelete?.libelle || '' }}" ?<br>
                Cette action est irréversible.
              </p>
              
              <div class="flex justify-end space-x-3">
                <button 
                  @click="closeDeleteModal" 
                  class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  Annuler
                </button>
                <button 
                  @click="confirmDelete" 
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Ajout du composant AdminToast -->
        <AdminToast ref="toast" />
      </div>
    </div>
  </Admin>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import Admin from '@/Layouts/Admin.vue';
//import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import AdminToast from '@/Components/AdminToast.vue';

const props = defineProps({
  structures: Array,
});

const page = usePage();
const toast = ref(null);
const showModal = ref(false);
const editingId = ref(null);

// Ajout des variables pour la confirmation de suppression
const showDeleteModal = ref(false);
const structureToDelete = ref(null);

const form = useForm({
  sigle: '',
  libelle: '',
  description: '',
});

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
  }, 100); // Petit délai pour s'assurer que le composant est monté
});

function openModal(structure = null) {
  if (structure) {
    form.sigle = structure.sigle;
    form.libelle = structure.libelle;
    form.description = structure.description;
    editingId.value = structure.id;
  } else {
    form.reset();
    editingId.value = null;
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  form.reset();
  editingId.value = null;
}

// Fonctions pour le modal de confirmation de suppression
function openDeleteModal(structure) {
  structureToDelete.value = structure;
  showDeleteModal.value = true;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  structureToDelete.value = null;
}

function confirmDelete() {
  if (!structureToDelete.value) return;
  
  destroy(structureToDelete.value.id);
  closeDeleteModal();
}

function submit() {
  if (editingId.value) {
    form.put(route('structures.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        // Afficher un message personnalisé
        if (toast.value) {
          toast.value.addToast({
            type: 'success',
            title: 'Structure modifiée',
            message: `La structure "${form.libelle}" a été mise à jour avec succès.`
          });
        }
      },
      onError: () => {
        if (toast.value) {
          toast.value.addToast({
            type: 'error',
            title: 'Erreur de validation',
            message: 'Veuillez vérifier les informations saisies'
          });
        }
      }
    });
  } else {
    form.post(route('structures.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        // Afficher un message personnalisé
        if (toast.value) {
          toast.value.addToast({
            type: 'success',
            title: 'Structure ajoutée',
            message: `La structure "${form.libelle}" a été ajoutée avec succès.`
          });
        }
      },
      onError: () => {
        if (toast.value) {
          toast.value.addToast({
            type: 'error',
            title: 'Erreur de validation',
            message: 'Veuillez vérifier les informations saisies'
          });
        }
      }
    });
  }
}

function destroy(id) {
  // Trouver la structure pour afficher son nom dans le message de confirmation
  const structure = props.structures.find(s => s.id === id);
  
  router.delete(route('structures.destroy', id), {
    onSuccess: () => {
      // Afficher un message personnalisé
      if (toast.value) {
        toast.value.addToast({
          type: 'success',
          title: 'Structure supprimée',
          message: `La structure "${structure?.libelle || ''}" a été supprimée avec succès.`
        });
      }
    },
    onError: () => {
      if (toast.value) {
        toast.value.addToast({
          type: 'error',
          title: 'Erreur de suppression',
          message: 'Impossible de supprimer cette structure'
        });
      }
    }
  });
}
</script>