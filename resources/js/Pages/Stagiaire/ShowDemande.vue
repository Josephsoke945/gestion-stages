<script setup>
import { Head } from '@inertiajs/vue3';
import Stagiaire from '@/Layouts/Stagiaire.vue';

defineProps({
  demande: Object
});

// Fonction pour formater une date
const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('fr-FR');
};

// Fonction pour obtenir une couleur selon le statut
const getStatusColor = (statut) => {
  switch (statut) {
    case 'En attente':
      return 'text-yellow-600 bg-yellow-100';
    case 'Acceptée':
      return 'text-green-600 bg-green-100';
    case 'Refusée':
      return 'text-red-600 bg-red-100';
    default:
      return 'text-gray-600 bg-gray-100';
  }
};
</script>

<template>
  <Head title="Détails de la demande" />
  <Stagiaire>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Détails de la demande</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="mb-6 flex justify-between items-center">
              <h1 class="text-2xl font-bold text-gray-800">Détails de la demande #{{ demande.code_suivi }}</h1>
              <a :href="route('mes.demandes')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Retour
              </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Informations sur la demande -->
              <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informations de la demande</h2>
                
                <div class="space-y-3">
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Code de suivi</span>
                    <span class="font-mono text-lg font-medium">{{ demande.code_suivi }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Statut</span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block w-fit" :class="getStatusColor(demande.statut)">
                      {{ demande.statut }}
                    </span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Structure</span>
                    <span class="font-medium">{{ demande.structure?.libelle || '-' }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Type de stage</span>
                    <span class="font-medium">{{ demande.type }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Nature</span>
                    <span class="font-medium">{{ demande.nature }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Période</span>
                    <span class="font-medium">Du {{ formatDate(demande.date_debut) }} au {{ formatDate(demande.date_fin) }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Date de soumission</span>
                    <span class="font-medium">{{ formatDate(demande.date_soumission) }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Informations du stagiaire -->
              <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informations du stagiaire</h2>
                
                <div class="space-y-3">
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Nom complet</span>
                    <span class="font-medium">{{ demande.stagiaire?.user?.nom }} {{ demande.stagiaire?.user?.prenom }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="font-medium">{{ demande.stagiaire?.user?.email }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Téléphone</span>
                    <span class="font-medium">{{ demande.stagiaire?.user?.telephone }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Université</span>
                    <span class="font-medium">{{ demande.stagiaire?.universite }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Filière</span>
                    <span class="font-medium">{{ demande.stagiaire?.filiere }}</span>
                  </div>
                  
                  <div class="flex flex-col">
                    <span class="text-sm text-gray-500">Niveau d'étude</span>
                    <span class="font-medium">{{ demande.stagiaire?.niveau_etude }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Membres du groupe (si applicable) -->
            <div v-if="demande.nature === 'Groupe' && demande.membres && demande.membres.length > 0" class="mt-6 bg-gray-50 p-6 rounded-lg shadow-sm">
              <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Membres du groupe</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="membre in demande.membres" :key="membre.id" class="bg-white p-4 rounded border">
                  <div class="font-medium">{{ membre.user?.nom }} {{ membre.user?.prenom }}</div>
                  <div class="text-sm text-gray-600">{{ membre.user?.email }}</div>
                </div>
              </div>
            </div>
            
            <!-- Documents attachés -->
            <div v-if="demande.lettre_cv_path || demande.diplomes_path" class="mt-6 bg-gray-50 p-6 rounded-lg shadow-sm">
              <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Documents attachés</h2>
              
              <div class="space-y-4">
                <!-- Documents pour demande académique -->
                <div v-if="demande.type === 'Académique' && demande.lettre_cv_path" class="flex items-center justify-between bg-white p-4 rounded-lg border">
                  <div>
                    <h3 class="font-medium text-gray-900">Lettre de recommandation</h3>
                    <p class="text-sm text-gray-500">Document de recommandation académique</p>
                  </div>
                  <a :href="'/storage/' + demande.lettre_cv_path" target="_blank" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Télécharger la lettre
                  </a>
                </div>

                <!-- Documents pour demande professionnelle -->
                <template v-if="demande.type === 'Professionnelle'">
                  <!-- CV et Lettre de motivation -->
                  <div v-if="demande.lettre_cv_path" class="flex items-center justify-between bg-white p-4 rounded-lg border">
                    <div>
                      <h3 class="font-medium text-gray-900">CV et Lettre de motivation</h3>
                      <p class="text-sm text-gray-500">Documents principaux de la candidature</p>
                    </div>
                    <a :href="'/storage/' + demande.lettre_cv_path" target="_blank" 
                      class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                      Télécharger CV et Lettre
                    </a>
                  </div>

                  <!-- Diplômes -->
                  <div v-if="demande.diplomes_path" class="flex items-center justify-between bg-white p-4 rounded-lg border">
                    <div>
                      <h3 class="font-medium text-gray-900">Diplômes</h3>
                      <p class="text-sm text-gray-500">Documents justificatifs des diplômes</p>
                    </div>
                    <a :href="'/storage/' + demande.diplomes_path" target="_blank" 
                      class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                      Télécharger les diplômes
                    </a>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Stagiaire>
</template> 