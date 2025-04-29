<script setup>
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

onMounted(() => {
    console.log('Dashboard component mounted');
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Message d'erreur -->
                <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    {{ $page.props.flash.error }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Bienvenue sur votre tableau de bord
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
