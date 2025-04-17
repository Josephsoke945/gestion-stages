<template>
  <SimpleLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre et bouton d'ajout -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Stagiaires</h1>
          <button 
            @click="openModal()" 
            class="px-5 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition duration-200 flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            <span class="font-medium">Ajouter un stagiaire</span>
          </button>
        </div>

        <!-- Flash message si nécessaire -->
        <div v-if="flash" class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <span>{{ flash }}</span>
        </div>

        <!-- Liste des stagiaires avec état vide -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des stagiaires</h2>
          </div>
          
          <div v-if="stagiaires.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4 text-gray-400">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <p class="text-gray-500 text-lg">Aucun stagiaire n'a été ajouté</p>
            <button 
              @click="openModal()" 
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200"
            >
              Ajouter votre premier stagiaire
            </button>
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
                <tr v-for="stagiaire in stagiaires" :key="stagiaire.id_stagiaire" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.nom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.prenom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.email ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.niveau_etude }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.universite }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.filiere }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <button 
                        @click="editStagiaire(stagiaire)" 
                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        </svg>
                        Modifier
                      </button>
                      <button 
                        @click="deleteStagiaire(stagiaire.id_stagiaire)" 
                        class="text-red-600 hover:text-red-800 font-medium flex items-center gap-1"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18"/>
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
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

        <!-- Modale pour l'ajout/modification de stagiaire -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <!-- En-tête de la modale -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">
                {{ editing ? 'Modifier un stagiaire' : 'Ajouter un stagiaire' }}
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submit" class="px-6 py-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                  <input 
                    v-model="form.nom" 
                    type="text"
                    placeholder="Nom du stagiaire"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                  <input 
                    v-model="form.prenom" 
                    type="text"
                    placeholder="Prénom du stagiaire"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input 
                    v-model="form.email" 
                    type="email"
                    placeholder="adresse@email.com"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                  <input 
                    v-model="form.telephone" 
                    type="number"
                    placeholder="Numéro de téléphone"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                  <input 
                    v-model="form.date_de_naissance" 
                    type="date"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sexe</label>
                  <select 
                    v-model="form.sexe"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  >
                    <option value="">Sélectionner</option>
                    <option value="Homme">Masculin</option>
                    <option value="Femme">Féminin</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Niveau d'étude</label>
                  <select 
                    v-model="form.niveau_etude"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  >
                    <option value="">Sélectionner</option>
                    <option value="Licence 1">Licence 1</option>
                    <option value="Licence 2">Licence 2</option>
                    <option value="Licence 3">Licence 3</option>
                    <option value="Master 1">Master 1</option>
                    <option value="Master 2">Master 2</option>
                    <option value="Autre">Autre</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Université</label>
                  <input 
                    v-model="form.universite" 
                    type="text"
                    placeholder="Nom de l'université"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Filière</label>
                  <input 
                    v-model="form.filiere" 
                    type="text"
                    placeholder="Nom de la filière"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ editing ? 'Nouveau mot de passe (laisser vide pour conserver)' : 'Mot de passe' }}
                  </label>
                  <input 
                    v-model="form.password" 
                    type="password"
                    placeholder="Mot de passe"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    :required="!editing"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ editing ? 'Confirmer le nouveau mot de passe' : 'Confirmer le mot de passe' }}
                  </label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password"
                    placeholder="Confirmer le mot de passe"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    :required="!editing"
                  />
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
                  :disabled="processing"
                >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span v-if="editing">Mettre à jour</span>
                  <span v-else>Ajouter</span>
                  <svg v-if="!processing" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
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

<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';

const props = defineProps({
  stagiaires: Array
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

const isModalOpen = ref(false);
const editing = ref(false);
const currentId = ref(null);
const processing = ref(false);

const form = ref({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  date_de_naissance: '',
  sexe: '',
  niveau_etude: '',
  universite: '',
  filiere: '',
  password: '',
  password_confirmation: ''
});

const openModal = () => {
  editing.value = false;
  form.value = {
    nom: '', prenom: '', email: '', telephone: '', date_de_naissance: '', sexe: '',
    niveau_etude: '', universite: '', filiere: '', password: '', password_confirmation: ''
  };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const editStagiaire = (stagiaire) => {
  editing.value = true;
  currentId.value = stagiaire.id_stagiaire;
  form.value = {
    nom: stagiaire.user?.nom || '',
    prenom: stagiaire.user?.prenom || '',
    email: stagiaire.user?.email || '',
    telephone: stagiaire.user?.telephone || '',
    date_de_naissance: stagiaire.user?.date_de_naissance || '',
    sexe: stagiaire.user?.sexe || '',
    niveau_etude: stagiaire.niveau_etude,
    universite: stagiaire.universite,
    filiere: stagiaire.filiere,
    password: '',
    password_confirmation: ''
  };
  isModalOpen.value = true;
};

const submit = () => {
  processing.value = true;
  if (editing.value) {
    router.put(`/stagiaires/${currentId.value}`, form.value, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: (errors) => {
        console.log(errors);
        processing.value = false;
      }
    });
  } else {
    router.post('/stagiaires', form.value, {
      onSuccess: () => {
        closeModal();
        processing.value = false;
      },
      onError: (errors) => {
        console.log(errors);
        processing.value = false;
      }
    });
  }
};

const deleteStagiaire = (id) => {
  if (confirm('Voulez-vous vraiment supprimer ce stagiaire ?')) {
    router.delete(`/stagiaires/${id}`);
  }
};
</script>