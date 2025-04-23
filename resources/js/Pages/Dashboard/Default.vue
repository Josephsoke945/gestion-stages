<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, onMounted } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

// Redirect admin users to admin dashboard
onMounted(() => {
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-bold mb-4">Bienvenue sur le tableau de bord</h1>
                        <p>Vous êtes connecté en tant que {{ $page.props.auth.user.role }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>