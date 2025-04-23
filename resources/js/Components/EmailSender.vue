<template>
  <div class="email-sender">
    <transition name="fade">
      <div v-if="emailState === 'success'" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
        <CheckCircleIcon class="h-5 w-5 mr-2" />
        <span>L'email de confirmation a été envoyé avec succès.</span>
      </div>
      <div v-else-if="emailState === 'error'" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
        <ExclamationCircleIcon class="h-5 w-5 mr-2" />
        <span>{{ errorMessage }}</span>
        <button @click="sendEmail" class="ml-auto bg-red-200 hover:bg-red-300 text-red-800 px-2 py-1 rounded text-sm">
          Réessayer
        </button>
      </div>
      <div v-else-if="emailState === 'loading'" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 flex items-center">
        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Envoi de l'email en cours...</span>
      </div>
    </transition>

    <div v-if="emailState !== 'loading' && emailState !== 'success'" class="mt-4">
      <PrimaryButton @click="sendEmail" class="px-4 py-2">
        <EnvelopeIcon class="h-5 w-5 mr-2" />
        Envoyer l'email de confirmation
      </PrimaryButton>
    </div>

    <div v-if="codeSuivi" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
      <p class="text-sm text-gray-600 mb-2">Code de suivi de votre demande :</p>
      <p class="text-xl font-bold text-gray-800 bg-white px-4 py-2 rounded border border-yellow-300 inline-block tracking-wider">{{ codeSuivi }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { EnvelopeIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  demandeId: {
    type: Number,
    required: true
  },
  email: {
    type: String,
    required: true
  },
  includeMembers: {
    type: Boolean,
    default: true
  },
  autoSend: {
    type: Boolean,
    default: false
  }
});

const emailState = ref('idle'); // 'idle', 'loading', 'success', 'error'
const errorMessage = ref('');
const codeSuivi = ref('');

// Envoyer automatiquement si autoSend est true
if (props.autoSend) {
  sendEmail();
}

async function sendEmail() {
  if (emailState.value === 'loading') return;
  
  emailState.value = 'loading';
  errorMessage.value = '';
  
  try {
    const response = await axios.post('/api/emails/demande-confirmation', {
      demande_id: props.demandeId,
      email: props.email,
      include_members: props.includeMembers
    });
    
    if (response.data.success) {
      emailState.value = 'success';
      if (response.data.code_suivi) {
        codeSuivi.value = response.data.code_suivi;
      }
    } else {
      emailState.value = 'error';
      errorMessage.value = response.data.message || 'Une erreur est survenue lors de l\'envoi de l\'email.';
    }
  } catch (error) {
    emailState.value = 'error';
    errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de l\'envoi de l\'email.';
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 