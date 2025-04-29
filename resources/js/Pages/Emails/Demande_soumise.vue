<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import EmailSender from '@/Components/EmailSender.vue';

const props = defineProps(['auth', 'structures']);

const showModal = ref(false);
const showSuccess = ref(false);
const codeSuivi = ref('');
const demandeId = ref(null);

// Référence à l'email de l'utilisateur actuel pour l'envoi d'email
const userEmail = ref(props.auth.user.email);

// État de l'envoi d'email
const emailSent = ref(false);
const emailError = ref(null);

const form = useForm({
    stagiaire_id: props.auth.user.id,
    structure_id: '',
    nature: 'Individuel',
    lettre_cv_path: null,
});

const submitRequest = () => {
    form.post(route('demande_stages.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            showModal.value = false;
            showSuccess.value = true;
            
            // Récupérer le code de suivi et l'ID de la demande
            codeSuivi.value = response.props?.code_suivi || response.props?.flash?.code_suivi || 'Inconnu';
            demandeId.value = response.props?.demande_id || null;
            
            form.reset();
        },
        onError: (errors) => {
            console.error('Erreur lors de la soumission :', errors);
        },
    });
};

// Gérer l'événement d'envoi d'email réussi
const handleEmailSent = (data) => {
    emailSent.value = true;
    console.log('Email envoyé avec succès:', data);
};

// Gérer l'événement d'erreur d'envoi d'email
const handleEmailError = (error) => {
    emailError.value = error;
    console.error('Erreur lors de l\'envoi de l\'email:', error);
};

// Vérifier la configuration email
const checkEmailConfig = async () => {
    try {
        const response = await fetch('/api/emails/check-config');
        const data = await response.json();
        console.log('Configuration email:', data.config);
        return data.config;
    } catch (error) {
        console.error('Erreur lors de la vérification de la configuration email:', error);
        return null;
    }
};
</script>

<template>
    <!-- Bouton pour ouvrir la modale -->
    <button @click="showModal = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Soumettre une demande
    </button>

    <!-- Modale de succès -->
    <div v-if="showSuccess" class="mt-6 p-6 bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="mb-4 bg-green-100 border border-green-300 rounded p-4 text-green-800">
            <h3 class="text-lg font-bold mb-2">🎉 Demande soumise avec succès!</h3>
            <p><strong>Code de suivi :</strong> <span class="font-mono bg-green-50 px-2 py-1 rounded">{{ codeSuivi }}</span></p>
            <p class="text-sm mt-2">Ce code vous permettra de suivre l'état de votre demande.</p>
        </div>
        
        <!-- Composant d'envoi d'email -->
        <div v-if="demandeId" class="mt-6">
            <h4 class="text-lg font-semibold mb-2">Confirmation par email</h4>
            <EmailSender 
                :demande-id="demandeId" 
                :email-address="userEmail"
                :auto-send="false"
                button-text="Recevoir le code par email"
                @sent="handleEmailSent"
                @error="handleEmailError"
            >
                <p class="text-gray-600 mb-3">
                    Vous pouvez vous envoyer votre code de suivi par email pour le conserver.
                </p>
            </EmailSender>
        </div>
        
        <div class="mt-6 flex justify-between items-center border-t pt-4">
            <a href="#" class="text-blue-600 hover:text-blue-800" @click.prevent="showSuccess = false">
                Fermer
            </a>
            <a :href="route('mes.demandes')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-flex items-center">
                Voir mes demandes
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Ta modale de soumission ici... -->
</template>

<style scoped>
.font-mono {
    font-family: monospace;
}
</style>
