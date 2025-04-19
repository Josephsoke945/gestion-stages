<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';

const props = defineProps({
  agents: Array,
});

// Gestion du flash message Inertia
const page = usePage();
const flash = ref(page.props.flash?.success || '');
watch(() => page.props.flash, (newVal) => {
  flash.value = newVal?.success || '';
  if (flash.value) {
    setTimeout(() => (flash.value = ''), 4000);
  }
});

// Contrôle de la modale et de la progression du formulaire
const showModal = ref(false);
const editingId = ref(null);
const step = ref(1);

// Ajout d'une variable pour la confirmation du mot de passe
const passwordConfirmation = ref('');
const passwordError = ref('');

// Définition du formulaire avec tous les champs nécessaires
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
  password_confirmation: '', // Ajout du champ de confirmation
  date_embauche: '',
});

function openModal(agent = null) {
  step.value = 1; // Réinitialiser la progression
  passwordError.value = ''; // Réinitialiser les erreurs personnalisées
  
  if (agent) {
    console.log('Mode édition :', agent);
    // Remplissage des infos personnelles issues de la table users
    form.nom = agent.user?.nom || '';
    form.prenom = agent.user?.prenom || '';
    form.email = agent.user?.email || '';
    form.telephone = agent.user?.telephone || '';
    form.date_de_naissance = agent.user?.date_de_naissance || '';
    form.sexe = agent.user?.sexe || '';
    // Infos spécifiques de l'agent
    form.matricule = agent.matricule;
    form.fonction = agent.fonction;
    form.date_embauche = agent.date_embauche;
    // Réinitialiser les mots de passe
    form.password = '';
    form.password_confirmation = '';
    editingId.value = agent.id;
    console.log('editingId après modification :', editingId.value);
  } else {
    console.log('Mode ajout');
    form.reset();
    editingId.value = null;
    console.log('editingId après reset :', editingId.value);
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

function validatePasswords() {
  // Si on est en mode édition et aucun mot de passe n'est fourni, on autorise
  if (editingId.value && !form.password) {
    return true;
  }
  
  // Vérifier que les mots de passe correspondent
  if (form.password !== form.password_confirmation) {
    passwordError.value = 'Les mots de passe ne correspondent pas';
    return false;
  }
  
  // En mode création, le mot de passe est obligatoire
  if (!editingId.value && !form.password) {
    passwordError.value = 'Le mot de passe est obligatoire';
    return false;
  }
  
  // Vérifier la longueur minimale du mot de passe s'il est fourni
  if (form.password && form.password.length < 8) {
    passwordError.value = 'Le mot de passe doit contenir au moins 8 caractères';
    return false;
  }
  
  passwordError.value = '';
  return true;
}

function nextStep() {
  if (step.value === 1) {
    // Valider les mots de passe avant de passer à l'étape suivante
    if (!validatePasswords()) {
      return;
    }
    step.value = 2;
  }
}

function submit() {
  // Vérifier une dernière fois les mots de passe
  if (!validatePasswords()) {
    step.value = 1; // Retourner à l'étape du mot de passe
    return;
  }
  
  console.log('Submit avec editingId :', editingId.value);
  if (editingId.value) {
    form.put(route('agents.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('agents.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
}

function destroy(id) {
  if (confirm('Voulez-vous vraiment supprimer cet agent ?')) {
    router.delete(route('agents.destroy', id));
  }
}
</script>

<template>
  <Head title="Gestion des Agents" />
  <SimpleLayout>
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
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <button @click="openModal(agent)"
                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                        </svg>
                        Modifier
                      </button>
                      <button @click="destroy(agent.id)"
                        class="text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18" />
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        </svg>
                        Supprimer
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
      </div>
    </div>
  </SimpleLayout>
</template>