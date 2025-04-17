<!-- resources/js/Pages/Users/Index.vue -->
<template>
  <Head title="Gestion des utilisateurs" />

  <SimpleLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre et bouton d'ajout -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Utilisateurs</h1>
          <button 
            @click="openModal()" 
            class="px-5 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition duration-200 flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            <span class="font-medium">Ajouter un utilisateur</span>
          </button>
        </div>

        <!-- Flash message si nécessaire -->
        <div v-if="$page.props.flash && $page.props.flash.success" class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <span>{{ $page.props.flash.success }}</span>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Liste des utilisateurs</h2>
          </div>
          
          <div v-if="users.length === 0" class="p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4 text-gray-400">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <p class="text-gray-500 text-lg">Aucun utilisateur n'a été ajouté</p>
            <button 
              @click="openModal()" 
              class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200"
            >
              Ajouter votre premier utilisateur
            </button>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Nom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Email</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Rôle</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ user.nom }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ user.email }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <span 
                      :class="{
                        'px-2 py-1 rounded text-xs font-semibold': true,
                        'bg-blue-100 text-blue-800': user.role.toLowerCase() === 'admin',
                        'bg-green-100 text-green-800': user.role.toLowerCase() === 'agent',
                        'bg-yellow-100 text-yellow-800': user.role.toLowerCase() === 'stagiaire'
                      }"
                    >
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <button 
                        @click="openModal(user)" 
                        class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        </svg>
                        Modifier
                      </button>
                      <button 
                        @click="deleteUser(user.id)" 
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

        <!-- Modale pour l'ajout/modification d'utilisateur -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <!-- En-tête de la modale -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
              <h3 class="text-xl font-bold text-gray-800">
                {{ editingId ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' }}
              </h3>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Formulaire -->
            <form @submit.prevent="submitForm" class="px-6 py-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                  <input 
                    v-model="form.nom" 
                    type="text"
                    placeholder="Nom de l'utilisateur"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                  <div v-if="form.errors.nom" class="mt-1 text-sm text-red-600">{{ form.errors.nom }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input 
                    v-model="form.email" 
                    type="email"
                    placeholder="adresse@email.com"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                  <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                  <select 
                    v-model="form.role" 
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin">Admin</option>
                    <option value="agent">Agent</option>
                    <option value="stagiaire">Stagiaire</option>
                  </select>
                  <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">{{ form.errors.role }}</div>
                </div>

                <div v-if="!editingId || form.password">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                  <input 
                    v-model="form.password" 
                    type="password"
                    placeholder="Minimum 8 caractères"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :required="!editingId"
                  />
                  <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
                </div>

                <div v-if="!editingId || form.password">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password"
                    placeholder="Confirmer le mot de passe"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :required="!editingId"
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
                  :disabled="form.processing"
                >
                  <span>{{ editingId ? 'Mettre à jour' : 'Ajouter' }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';

const props = defineProps({
  users: Array,
});

const showModal = ref(false);
const editingId = ref(null);

// Formulaire pour les données utilisateur
const form = useForm({
  nom: '',
  email: '',
  role: '',
  password: '',
  password_confirmation: '',
});

function openModal(user = null) {
  if (user) {
    // Mode modification
    editingId.value = user.id;
    form.nom = user.nom;
    form.email = user.email;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
  } else {
    // Mode création
    editingId.value = null;
    form.reset();
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  form.reset();
  editingId.value = null;
}

function submitForm() {
  if (editingId.value) {
    // Mise à jour d'un utilisateur existant
    form.put(route('users.update', editingId.value), {
      onSuccess: () => closeModal(),
    });
  } else {
    // Création d'un nouvel utilisateur
    form.post(route('users.store'), {
      onSuccess: () => closeModal(),
    });
  }
}

function deleteUser(id) {
  if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
    router.delete(route('users.destroy', id));
  }
}
</script>