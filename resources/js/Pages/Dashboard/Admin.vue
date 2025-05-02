<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Access props from controller
const props = defineProps({
    stats: Object,
    error: {
        type: String,
        default: null
    },
    recentActivities: {
        type: Array,
        default: () => []
    }
});

// Map stats to our cards format with enhanced visual elements and trend indicators
const statsCards = computed(() => [
    { 
        title: 'Utilisateurs', 
        count: props.stats.users, 
        icon: 'users', 
        color: 'blue', 
        gradient: 'from-blue-400 to-blue-600',
        route: 'admin.users.index',
        trend: 5, // Example: 5% increase
        trendUp: true
    },
    { 
        title: 'Structures', 
        count: props.stats.structures, 
        icon: 'building', 
        color: 'emerald', 
        gradient: 'from-emerald-400 to-emerald-600',
        route: 'admin.structures.index',
        trend: 2, // Example: 2% increase
        trendUp: true
    },
    { 
        title: 'Stagiaires', 
        count: props.stats.stagiaires, 
        icon: 'user-graduate', 
        color: 'violet', 
        gradient: 'from-violet-400 to-violet-600',
        route: 'admin.stagiaires.index',
        trend: 8, // Example: 8% increase
        trendUp: true
    },
    { 
        title: 'Agents', 
        count: props.stats.agents, 
        icon: 'briefcase', 
        color: 'amber', 
        gradient: 'from-amber-400 to-amber-600',
        route: 'admin.agents.index',
        trend: 1, // Example: 1% decrease
        trendUp: false
    },
]);

// Quick actions with enhanced visual styling and organized by categories
const quickActions = [
    { 
        title: 'Ajouter un utilisateur', 
        route: 'admin.users.index', 
        icon: 'user-plus', 
        color: 'blue',
        description: 'Créer un nouveau compte utilisateur',
        category: 'users'
    },
    { 
        title: 'Ajouter une structure', 
        route: 'admin.structures.index', 
        icon: 'building', 
        color: 'emerald',
        description: 'Enregistrer une nouvelle structure',
        category: 'structures'
    },
    { 
        title: 'Voir les stagiaires', 
        route: 'admin.stagiaires.index', 
        icon: 'user-graduate', 
        color: 'violet',
        description: 'Accéder à la liste des stagiaires',
        category: 'stagiaires'
    },
    { 
        title: 'Ajouter un Agent', 
        route: 'admin.agents.index', 
        icon: 'briefcase', 
        color: 'amber',
        description: 'Enregistrer un nouvel agent',
        category: 'agents'
    },
    {
        title: 'Télécharger le rapport mensuel',
        route: 'admin.rapport-mensuel',
        icon: 'file-download',
        color: 'indigo',
        description: 'Exporter le rapport mensuel des activités',
        download: true,
        category: 'reports'
    }
];

// Activity icon mapping with improved color scheme
const activityIconMap = {
    'stagiaire': { icon: 'user-graduate', color: 'blue', gradient: 'from-blue-400 to-blue-600' },
    'structure': { icon: 'building', color: 'emerald', gradient: 'from-emerald-400 to-emerald-600' },
    'agent': { icon: 'briefcase', color: 'amber', gradient: 'from-amber-400 to-amber-600' },
    'user': { icon: 'users', color: 'violet', gradient: 'from-violet-400 to-violet-600' },
    'rapport': { icon: 'file-alt', color: 'indigo', gradient: 'from-indigo-400 to-indigo-600' },
    'default': { icon: 'circle-info', color: 'gray', gradient: 'from-gray-400 to-gray-600' }
};

// Group activities by date for better organization
const groupedActivities = computed(() => {
    const groups = {};
    const today = new Date().toLocaleDateString('fr-FR');
    const yesterday = new Date(Date.now() - 86400000).toLocaleDateString('fr-FR');
    
    props.recentActivities.forEach(activity => {
        const date = new Date(activity.created_at).toLocaleDateString('fr-FR');
        let groupName = date;
        
        if (date === today) {
            groupName = "Aujourd'hui";
        } else if (date === yesterday) {
            groupName = "Hier";
        }
        
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        
        groups[groupName].push(activity);
    });
    
    return groups;
});

