<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Access props from controller
const props = defineProps({
    stats: Object,
    error: {
        type: String,
        default: null
    }
});

// Map stats to our cards format
const statsCards = computed(() => [
    { title: 'Utilisateurs', count: props.stats.users, icon: 'users', color: 'blue', route: 'admin.users.index' },
    { title: 'Structures', count: props.stats.structures, icon: 'building', color: 'green', route: 'admin.structures.index' },
    { title: 'Stagiaires', count: props.stats.stagiaires, icon: 'user-graduate', color: 'purple', route: 'admin.stagiaires.index' },
    { title: 'Agents', count: props.stats.agents, icon: 'briefcase', color: 'orange', route: 'admin.agents.index' },
]);
</script>

<template>
    <Head title="Administration" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord d'administration
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Error Message -->
                <div v-if="error" class="mb-6 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <font-awesome-icon icon="exclamation-circle" class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Stats Cards -->
                    <div v-for="(stat, index) in statsCards" :key="index" 
                        class="overflow-hidden rounded-lg bg-white shadow transition-all hover:shadow-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div :class="`bg-${stat.color}-100 rounded-md p-3`">
                                        <font-awesome-icon :icon="['fas', stat.icon]" :class="`text-${stat.color}-600 text-xl`" />
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            {{ stat.title }}
                                        </dt>
                                        <dd>
                                            <div class="text-lg font-medium text-gray-900">{{ stat.count }}</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div :class="`bg-gray-50 px-5 py-3 text-right`">
                            <Link :href="route(stat.route)" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                Voir tout
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Recent Activities -->
                    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Activités récentes</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="divide-y divide-gray-200">
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                                <font-awesome-icon icon="user-plus" class="text-blue-600" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Nouveau stagiaire inscrit</p>
                                            <p class="text-sm text-gray-500">Il y a 2 heures</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                                                <font-awesome-icon icon="building" class="text-green-600" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Nouvelle structure ajoutée</p>
                                            <p class="text-sm text-gray-500">Hier à 14:30</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100">
                                                <font-awesome-icon icon="file-alt" class="text-yellow-600" />
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Rapport mensuel généré</p>
                                            <p class="text-sm text-gray-500">Il y a 2 jours</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Actions rapides</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <Link :href="route('admin.users.index')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <font-awesome-icon icon="user-plus" class="mr-2" />
                                    Ajouter un utilisateur
                                </Link>
                                <Link :href="route('admin.structures.index')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <font-awesome-icon icon="building" class="mr-2" />
                                    Ajouter une structure
                                </Link>
                                <Link :href="route('admin.stagiaires.index')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                    <font-awesome-icon icon="user-graduate" class="mr-2" />
                                    Voir les  stagiaires
                                </Link>
                                <Link :href="route('admin.agents.index')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                    <font-awesome-icon icon="briefcase" class="mr-2" />
                                    Ajouter un Agent 
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 