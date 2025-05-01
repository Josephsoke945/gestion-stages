<template>
    <div v-if="links.length > 3" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                v-html="link.label" />

            <button v-else
                @click="handleClick(link)"
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500"
                :class="{ 'bg-blue-700 text-white': link.active }"
                v-html="link.label" />
        </template>
    </div>
</template>

<script setup>
const emit = defineEmits(['change']);

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

function handleClick(link) {
    if (!link.url) return;
    
    // Extraire le numéro de page de l'URL
    const url = new URL(link.url);
    const page = url.searchParams.get('page') || '1';
    
    emit('change', page);
}
</script> 