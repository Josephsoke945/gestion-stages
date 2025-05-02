<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const { mustVerifyEmail, status } = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const showSuccessMessage = ref(false);

const form = useForm({
    nom: user.nom,
    prenom: user.prenom,
    email: user.email,
    telephone: user.telephone,
    date_de_naissance: user.date_de_naissance,
    avatar: null,
});

const avatarPreview = ref(user.avatar ? `/storage/${user.avatar}` : null);
const isUploading = ref(false);

const selectNewAvatar = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const hasChanges = computed(() => {
    return form.nom !== user.nom ||
           form.prenom !== user.prenom ||
           form.email !== user.email ||
           form.telephone !== user.telephone ||
           form.date_de_naissance !== user.date_de_naissance;
});

const updateProfileInformation = async () => {
    isUploading.value = true;
    
    if (form.avatar instanceof File) {
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('avatar', form.avatar);
        formData.append('nom', form.nom);
        formData.append('prenom', form.prenom);
        formData.append('email', form.email);
        formData.append('telephone', form.telephone);
        formData.append('date_de_naissance', form.date_de_naissance);

        try {
            const response = await axios.post(route('profile.update'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            
            // Mise à jour de l'aperçu avec la nouvelle image
            if (response.data && response.data.avatar) {
                avatarPreview.value = `/storage/${response.data.avatar}`;
            }
            
            showSuccessMessage.value = true;
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 3000);
        } catch (error) {
            if (error.response?.data?.errors) {
                form.setError(error.response.data.errors);
            }
        }
    } else {
        await form.patch(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccessMessage.value = true;
                setTimeout(() => {
                    showSuccessMessage.value = false;
                }, 3000);
            },
        });
    }
    
    isUploading.value = false;
};
</script>

<template>
    <section class="bg-white rounded-lg shadow-sm p-6 transition-all duration-300 hover:shadow-md">
        <header class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Informations du profil</h2>
            <p class="text-gray-600 max-w-lg mx-auto">
                Mettez à jour les informations de votre profil et votre adresse email.
            </p>
        </header>

        <form @submit.prevent="updateProfileInformation" class="space-y-6 max-w-2xl mx-auto">
            <!-- Avatar -->
            <div class="flex flex-col items-center space-y-4">
                <div class="relative">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center border-4 border-white shadow-lg relative">
                        <img v-if="avatarPreview" :src="avatarPreview" class="w-full h-full object-cover" alt="Avatar" />
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        
                        <!-- Overlay de chargement -->
                        <div v-if="isUploading" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        <input
                            type="file"
                            id="avatar"
                            class="hidden"
                            accept="image/*"
                            @change="selectNewAvatar"
                        />
                    </label>
                </div>
                <p class="text-sm text-gray-500">Cliquez sur l'icône pour modifier votre photo de profil</p>
            </div>

            <!-- Message de succès -->
            <transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <p v-if="showSuccessMessage" class="text-sm text-green-600 mt-2 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Modifications enregistrées avec succès !
                </p>
            </transition>

            <!-- Informations personnelles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <InputLabel for="nom" value="Nom" class="text-gray-700" />
                    <div class="relative">
                <TextInput
                            id="nom"
                    type="text"
                            v-model="form.nom"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    required
                            placeholder="Votre nom"
                        />
                        <div v-if="form.nom" class="absolute right-3 top-2.5 text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <InputError :message="form.errors.nom" class="mt-1" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="prenom" value="Prénom" class="text-gray-700" />
                    <div class="relative">
                        <TextInput
                            id="prenom"
                            type="text"
                            v-model="form.prenom"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            required
                            placeholder="Votre prénom"
                        />
                        <div v-if="form.prenom" class="absolute right-3 top-2.5 text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <InputError :message="form.errors.prenom" class="mt-1" />
                </div>
            </div>

            <!-- Contact -->
            <div class="space-y-2">
                <InputLabel for="email" value="Email" class="text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        required
                        placeholder="votre@email.com"
                    />
                </div>
                <InputError :message="form.errors.email" class="mt-1" />
            </div>

            <div class="space-y-2">
                <InputLabel for="telephone" value="Téléphone" class="text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                    </div>
                    <TextInput
                        id="telephone"
                        type="tel"
                        v-model="form.telephone"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    required
                        pattern="[0-9]{10}"
                        placeholder="0123456789"
                    />
                </div>
                <InputError :message="form.errors.telephone" class="mt-1" />
            </div>

            <div class="space-y-2">
                <InputLabel for="date_de_naissance" value="Date de naissance" class="text-gray-700" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <TextInput
                        id="date_de_naissance"
                        type="date"
                        v-model="form.date_de_naissance"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        required
                    />
                </div>
                <InputError :message="form.errors.date_de_naissance" class="mt-1" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="bg-yellow-50 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Votre adresse email n'est pas vérifiée.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                                class="font-medium text-yellow-700 underline hover:text-yellow-600"
                    >
                                Cliquez ici pour renvoyer l'email de vérification.
                    </Link>
                </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing || !hasChanges"
                >
                    <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                    <span v-else>Sauvegarder</span>
                </PrimaryButton>
            </div>
        </form>
    </section>
</template>

<style scoped>
.form-input:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
</style>
