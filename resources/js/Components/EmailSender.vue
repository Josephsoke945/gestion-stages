<template>
    <div>
        <div v-if="showSendEmailOption" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Envoyer un email de confirmation</h3>
                    <p class="text-sm text-gray-600" v-if="!emailSent">
                        Envoyez un email de confirmation avec le code de suivi pour cette demande.
                        <span v-if="hasGroupMembers" class="font-medium text-indigo-600">
                            L'email sera envoyé à tous les membres du groupe.
                        </span>
                    </p>
                    <p v-else-if="emailSent && !error" class="text-sm text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Email de confirmation envoyé avec succès !
                    </p>
                    <p v-else-if="error" class="text-sm text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ error }}
                    </p>
                </div>
                <div v-if="!emailSent" class="ml-4">
                    <button
                        @click="sendEmail"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        :disabled="loading"
                    >
                        <span v-if="loading" class="mr-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ loading ? 'Envoi en cours...' : 'Envoyer' }}
                    </button>
                </div>
                <div v-else-if="!error" class="ml-4">
                    <button
                        @click="emailSent = false"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Renvoyer
                    </button>
                </div>
                <div v-else class="ml-4">
                    <button
                        @click="retryEmail"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Réessayer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    demandeId: {
        type: [Number, String],
        required: true
    },
    email: {
        type: String,
        required: true
    },
    hasGroupMembers: {
        type: Boolean,
        default: false
    }
});

const loading = ref(false);
const emailSent = ref(false);
const error = ref(null);
// La case est automatiquement activée si le groupe a des membres
const sendToMembers = ref(props.hasGroupMembers);

// Réactiver si hasGroupMembers change
watch(() => props.hasGroupMembers, (newValue) => {
    sendToMembers.value = newValue;
});

const showSendEmailOption = computed(() => {
    return props.demandeId && props.email;
});

const emit = defineEmits(['email-sent', 'email-error']);

const sendEmail = async () => {
    loading.value = true;
    error.value = null;

    try {
        console.log('Tentative d\'envoi d\'email pour la demande:', props.demandeId);
        
        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('Token CSRF non trouvé. Veuillez recharger la page et réessayer.');
        }
        
        // Configurer les headers pour inclure le token CSRF
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        
        // Toujours envoyer aux membres du groupe si disponible
        const shouldSendToMembers = props.hasGroupMembers;
        
        const response = await axios.post('/api/emails/demande-confirmation', {
            demande_id: props.demandeId,
            email: props.email,
            send_to_members: shouldSendToMembers
        });

        if (response.data.success) {
            emailSent.value = true;
            emit('email-sent', response.data);
            
            // Afficher des informations de diagnostic
            console.log('Email envoyé avec succès', response.data);
            
            // Vérifier la configuration email
            try {
                const configResponse = await axios.get('/api/emails/check-config');
                console.log('Configuration email:', configResponse.data.config);
            } catch (configError) {
                console.warn('Erreur lors de la vérification de la configuration:', configError);
            }
        } else {
            error.value = response.data.message || 'Une erreur est survenue lors de l\'envoi de l\'email.';
            emit('email-error', error.value);
        }
    } catch (err) {
        console.error('Erreur détaillée:', err);
        error.value = err.response?.data?.message || 'Une erreur est survenue lors de l\'envoi de l\'email.';
        emit('email-error', error.value);
    } finally {
        loading.value = false;
    }
};

const retryEmail = () => {
    error.value = null;
    emailSent.value = false;
    sendEmail();
};
</script>

<style scoped>
.email-sender {
  margin-bottom: 1rem;
}
</style> 