// Helper function for time formatting
function timeAgo(date) {
    const now = new Date();
    const d = new Date(date);
    const diff = (now - d) / 1000;
    if (diff < 60) return "À l'instant";
    if (diff < 3600) return `Il y a ${Math.floor(diff/60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff/3600)} h`;
    if (diff < 172800) return 'Hier';
    return `Il y a ${Math.floor(diff/86400)} jours`;
}

// Format time as hours:minutes
function formatTime(date) {
    return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

// For animation states and dashboard refresh
const isLoaded = ref(false);
const lastRefreshed = ref(new Date());
const refreshing = ref(false);

// Initialize animations and dashboard data
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

// Function to refresh dashboard data
function refreshDashboard() {
    refreshing.value = true;
    // Simulate refresh time (in a real app, you would fetch data here)
    setTimeout(() => {
        lastRefreshed.value = new Date();
        refreshing.value = false;
    }, 800);
}
</script>

<template>
    <Head title="Administration" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Tableau de bord d'administration
                </h2>
                <div class="mt-2 sm:mt-0 flex items-center text-sm text-gray-500">
                    <span class="mr-4 flex items-center">
                        <FontAwesomeIcon icon="calendar" class="mr-1" />
                        {{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </span>
                    <button 
                        @click="refreshDashboard" 
                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs rounded 
                              text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 
                              focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        :disabled="refreshing"
                    >
                        <FontAwesomeIcon 
                            :icon="refreshing ? 'spinner' : 'sync'" 
                            :class="{'animate-spin': refreshing}"
                            class="mr-1" 
                        />
                        Actualiser
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Error Message with enhanced styling and animation -->
                <transition 
                    enter-active-class="transition duration-300 ease-out" 
                    enter-from-class="transform -translate-y-4 opacity-0" 
                    enter-to-class="transform translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in" 
                    leave-from-class="opacity-100" 
                    leave-to-class="opacity-0"
                >
                    <div 
                        v-if="error" 
                        class="mb-6 rounded-lg bg-red-50 p-4 shadow-sm border-l-4 border-red-500"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <FontAwesomeIcon icon="exclamation-circle" class="h-5 w-5 text-red-500" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                            </div>
                        </div>
                    </div>
                </transition>
                
                <!-- Last refreshed info -->
                <div class="mb-6 text-right text-xs text-gray-500">
                    Dernière actualisation: {{ lastRefreshed.toLocaleTimeString('fr-FR') }}
                </div>
                
                <!-- Stats Cards with enhanced styling and trend indicators -->
                <div 
                    class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                    :class="{ 'opacity-100 transform translate-y-0': isLoaded, 'opacity-0 transform -translate-y-4': !isLoaded }"
                    style="transition: all 0.5s ease;"
                >
                    <div 
                        v-for="(stat, index) in statsCards" 
                        :key="index"
                        class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md hover:ring-0"
                        :style="`transition-delay: ${index * 100}ms`"
                    >
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div :class="`bg-gradient-to-r ${stat.gradient} rounded-lg p-3 shadow-sm`">
                                        <FontAwesomeIcon 
                                            :icon="['fas', stat.icon]" 
                                            class="text-white text-xl" 
                                        />
                                    </div>
                                </div>
                                <div class="ml-5 w-full">
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 mb-1">
                                                {{ stat.title }}
                                            </p>
                                            <div class="flex items-center">
                                                <p class="text-2xl font-bold" :class="`text-${stat.color}-600`">
                                                    {{ stat.count }}
                                                </p>
                                                <div v-if="stat.trend" class="ml-2 flex items-center">
                                                    <FontAwesomeIcon 
                                                        :icon="stat.trendUp ? 'arrow-up' : 'arrow-down'" 
                                                        class="text-xs mr-0.5"
                                                        :class="stat.trendUp ? 'text-emerald-500' : 'text-red-500'"
                                                    />
                                                    <span 
                                                        class="text-xs font-medium"
                                                        :class="stat.trendUp ? 'text-emerald-500' : 'text-red-500'"
                                                    >
                                                        {{ stat.trend }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <Link 
                                                :href="route(stat.route)" 
                                                class="text-sm font-medium text-gray-400 hover:text-gray-900 transition-colors"
                                            >
                                                <FontAwesomeIcon icon="arrow-right" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Panels -->
                <div 
                    class="grid grid-cols-1 gap-8 lg:grid-cols-5"
                    :class="{ 'opacity-100 transform translate-y-0': isLoaded, 'opacity-0 transform -translate-y-4': !isLoaded }"
                    style="transition: all 0.7s ease;"
                >
                    <!-- Recent Activities - 3/5 width on large screens -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Activities Panel -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200">
                            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Activités récentes</h3>
                                <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1 rounded-full">
                                    {{ props.recentActivities.length }} activités
                                </span>
                            </div>
                            <div class="px-6 py-2">
                                <template v-if="props.recentActivities.length > 0">
                                    <div v-for="(activities, date) in groupedActivities" :key="date" class="mb-4">
                                        <div class="sticky top-0 bg-white z-10 py-2">
                                            <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">
                                                {{ date }}
                                            </h4>
                                        </div>
                                        <ul class="divide-y divide-gray-100">
                                            <li 
                                                v-for="(activity, idx) in activities" 
                                                :key="idx" 
                                                class="py-3 px-2 hover:bg-gray-50 rounded-lg transition-colors"
                                            >
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div 
                                                            :class="`inline-flex h-10 w-10 items-center justify-center rounded-full
                                                                bg-gradient-to-r ${activityIconMap[activity.type]?.gradient || activityIconMap.default.gradient}`"
                                                        >
                                                            <FontAwesomeIcon 
                                                                :icon="activityIconMap[activity.type]?.icon || activityIconMap.default.icon" 
                                                                class="text-white" 
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex justify-between">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ activity.title }}
                                                                <span v-if="activity.user" class="font-normal">: {{ activity.user }}</span>
                                                                <span v-if="activity.structure" class="font-normal">: {{ activity.structure }}</span>
                                                            </p>
                                                            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                                                {{ formatTime(activity.created_at) }}
                                                            </span>
                                                        </div>
                                                        <p v-if="activity.description" class="text-sm text-gray-500 mt-1">
                                                            {{ activity.description }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="py-10 text-gray-400 text-center">
                                        <FontAwesomeIcon icon="inbox" class="text-gray-300 text-3xl mb-2" />
                                        <p>Aucune activité récente à afficher.</p>
                                    </div>
                                </template>
                            </div>
                            <!-- Footer commented out as per the source code -->
                            <!-- <div class="border-t border-gray-100 bg-gray-50 px-6 py-3 text-right">
                                <Link href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                    Voir toutes les activités
                                    <FontAwesomeIcon icon="arrow-right" class="ml-1" />
                                </Link>
                            </div> -->
                        </div>
                    </div>

                    <!-- Right Column - 2/5 width on large screens -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Quick Actions Panel -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200">
                            <div class="border-b border-gray-100 px-6 py-4">
                                <h3 class="text-lg font-semibold text-gray-900">Actions rapides</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-4">
                                    <template v-for="(action, index) in quickActions" :key="index">
                                        <a
                                            v-if="action.download"
                                            :href="route(action.route)"
                                            target="_blank"
                                            download
                                            :class="`flex items-center rounded-lg border border-gray-200 p-4 
                                                    transition-all hover:bg-${action.color}-50 hover:border-${action.color}-200
                                                    focus:outline-none focus:ring-2 focus:ring-${action.color}-500 focus:ring-offset-2`"
                                        >
                                            <div :class="`flex-shrink-0 rounded-lg bg-gradient-to-r from-${action.color}-400 to-${action.color}-600 p-3`">
                                                <FontAwesomeIcon 
                                                    :icon="action.icon" 
                                                    class="text-white h-5 w-5" 
                                                />
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ action.title }}</p>
                                                <p class="text-xs text-gray-500">{{ action.description }}</p>
                                            </div>
                                            <FontAwesomeIcon 
                                                icon="download" 
                                                class="text-gray-400"
                                            />
                                        </a>
                                        <Link
                                            v-else
                                            :href="route(action.route)" 
                                            :class="`flex items-center rounded-lg border border-gray-200 p-4 
                                                    transition-all hover:bg-${action.color}-50 hover:border-${action.color}-200
                                                    focus:outline-none focus:ring-2 focus:ring-${action.color}-500 focus:ring-offset-2`"
                                        >
                                            <div :class="`flex-shrink-0 rounded-lg bg-gradient-to-r from-${action.color}-400 to-${action.color}-600 p-3`">
                                                <FontAwesomeIcon 
                                                    :icon="action.icon" 
                                                    class="text-white h-5 w-5" 
                                                />
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ action.title }}</p>
                                                <p class="text-xs text-gray-500">{{ action.description }}</p>
                                            </div>
                                            <FontAwesomeIcon 
                                                icon="chevron-right" 
                                                class="text-gray-400"
                                            />
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- System Status Panel commented out as per the source code -->
                        <!-- <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200">
                            <div class="border-b border-gray-100 px-6 py-4">
                                <h3 class="text-lg font-semibold text-gray-900">État du système</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-3 w-3 rounded-full bg-green-400 mr-2"></div>
                                        <span class="text-sm text-gray-700">Service en ligne</span>
                                    </div>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                        Opérationnel
                                    </span>
                                </div>
                                
                                <div class="pt-2 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Espace disque</span>
                                        <span class="text-xs font-medium text-gray-700">75%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                </div>
                                
                                <div class="pt-2 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Mémoire système</span>
                                        <span class="text-xs font-medium text-gray-700">45%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: 45%"></div>
                                    </div>
                                </div>
                                
                                <div class="pt-2 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Dernière sauvegarde</span>
                                        <span class="text-xs font-medium text-gray-700">Il y a 3 heures</span>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- Added this widget for notifications (if needed) -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-xl ring-1 ring-gray-200">
                            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                <span class="bg-blue-100 text-blue-600 text-xs font-medium px-2.5 py-1 rounded-full">
                                    3 nouvelles
                                </span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                                                <FontAwesomeIcon icon="bell" class="text-blue-600" />
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Mise à jour système prévue</p>
                                            <p class="mt-1 text-sm text-gray-500">Planifiée pour le 15 mai à 22h00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                                                <FontAwesomeIcon icon="check-circle" class="text-emerald-600" />
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">5 nouvelles inscriptions validées</p>
                                            <p class="mt-1 text-sm text-gray-500">Les stagiaires ont été notifiés</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100">
                                                <FontAwesomeIcon icon="exclamation-triangle" class="text-amber-600" />
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Rappel : Réunion mensuelle</p>
                                            <p class="mt-1 text-sm text-gray-500">Demain à 10h00 en salle de conférence</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 bg-gray-50 px-6 py-3 text-right">
                                <Link href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                    Voir toutes les notifications
                                    <FontAwesomeIcon icon="arrow-right" class="ml-1" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Enhanced animations and transitions */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { transform: translateX(-20px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.stats-card-animation {
  animation: fadeIn 0.3s ease-out forwards;
}

.slide-animation {
  animation: slideIn 0.3s ease-out forwards;
}

/* Improve responsiveness for small screens */
@media (max-width: 640px) {
  .header-container {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .date-display {
    margin-top: 0.5rem;
  }
}
</style>