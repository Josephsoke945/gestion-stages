<script setup>
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

defineProps({
    error: {
        type: String,
        required: false
    }
});

// Redirect admin users to admin dashboard
onMounted(() => {
    console.log('Default Dashboard mounted');
    // Check if the user is authenticated and has a role
    if (window.$page && window.$page.props && window.$page.props.auth && window.$page.props.auth.user) {
        const userRole = window.$page.props.auth.user.role;
        if (userRole === 'admin') {
            router.visit(route('admin.dashboard'));
        }
    }
});
</script>

<template>
    <Head title="Tableau de bord" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Message d'erreur -->
                <div v-if="error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    {{ error }}
                </div>

                <!-- Message flash -->
                <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h1 class="text-2xl font-bold mb-4">Bienvenue sur le tableau de bord</h1>
                        <p>Vous êtes connecté en tant que {{ $page.props.auth.user.role }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>