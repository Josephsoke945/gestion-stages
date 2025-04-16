<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';

const props = defineProps({
  stagiaires: Array,
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
const step = ref(1);

const form = useForm({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  date_de_naissance: '',
  sexe: '',
  niveau_etude: '',
  universite: '',
  filiere: '',
});

function openModal(stagiaire = null) {
  step.value = 1;
  if (stagiaire) {
    form.nom = stagiaire.user?.nom || '';
    form.prenom = stagiaire.user?.prenom || '';
    form.email = stagiaire.user?.email || '';
    form.telephone = stagiaire.user?.telephone || '';
    form.date_de_naissance = stagiaire.user?.date_de_naissance || '';
    form.sexe = stagiaire.user?.sexe || '';
    form.niveau_etude = stagiaire.niveau_etude;
    form.universite = stagiaire.universite;
    form.filiere = stagiaire.filiere;
    editingId.value = stagiaire.id;
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
}

function submit() {
  if (editingId.value) {
    form.put(route('stagiaires.update', editingId.value), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('stagiaires.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
}

function destroy(id) {
  if (confirm('Voulez-vous vraiment supprimer ce stagiaire ?')) {
    router.delete(route('stagiaires.destroy', id));
  }
}
</script>

<template>
  <Head title="Stagiaires" />
  <SimpleLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <!-- Flash message -->
        <div v-if="flash" class="p-4 bg-green-100 border border-green-300 text-green-700 rounded">
          {{ flash }}
        </div>

        <!-- Bouton d'ajout -->
        <div class="text-right">
          <button @click="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Ajouter un stagiaire
          </button>
        </div>

        <!-- Liste des stagiaires -->
        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-lg font-bold mb-4">Liste des stagiaires</h3>
          <table class="w-full table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100 text-left">
                <th class="border p-2">Nom</th>
                <th class="border p-2">Prénom</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Téléphone</th>
                <th class="border p-2">Niveau</th>
                <th class="border p-2">Université</th>
                <th class="border p-2">Filière</th>
                <th class="border p-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stagiaire in stagiaires.filter(s => s.user?.role === 'stagiaire')" :key="stagiaire.id" class="hover:bg-gray-50">
                <td class="border p-2">{{ stagiaire.user?.nom }}</td>
                <td class="border p-2">{{ stagiaire.user?.prenom }}</td>
                <td class="border p-2">{{ stagiaire.user?.email }}</td>
                <td class="border p-2">{{ stagiaire.user?.telephone }}</td>
                <td class="border p-2">{{ stagiaire.niveau_etude }}</td>
                <td class="border p-2">{{ stagiaire.universite }}</td>
                <td class="border p-2">{{ stagiaire.filiere }}</td>
                <td class="border p-2 flex space-x-2">
                  <button @click="openModal(stagiaire)" class="text-blue-600 hover:underline">Modifier</button>
                  <button @click="destroy(stagiaire.id)" class="text-red-600 hover:underline">Supprimer</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modale -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow max-w-lg w-full">
            <h3 class="text-lg font-bold mb-4">
              {{ editingId ? 'Modifier un agent' : 'Ajouter un agent' }}
            </h3>

            <div class="mb-4">
              <div class="text-sm text-gray-600 mb-1">Étape {{ step }} sur 2</div>
              <div class="w-full h-2 bg-gray-200 rounded-full">
                <div class="h-full bg-blue-500 transition-all" :style="{ width: step === 1 ? '50%' : '100%' }"></div>
              </div>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Étape 1 -->
              <div v-if="step === 1" class="space-y-4">
                <div>
                  <label class="block font-medium">Nom</label>
                  <input v-model="form.nom" class="w-full border rounded p-2" />
                  <div v-if="form.errors.nom" class="text-red-600">{{ form.errors.nom }}</div>
                </div>
                <div>
                  <label class="block font-medium">Prénom</label>
                  <input v-model="form.prenom" class="w-full border rounded p-2" />
                  <div v-if="form.errors.prenom" class="text-red-600">{{ form.errors.prenom }}</div>
                </div>
                <div>
                  <label class="block font-medium">Email</label>
                  <input type="email" v-model="form.email" class="w-full border rounded p-2" />
                  <div v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</div>
                </div>
                <div>
                  <label class="block font-medium">Téléphone</label>
                  <input v-model="form.telephone" class="w-full border rounded p-2" />
                  <div v-if="form.errors.telephone" class="text-red-600">{{ form.errors.telephone }}</div>
                </div>
                <div>
                  <label class="block font-medium">Date de naissance</label>
                  <input type="date" v-model="form.date_de_naissance" class="w-full border rounded p-2" />
                  <div v-if="form.errors.date_de_naissance" class="text-red-600">{{ form.errors.date_de_naissance }}</div>
                </div>
                <div>
                  <label class="block font-medium">Sexe</label>
                  <select v-model="form.sexe" class="w-full border rounded p-2">
                    <option value="">Sélectionner</option>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                  </select>
                  <div v-if="form.errors.sexe" class="text-red-600">{{ form.errors.sexe }}</div>
                </div>
              </div>

              <!-- Étape 2 -->
              <div v-if="step === 2" class="space-y-4">
                <div>
                  <label class="block font-medium">Niveau d'étude</label>
                  <select v-model="form.niveau_etude" class="w-full border rounded p-2">
                    <option value="">Sélectionner</option>
                    <option value="Licence 1">Licence 1</option>
                    <option value="Licence 2">Licence 2</option>
                    <option value="Licence 3">Licence 3</option>
                    <option value="Master 1">Master 1</option>
                    <option value="Master 2">Master 2</option>
                    <option value="Autre">Autre</option>
                  </select>
                  <div v-if="form.errors.niveau_etude" class="text-red-600">{{ form.errors.niveau_etude }}</div>
                </div>
                <div>
                  <label class="block font-medium">Université</label>
                  <input v-model="form.universite" class="w-full border rounded p-2" />
                  <div v-if="form.errors.universite" class="text-red-600">{{ form.errors.universite }}</div>
                </div>
                <div>
                  <label class="block font-medium">Filière</label>
                  <input v-model="form.filiere" class="w-full border rounded p-2" />
                  <div v-if="form.errors.filiere" class="text-red-600">{{ form.errors.filiere }}</div>
                </div>
              </div>

              <div class="flex justify-between">
                <button type="button" @click="step > 1 ? step-- : closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">
                  {{ step > 1 ? 'Précédent' : 'Annuler' }}
                </button>
                <button v-if="step < 2" type="button" @click="step++" class="px-4 py-2 bg-blue-500 text-white rounded">
                  Suivant
                </button>
                <button v-else type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                  {{ editingId ? 'Ajouter ':'Mettre à jour'  }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </SimpleLayout>
</template>