<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import { reactive, onMounted } from 'vue' 
import { getToday } from '@/common'
import Chart from '@/Components/Chart.vue';

const data = reactive({
  data: [],
  labels: [],
  totals: []
})

onMounted(() => { 
  form.startDate = getToday();
  form.endDate = getToday();
})
const form = reactive({ 
    startDate: null, 
    endDate: null,
    type: 'perDay'// 仮で直入力
})

const getData = async () => { 
try{ 
  await axios.get('/api/analysis/', { 
    params: { 
      startDate: form.startDate, 
      endDate: form.endDate, 
      type: form.type 
    } 
  }) 
  .then( res => { 
      data.data = res.data.data
      console.log(res.data) 
      data.labels = res.data.labels
      data.totals = res.data.totals
    }) 
  } catch (e){ 
    console.log(e.message) 
  } 
}
</script>

<template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">データ分析</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FlashMessage />
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="getData"> 
                     From: <input type="date" name="startDate" v-model="form.startDate"> 
                      To: <input type="date" name="endDate" v-model="form.endDate"> 
                        <button type="submit" class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                            分析する</button>
                        </form>

                        <Chart :chartData="data" />

                      <div class="lg:w-2/3 w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">年月日</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">金額</th>
            
            
          </tr>
        </thead>
          <tbody>
            <tr v-if="!data.data">
              <td colspan="2">データがありません</td>
            </tr>
            <tr v-for="item in data.data" :key="item.date">
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.date }}</td>
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.total }}</td>
            </tr>
          </tbody>
        </table>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>
