<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
//import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import Admin from '@/Layouts/Admin.vue';
import AdminToast from '@/Components/AdminToast.vue';

const props = defineProps({
  agents: Array,
});

const page = usePage();
const toast = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const agentToDelete = ref(null);
const editingId = ref(null);
const step = ref(1);

const passwordConfirmation = ref('');
const passwordError = ref('');

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

const form = useForm({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  date_de_naissance: '',
  sexe: '',
  matricule: '',
  fonction: '',
  password: '',
  password_confirmation: '',
  date_embauche: '',
  role_agent: '',
});

function openModal(agent = null) {
  step.value = 1;
  passwordError.value = '';
  
  if (agent) {
    form.nom = agent.user?.nom || '';
    form.prenom = agent.user?.prenom || '';
    form.email = agent.user?.email || '';
    form.telephone = agent.user?.telephone || '';
    form.date_de_naissance = agent.user?.date_de_naissance || '';
    form.sexe = agent.user?.sexe || '';
    form.matricule = agent.matricule;
    form.fonction = agent.fonction;
    form.date_embauche = agent.date_embauche;
    form.password = '';
    form.password_confirmation = '';
    form.role_agent = agent.role_agent;
    editingId.value = agent.id;
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
  step.value = 1;
  passwordError.value = '';
}

// Fonctions pour le modal de confirmation de suppression
function openDeleteModal(agent) {
  agentToDelete.value = agent;
  showDeleteModal.value = true;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  agentToDelete.value = null;
}

function confirmDelete() {
  if (!agentToDelete.value) return;
  
  destroy(agentToDelete.value.id);
  closeDeleteModal();
}

function validatePasswords() {
  if (editingId.value && !form.password) {
    return true;
  }
  
  if (form.password !== form.password_confirmation) {
    passwordError.value = 'Les mots de passe ne correspondent pas';
    return false;
  }
  
  if (!editingId.value && !form.password) {
    passwordError.value = 'Le mot de passe est obligatoire';
    return false;
  }
  
  if (form.password && form.password.length < 8) {
    passwordError.value = 'Le mot de passe doit contenir au moins 8 caractères';
    return false;
  }
  
  passwordError.value = '';
  return true;
}

function nextStep() {
  if (step.value === 1) {
    if (!validatePasswords()) {
      return;
    }
    step.value = 2;
  }
}

function submit() {
  if (!validatePasswords()) {
    step.value = 1;
    return;
  }
  
  if (editingId.value) {
    form.put(route('admin.agents.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        // Afficher un message personnalisé
        if (toast.value) {
          toast.value.addToast({
            type: 'success',
            title: 'Agent modifié',
            message: `L'agent "${form.prenom} ${form.nom}" a été mis à jour avec succès.`
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
    form.post(route('admin.agents.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        // Afficher un message personnalisé
        if (toast.value) {
          toast.value.addToast({
            type: 'success',
            title: 'Agent ajouté',
            message: `L'agent "${form.prenom} ${form.nom}" a été ajouté avec succès.`
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
  // Trouver l'agent pour afficher son nom dans le message de confirmation
  const agent = props.agents.find(a => a.id === id);
  
  router.delete(route('agents.destroy', id), {
    onSuccess: () => {
      // Afficher un message personnalisé
      if (toast.value) {
        toast.value.addToast({
          type: 'success',
          title: 'Agent supprimé',
          message: `L'agent "${agent?.user?.prenom || ''} ${agent?.user?.nom || ''}" a été supprimé avec succès.`
        });
      }
    },
    onError: () => {
      if (toast.value) {
        toast.value.addToast({
          type: 'error',
          title: 'Erreur de suppression',
          message: 'Impossible de supprimer cet agent'
        });
      }
    }
  });
}
</script>

<template>
  <Head title="Gestion des Agents" />
  <Admin>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre et bouton d'ajout -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Agents</h1>
          <button @click="openModal()"
            class="px-5 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 5v14M5 12h14" />
            </svg>
            <span class="font-medium">Ajouter un agent</span>
          </button>
        </div>

        <!-- Flash message amélioré -->
        <div v-if="flash"
          class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
          <span>{{ flash }}</span>
        </div>

        <div v-if="$page.props.flash && $page.props.flash.error" class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
          {{ $page.props.flash.error }}
        </div>

        <!-- Liste des agents avec état vide -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des agents</h2>
          </div>

          <div v-if="props.agents && props.agents.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
              class="mx-auto mb-4 text-gray-400">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <p class="text-gray-500 text-lg">Aucun agent n'a été ajouté</p>
            <button @click="openModal()"
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">
              Ajouter votre premier agent
            </button>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Nom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Prénom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Email</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Téléphone</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Matricule</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Fonction</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Rôle</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="agent in props.agents" :key="agent.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.user?.nom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.user?.prenom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.user?.email ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.user?.telephone ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.matricule }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.fonction }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ agent.role_agent }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
  <!-- Bouton Modifier -->
  <button 
    @click="openModal(agent)"
    class="text-blue-600 hover:text-blue-800 flex items-center"
    title="Modifier"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
    </svg>
  </button>

  <!-- Bouton Supprimer -->
  <button @click="openDeleteModal(agent)" title="Supprimer"
    class="text-red-600 hover:text-red-800 transition-colors ml-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
      stroke="currentColor" class="w-5 h-5">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
    </svg>
  </button>
</div>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modale multi-étapes améliorée -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <!-- En-tête de la modale -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">
                {{ editingId ? 'Modifier un agent' : 'Ajouter un agent' }}
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Barre de progression améliorée -->
            <div class="px-6 pt-6">
              <div class="flex justify-between mb-2">
                <div class="flex items-center">
                  <div :class="[
                    'flex items-center justify-center w-8 h-8 rounded-full mr-2',
                    step === 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'
                  ]">1</div>
                  <span :class="{ 'text-blue-600 font-medium': step === 1 }">Informations personnelles</span>
                </div>
                <div class="flex items-center">
                  <div :class="[
                    'flex items-center justify-center w-8 h-8 rounded-full mr-2',
                    step === 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'
                  ]">2</div>
                  <span :class="{ 'text-blue-600 font-medium': step === 2 }">Informations professionnelles</span>
                </div>
              </div>
              <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 transition-all duration-500"
                  :style="{ width: step === 1 ? '50%' : '100%' }"></div>
              </div>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="px-6 py-4">
              <!-- Étape 1 : Informations personnelles -->
              <div v-if="step === 1" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input v-model="form.nom" type="text" placeholder="Nom de l'agent"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      required />
                    <div v-if="form.errors.nom" class="text-red-600 text-sm mt-1">{{ form.errors.nom }}</div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input v-model="form.prenom" type="text" placeholder="Prénom de l'agent"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      required />
                    <div v-if="form.errors.prenom" class="text-red-600 text-sm mt-1">{{ form.errors.prenom }}</div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input v-model="form.email" type="email" placeholder="adresse@email.com"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required />
                  <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                  <input v-model="form.telephone" type="number" placeholder="Numéro de téléphone" pattern="[0-9]{10}"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required />
                  <div v-if="form.errors.telephone" class="text-red-600 text-sm mt-1">{{ form.errors.telephone }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                    <input v-model="form.date_de_naissance" type="date"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      required />
                    <div v-if="form.errors.date_de_naissance" class="text-red-600 text-sm mt-1">{{
                      form.errors.date_de_naissance }}</div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                    <select v-model="form.sexe"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      required>
                      <option value="">Sélectionner</option>
                      <option value="Homme">Masculin</option>
                      <option value="Femme">Féminin</option>
                    </select>
                    <div v-if="form.errors.sexe" class="text-red-600 text-sm mt-1">{{ form.errors.sexe }}</div>
                  </div>
                </div>
                
                <!-- Section mot de passe avec confirmation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input v-model="form.password" type="password" placeholder="Mot de passe"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      :required="!editingId" />
                    <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
                    <div v-if="editingId" class="text-gray-500 text-xs mt-1">Laissez vide pour conserver le mot de passe actuel</div>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input v-model="form.password_confirmation" type="password" placeholder="Confirmez le mot de passe"
                      class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      :required="!editingId || form.password" />
                    <div v-if="passwordError" class="text-red-600 text-sm mt-1">{{ passwordError }}</div>
                  </div>
                </div>
              </div>

              <!-- Étape 2 : Informations professionnelles -->
              <div v-if="step === 2" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                  <input v-model="form.matricule" type="text" placeholder="Matricule de l'agent"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required />
                  <div v-if="form.errors.matricule" class="text-red-600 text-sm mt-1">{{ form.errors.matricule }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                  <input v-model="form.fonction" type="text" placeholder="Fonction occupée"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required />
                  <div v-if="form.errors.fonction" class="text-red-600 text-sm mt-1">{{ form.errors.fonction }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rôle de l'agent</label>
                  <select v-model="form.role_agent"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="DPAF">Direction du Patrimoine et des Affaires Foncières (DPAF)</option>
                    <option value="MS">Maître de Stage (MS)</option>
                    <option value="RS">Responsable de Structure (RS)</option>
                  </select>
                  <div v-if="form.errors.role_agent" class="text-red-600 text-sm mt-1">{{ form.errors.role_agent }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                  <input v-model="form.date_embauche" type="date"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required />
                  <div v-if="form.errors.date_embauche" class="text-red-600 text-sm mt-1">{{ form.errors.date_embauche
                    }}</div>
                </div>
              </div>

              <!-- Boutons de navigation améliorés -->
              <div class="flex justify-between pt-6 border-t mt-6">
                <button type="button" @click="step > 1 ? step-- : closeModal()"
                  class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 flex items-center gap-1">
                  <svg v-if="step > 1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                  </svg>
                  {{ step > 1 ? 'Précédent' : 'Annuler' }}
                </button>

                <button v-if="step < 2" type="button" @click="nextStep()"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1">
                  Suivant
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6" />
                  </svg>
                </button>

                <button v-else type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1"
                  :disabled="form.processing">
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  <span v-if="editingId">Mettre à jour</span>
                  <span v-else>Ajouter</span>
                  <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
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
                <h3 class="text-lg font-medium text-red-800">Supprimer l'agent</h3>
              </div>
            </div>
            
            <div class="px-6 py-4">
              <p class="text-gray-700 mb-4">
                Voulez-vous vraiment supprimer l'agent "{{ agentToDelete?.user?.prenom || '' }} {{ agentToDelete?.user?.nom || '' }}" ?<br>
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
      </div>
    </div>
  </Admin>
  
  <!-- Composant Toast pour les notifications -->
  <AdminToast ref="toast" />
</template>