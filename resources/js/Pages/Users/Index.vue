<template>

  <Head title="Gestion des utilisateurs" />

  <SimpleLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Flash message -->
        <div v-if="flash" class="p-4 bg-green-100 border border-green-300 text-green-700 rounded">
          {{ flash }}
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
                <th class="border p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="border p-2">{{ user.nom }}</td>
                <td class="border p-2">{{ user.email }}</td>
                <td class="border p-2">{{ user.role }}</td>
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
              {{ editingId ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' }}
            </h3>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Nom -->
              <div>
                <label class="block font-medium">Nom</label>
                <input v-model="form.nom" class="w-full border rounded p-2" />
                <div v-if="form.errors.nom" class="text-red-600">{{ form.errors.nom }}</div>
              </div>

              <!-- Email -->
              <div>
                <label class="block font-medium">Email</label>
                <input type="email" v-model="form.email" class="w-full border rounded p-2" />
                <div v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</div>
              </div>

              <!-- Rôle -->
              <div>
                <label class="block font-medium">Rôle</label>
                <select v-model="form.role" class="w-full border rounded p-2">
                  <option value="université">université</option>
                  <option value="stagiaire">Stagiaire</option>
                  <option value="agent">Agent</option>
                </select>
                <div v-if="form.errors.role" class="text-red-600">{{ form.errors.role }}</div>
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
  </SimpleLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue'; // Layout sans navigation

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
  nom: '',
  email: '',
  role: '',
});

function openModal(user = null) {
  if (user) {
    form.nom = user.nom;
    form.email = user.email;
    form.role = user.role;
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
    form.put(route('users.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
}

function destroy(id) {
  if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
    router.delete(route('users.destroy', id));
  }
}
</script>
