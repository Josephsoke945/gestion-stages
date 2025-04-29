<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  demandeId: {
    type: Number,
    required: true
  }
});

const sending = ref(false);
const sent = ref(false);
const error = ref(null);

// Fonction pour envoyer l'email via l'API
const sendConfirmationEmail = async () => {
  if (sending.value || sent.value) return;
  
  sending.value = true;
  error.value = null;
  
  try {
    const response = await axios.post('/api/emails/demande-confirmation', {
      demande_id: props.demandeId
    });
    
    if (response.data.success) {
      sent.value = true;
      console.log('Email de confirmation envoyé avec succès');
    } else {
      error.value = response.data.message || 'Erreur lors de l\'envoi de l\'email';
      console.error('Erreur:', error.value);
    }
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Erreur lors de l\'envoi de l\'email';
    console.error('Erreur:', error.value);
  } finally {
    sending.value = false;
  }
};

// Envoyer automatiquement l'email dès que le composant est monté
onMounted(() => {
  if (props.demandeId) {
    sendConfirmationEmail();
  }
});
</script>

<template>
  <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-md">
    <p>{{ error }}</p>
    <button 
      @click="sendConfirmationEmail" 
      class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 disabled:opacity-50"
      :disabled="sending"
    >
      {{ sending ? 'Envoi en cours...' : 'Réessayer' }}
    </button>
  </div>
  
  <div v-else-if="sent" class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
    <p>Email de confirmation envoyé avec succès.</p>
  </div>
  
  <div v-else-if="sending" class="mt-4 p-4 bg-blue-100 text-blue-700 rounded-md">
    <p>Envoi de l'email de confirmation en cours...</p>
  </div>
</template> 