<script setup>

import { onMounted } from "vue";
import { Chart } from "chart.js";
import { LineController } from "chart.js/auto";

class Custom extends LineController {
    draw() {
        // Call bubble controller method to draw all the points
        super.draw();

        // Now we can do some custom drawing for this dataset. Here we'll draw a red box around the first point in each dataset
        const meta = this.getMeta();
        const pt0 = meta.data[0];

        const {x, y} = pt0.getProps(['x', 'y']);
        const {radius} = pt0.options;

        const ctx = this.chart.ctx;
        ctx.save();
        ctx.strokeStyle = 'red';
        ctx.lineWidth = 1;
        ctx.strokeRect(x - radius, y - radius, 2 * radius, 2 * radius);
        ctx.restore();
    }
};
Custom.id = 'derivedLine';
Custom.defaults = LineController.defaults;

// Stores the controller so that the chart initialization routine can look it up
Chart.register(Custom);

const props = defineProps({
    chartId: {
        type: String,
        default: 'line-chart'
    },
    datasetIdKey: {
        type: String,
        default: 'label'
    },
    width: {
        type: Number,
        default: 400
    },
    height: {
        type: Number,
        default: 400
    },
    cssClasses: {
        default: '',
        type: String
    },
    styles: {
        type: Object,
        default: () => {}
    },
    plugins: {
        type: Array,
        default: () => []
    },
    chartData: {},
    chartOptions: {}
});

onMounted(() => {
    //console.log("monthLineChart mounted ref: ", this.chartData)
    if (props.chartData) {
        let ctx = document.getElementById(props.chartId).getContext('2d');
        let cdata = props.chartData
        //new Chart(this.$refs.monthLineCanvas, {
        let chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {
                labels: props.chartData.labels,
                datasets: props.chartData.datasets
            },

            // Configuration options go here
            options: {
                legend: {
                    display: true
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>

<template>
    <canvas :id="chartId"></canvas>
</template>

<style scoped>

</style>
