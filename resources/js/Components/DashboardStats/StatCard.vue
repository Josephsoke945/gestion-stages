<template>
  <div 
    class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-all duration-300"
    :class="{ 'transform hover:-translate-y-1': enableHover }"
  >
    <div class="p-6">
      <div class="flex items-center">
        <div 
          class="p-3 rounded-full"
          :class="[`bg-${color}-100`, `text-${color}-600`]"
        >
          <slot name="icon"></slot>
        </div>
        <div class="ml-4 flex-1">
          <p class="text-sm font-medium text-gray-600">{{ title }}</p>
          <div class="flex items-baseline">
            <p class="text-3xl font-bold" :class="`text-${color}-600`">
              {{ count }}
            </p>
            <p class="ml-2 text-sm text-gray-500">demande(s)</p>
          </div>
          <div v-if="showProgress && total > 0" class="mt-3">
            <div class="flex justify-between text-xs text-gray-600">
              <span>Progression</span>
              <span>{{ computedPercentage }}%</span>
            </div>
            <div class="mt-1 h-2 w-full bg-gray-200 rounded-full overflow-hidden">
              <div 
                class="h-full rounded-full transition-all duration-500"
                :class="`bg-${color}-500`"
                :style="{ width: `${computedPercentage}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    default: 0
  },
  color: {
    type: String,
    default: 'blue'
  },
  showProgress: {
    type: Boolean,
    default: true
  },
  enableHover: {
    type: Boolean,
    default: true
  }
});

const computedPercentage = computed(() => {
  if (!props.total) return 0;
  return Math.round((props.count / props.total) * 100);
});
</script> 