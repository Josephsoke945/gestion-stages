<template>
    <Head title="Gestion des utilisateurs" />
  
    <AuthenticatedLayout>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
          <!-- Flash message -->
          <div v-if="flash" class="p-4 bg-green-100 border border-green-300 text-green-700 rounded">
            {{ flash }}
          </div>
  
          <!-- Bouton pour ouvrir le formulaire -->
          <div class="text-right">
            <button @click="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
              Ajouter un agent
            </button>
          </div>
  
          <!-- Liste des utilisateurs -->
          <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-4">Liste des utilisateurs</h3>
            <table class="w-full table-auto border-collapse">
              <thead>
                <tr class="bg-gray-100 text-left">
                  <th class="border p-2">Nom</th>
                  <th class="border p-2">Email</th>
                  <th class="border p-2">Rôle</th>
                  <th class="border p-2">Date de création</th>
                  <th class="border p-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                  <td class="border p-2">{{ user.nom }}</td>
                  <td class="border p-2">{{ user.email }}</td>
                  <td class="border p-2">{{ user.role }}</td>
                  <td class="border p-2">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                  <td class="border p-2 flex space-x-2">
                    <button @click="openModal(user)" class="text-blue-600 hover:underline">Modifier</button>
                    <button @click="destroy(user.id)" class="text-red-600 hover:underline">Supprimer</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Modal -->
          <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow max-w-lg w-full">
              <h3 class="text-lg font-bold mb-4">
                {{ editingId ? 'Modifier un agent' : 'Ajouter un agent' }}
              </h3>
  
              <form @submit.prevent="submit" class="space-y-4">
                <div>
                  <label class="block font-medium">Utilisateur</label>
                  <select v-model="form.user_id" class="w-full border rounded p-2">
                    <option value="">-- Sélectionner un utilisateur --</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.nom }}
                    </option>
                  </select>
                  <div v-if="form.errors.user_id" class="text-red-600">{{ form.errors.user_id }}</div>
                </div>
  
                <div>
                  <label class="block font-medium">Matricule</label>
                  <input v-model="form.matricule" class="w-full border rounded p-2" />
                  <div v-if="form.errors.matricule" class="text-red-600">{{ form.errors.matricule }}</div>
                </div>
  
                <div>
                  <label class="block font-medium">Fonction</label>
                  <input v-model="form.fonction" class="w-full border rounded p-2" />
                  <div v-if="form.errors.fonction" class="text-red-600">{{ form.errors.fonction }}</div>
                </div>
  
                <div>
                  <label class="block font-medium">Rôle</label>
                  <select v-model="form.role" class="w-full border rounded p-2">
                    <option value="">-- Sélectionner un rôle --</option>
                    <option value="admin">Admin</option>
                    <option value="stagiaire">Stagiaire</option>
                    <option value="agent">Agent</option>
                  </select>
                  <div v-if="form.errors.role" class="text-red-600">{{ form.errors.role }}</div>
                </div>
  
                <div>
                  <label class="block font-medium">Date d'embauche</label>
                  <input type="date" v-model="form.date_embauche" class="w-full border rounded p-2" />
                  <div v-if="form.errors.date_embauche" class="text-red-600">{{ form.errors.date_embauche }}</div>
                </div>
  
                <div class="flex justify-end space-x-2">
                  <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded">
                    Annuler
                  </button>
                  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    {{ editingId ? 'Mettre à jour' : 'Ajouter' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  import { Head, useForm, usePage, router } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  const props = defineProps({
    users: Array,
  });
  
  const page = usePage();
  const flash = ref(page.props.flash?.success || '');
  watch(() => page.props.flash, (newVal) => {
    flash.value = newVal?.success || '';
    if (flash.value) {
      setTimeout(() => (flash.value = ''), 4000);
    }
  });
  
  const showModal = ref(false);
  const editingId = ref(null);
  
  const form = useForm({
    user_id: '',
    matricule: '',
    fonction: '',
    role: '',
    date_embauche: '',
  });
  
  function openModal(user = null) {
    if (user) {
      form.user_id = user.id;
      form.matricule = user.matricule || '';
      form.fonction = user.fonction || '';
      form.role = user.role || '';
      form.date_embauche = user.date_embauche || '';
      editingId.value = user.id;
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
  
  function submit() {
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
    if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
      router.delete(route('agents.destroy', id));
    }
  }
  </script>
  