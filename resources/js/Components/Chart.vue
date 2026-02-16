<script setup>
import { Chart, registerables } from "chart.js"; 
import { BarChart } from "vue-chart-3"; 
import { reactive, watch } from "vue"
import { computed } from "vue";

const props = defineProps({
  chartData: {
    type: Object,
    required: true
  }
})

const labels = computed(() => props.chartData.labels || [] ) 
const totals = computed(() => {
  const data = props.chartData.totals || []
  return data.map(total => Number(total))
})

Chart.register(...registerables); 
const barData = computed(() => ({ 
  labels: labels.value, 
  datasets: [ 
    { 
      label: '売上', 
      data: totals.value, 
      backgroundColor: "rgb(75, 192, 192)", 
      tension: 0.1, 
    } 
  ] 
})) 

// デバッグログ
watch(() => props.chartData, (newVal) => {
  console.log('chartData updated:', newVal)
  console.log('labels:', labels.value)
  console.log('totals:', totals.value)
  console.log('data length:', newVal.data?.length)
}, { deep: true })

</script>

<template>

<div> 
    <div v-if="props.chartData.data && props.chartData.data.length"> 
    <BarChart :chartData="barData" /> 
    </div> 
</div>
</template>