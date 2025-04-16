<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['auth', 'structures']);

const showModal = ref(false);
const showSuccess = ref(false);
const codeSuivi = ref('');

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
            codeSuivi.value = response.props?.code_suivi || 'Inconnu';
            form.reset();
        },
        onError: (errors) => {
            console.error('Erreur lors de la soumission :', errors);
        },
    });
};
</script>

<template>
    <!-- Bouton pour ouvrir la modale -->
    <button @click="showModal = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Soumettre une demande
    </button>

    <!-- Modale de succès -->
    <div v-if="showSuccess" class="mt-6 p-4 bg-green-100 border border-green-300 rounded text-green-800">
        🎉 Votre demande a été soumise avec succès !<br />
        🔐 <strong>Code de suivi :</strong> <span class="font-mono">{{ codeSuivi }}</span><br />
        📧 Un email vous a également été envoyé avec ce code.
    </div>

    <!-- Ta modale de soumission ici... -->
</template>
