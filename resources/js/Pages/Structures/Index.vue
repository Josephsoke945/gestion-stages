<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue'; // Layout sans navigation

const props = defineProps({
  structures: Array,
  users: Array,
});

const page = usePage();
const flash = ref(page.props.flash?.success || '');
watch(() => page.props.flash, (newVal) => {
  flash.value = newVal?.success || '';
  if (flash.value) {
    setTimeout(() => flash.value = '', 4000);
  }
});

const showModal = ref(false);
const editingId = ref(null);

const form = useForm({
  sigle: '',
  libelle: '',
  responsable_id: '',
  description: '',
});

function openModal(structure = null) {
  if (structure) {
    form.sigle = structure.sigle;
    form.libelle = structure.libelle;
    form.responsable_id = structure.responsable_id;
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

function submit() {
  if (editingId.value) {
    form.put(route('structures.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('structures.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
}

function destroy(id) {
  if (confirm('Voulez-vous vraiment supprimer cette structure ?')) {
    router.delete(route('structures.destroy', id));
  }
}
</script>

<template>
  <Head title="Structures" />

  <SimpleLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <!-- Flash message -->
        <div v-if="flash" class="p-4 bg-green-100 border border-green-300 text-green-700 rounded">
          {{ flash }}
        </div>

        <!-- Bouton pour ouvrir le formulaire -->
        <div class="text-right">
          <button @click="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Ajouter une structure
          </button>
        </div>

        <!-- Liste des structures -->
        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-lg font-bold mb-4">Liste des structures</h3>
          <table class="w-full table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100 text-left">
                <th class="border p-2">Sigle</th>
                <th class="border p-2">Libellé</th>
                <th class="border p-2">Responsable</th>
                <th class="border p-2">Description</th>
                <th class="border p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="structure in props.structures" :key="structure.id" class="hover:bg-gray-50">
                <td class="border p-2">{{ structure.sigle }}</td>
                <td class="border p-2">{{ structure.libelle }}</td>
                <td class="border p-2">
                  {{ structure.responsable?.nom ?? '-' }}
                </td>
                <td class="border p-2">{{ structure.description ?? '-' }}</td>
                <td class="border p-2 flex space-x-2">
                  <button @click="openModal(structure)" class="text-blue-600 hover:underline">Modifier</button>
                  <button @click="destroy(structure.id)" class="text-red-600 hover:underline">Supprimer</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow max-w-lg w-full">
            <h3 class="text-lg font-bold mb-4">
              {{ editingId ? 'Modifier une structure' : 'Ajouter une structure' }}
            </h3>

            <form @submit.prevent="submit" class="space-y-4">
              <div>
                <label class="block font-medium">Sigle</label>
                <input v-model="form.sigle" class="w-full border rounded p-2" />
                <div v-if="form.errors.sigle" class="text-red-600">{{ form.errors.sigle }}</div>
              </div>

              <div>
                <label class="block font-medium">Libellé</label>
                <input v-model="form.libelle" class="w-full border rounded p-2" />
                <div v-if="form.errors.libelle" class="text-red-600">{{ form.errors.libelle }}</div>
              </div>

              <div>
                <label class="block font-medium">Responsable</label>
                <select v-model="form.responsable_id" class="w-full border rounded p-2">
                  <option value="">-- Sélectionner un responsable --</option>
                  <option v-for="user in props.users" :key="user.id" :value="user.id">
                    {{ user.nom }}
                  </option>
                </select>
                <div v-if="form.errors.responsable_id" class="text-red-600">{{ form.errors.responsable_id }}</div>
              </div>

              <div>
                <label class="block font-medium">Description</label>
                <textarea v-model="form.description" class="w-full border rounded p-2"></textarea>
                <div v-if="form.errors.description" class="text-red-600">{{ form.errors.description }}</div>
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