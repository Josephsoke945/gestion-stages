<template>
  <div class="email-sender">
    <div v-if="isLoading" class="flex items-center justify-center">
      <svg class="animate-spin h-5 w-5 mr-3 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Envoi de l'email en cours...
    </div>
    
    <div v-if="emailSent" class="bg-green-100 text-green-800 p-3 rounded-md flex items-start mb-4">
      <svg class="h-5 w-5 mr-2 text-green-600 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <div>
        <p class="font-semibold">Email envoyé avec succès!</p>
        <p class="text-sm">Un email de confirmation avec votre code de suivi a été envoyé à {{ emailAddress }}.</p>
      </div>
    </div>
    
    <div v-if="error" class="bg-red-100 text-red-800 p-3 rounded-md flex items-start mb-4">
      <svg class="h-5 w-5 mr-2 text-red-600 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <div>
        <p class="font-semibold">Erreur lors de l'envoi de l'email</p>
        <p class="text-sm">{{ error }}</p>
        <button 
          @click="sendEmail" 
          class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium"
        >
          Réessayer
        </button>
      </div>
    </div>
    
    <slot v-if="!isLoading && !emailSent"></slot>
    
    <div v-if="showManualButton && !emailSent" class="mt-4">
      <button 
        @click="sendEmail" 
        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        :disabled="isLoading"
      >
        <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ buttonText }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import axios from 'axios';

const props = defineProps({
  demandeId: {
    type: [Number, String],
    required: true
  },
  emailAddress: {
    type: String,
    required: true
  },
  membres: {
    type: Array,
    default: () => []
  },
  autoSend: {
    type: Boolean,
    default: false
  },
  showManualButton: {
    type: Boolean,
    default: true
  },
  buttonText: {
    type: String,
    default: "Envoyer l'email de confirmation"
  }
});

const emit = defineEmits(['sent', 'error']);

const isLoading = ref(false);
const emailSent = ref(false);
const error = ref(null);

// Si autoSend est activé, envoyer l'email automatiquement
watchEffect(() => {
  if (props.autoSend && props.demandeId && !emailSent.value) {
    sendEmail();
  }
});

const sendEmail = async () => {
  if (isLoading.value) return;
  
  isLoading.value = true;
  error.value = null;
  
  try {
    // Récupérer le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
      throw new Error('Token CSRF non trouvé. Veuillez recharger la page et réessayer.');
    }
    
    // Configurer les headers pour inclure le token CSRF
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    
    const response = await axios.post('/api/emails/demande-confirmation', {
      demande_id: props.demandeId,
      email: props.emailAddress,
      send_to_membres: props.membres.length > 0
    });
    
    if (response.data && response.data.success) {
      emailSent.value = true;
      emit('sent', response.data);
    } else {
      throw new Error(response.data?.message || 'Une erreur inattendue est survenue');
    }
    
  } catch (err) {
    console.error('Erreur lors de l\'envoi de l\'email:', err);
    error.value = err.response?.data?.message || err.message || 'Une erreur est survenue lors de l\'envoi de l\'email.';
    emit('error', error.value);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.email-sender {
  margin-bottom: 1rem;
}
</style> 