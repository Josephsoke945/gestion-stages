<template>
  <div class="relative">
    <Doughnut
      :data="chartData"
      :options="chartOptions"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale
);

const props = defineProps({
  stats: {
    type: Object,
    required: true
  }
});

const chartData = computed(() => ({
  labels: ['En attente', 'Acceptées', 'Rejetées'],
  datasets: [
    {
      backgroundColor: ['#FCD34D', '#34D399', '#F87171'],
      data: [
        props.stats.demandesEnAttente,
        props.stats.demandesAcceptees,
        props.stats.demandesRejetees
      ]
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: true,
  plugins: {
    legend: {
      position: 'bottom'
    },
    title: {
      display: true,
      text: 'Répartition des demandes',
      font: {
        size: 16
      }
    }
  },
  cutout: '70%',
  radius: '90%'
};
</script> 