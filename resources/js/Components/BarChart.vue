<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import {
    Chart,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
);

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    data: {
        type: Array,
        default: () => [],
    },
});

const canvas = ref(null);
let chart = null;

function buatGrafik() {
    if (!canvas.value) return;

    if (chart) {
        chart.destroy();
    }

    chart = new Chart(canvas.value, {
        // Jenis grafik: bar atau line
        type: 'bar',

        data: {
            labels: props.labels,
            datasets: [
                {
                    label: 'Jumlah Peminjaman',
                    data: props.data,

                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1,
                    borderRadius: 5,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
}

onMounted(() => {
    buatGrafik();
});

watch(
    () => [props.labels, props.data],
    () => buatGrafik(),
    { deep: true },
);

onBeforeUnmount(() => {
    if (chart) {
        chart.destroy();
    }
});
</script>

<template>
    <div style="position: relative; height: 320px; width: 100%;">
        <canvas ref="canvas"></canvas>
    </div>
</template>
