<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Stagiaire from '@/Layouts/Stagiaire.vue';

const props = defineProps({
  errors: Object,
});

const form = useForm({
  code_suivi: '',
});

const isSubmitting = ref(false);

const submit = () => {
  isSubmitting.value = true;
  form.post(route('demandes.search'), {
    preserveScroll: true,
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
};
</script>

<template>
  <Head title="Recherche de demande" />
  <Stagiaire>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Recherche de demande</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">Rechercher une demande par code de suivi</h1>
            
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label for="code_suivi" class="block text-sm font-medium text-gray-700">Code de suivi</label>
                <div class="mt-1">
                  <input
                    id="code_suivi"
                    v-model="form.code_suivi"
                    type="text"
                    required
                    placeholder="Entrez le code de suivi (ex: AB12CD34)"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                </div>
                <p v-if="errors && errors.code_suivi" class="mt-2 text-sm text-red-600">{{ errors.code_suivi }}</p>
                <p v-else class="mt-2 text-sm text-gray-500">
                  Entrez le code de suivi qui vous a été communiqué par email.
                </p>
              </div>

              <div>
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isSubmitting ? 'Recherche en cours...' : 'Rechercher' }}
                </button>
              </div>
            </form>

            <div class="mt-8 border-t border-gray-200 pt-6">
              <h2 class="text-lg font-semibold text-gray-900">Besoin d'aide ?</h2>
              <p class="mt-2 text-sm text-gray-600">
                Le code de suivi est un identifiant unique qui vous a été envoyé par email lors de la soumission de votre demande.
                Il vous permet de retrouver facilement votre demande et de consulter son statut. 
              </p>
              <p class="mt-2 text-sm text-gray-600">
                Si vous avez perdu votre code de suivi, veuillez contacter le service des stages à l'adresse suivante : 
                <a href="mailto:stages@example.com" class="text-indigo-600 hover:text-indigo-500">stages@example.com</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Stagiaire>
</template> 