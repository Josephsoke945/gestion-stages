<template>
  <Head title="Liste des Stagiaires" />

  <Admin>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête avec titre -->
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-800">Liste des Stagiaires</h1>
        </div>

        <!-- Liste des stagiaires -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Stagiaires</h2>
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
            <p class="text-gray-500 text-lg">Aucun stagiaire trouvé</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left">
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Nom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Prénom</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Email</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700">Téléphone</th>
                  <th class="px-6 py-3 border-b border-gray-200 font-medium text-gray-700 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stagiaire in stagiaires" :key="stagiaire.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.nom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.prenom ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.email ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">{{ stagiaire.user?.telephone ?? '-' }}</td>
                  <td class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-center space-x-3">
                      <Link
                        :href="route('agent.stagiaires.show', stagiaire.id)"
                        class="text-blue-600 hover:text-blue-800"
                      >
                        Voir détails
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </Admin>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Admin from '@/Layouts/Admin.vue';

defineProps({
  stagiaires: {
    type: Array,
    required: true
  }
});
</script> 