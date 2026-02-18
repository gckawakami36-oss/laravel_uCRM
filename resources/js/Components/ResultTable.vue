<script setup>

const props = defineProps({
  data: Object
})

</script>

<template>
    <div  v-if="data.type === 'perDay' || data.type === 'perMonth' || data.type === 'perYear'" class="lg:w-2/3 w-full mx-auto overflow-auto">
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
     <div v-if="data.type === 'decile'" class="lg:w-2/3 w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">グループ</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">平均</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">合計</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">構成比</th>
          </tr>
        </thead>
          <tbody>
            <tr v-if="!data.data">
              <td colspan="4">データがありません</td>
            </tr>
            <tr v-for="item in data.data" :key="item.decile">
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.decile }}</td>
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.average }}</td>
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.totalPerGroup }}</td>
              <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.totalRatio }}</td>
            </tr>
          </tbody>
        </table>
     </div>
 <div v-if="data.type === 'rfm'" class="lg:w-2/3 w-full mx-auto overflow-auto">
  <!-- 合計人数 -->
  <div class="my-4">
    <p class="text-lg font-semibold">合計人数: {{ data.totals?.totalCount || 0 }} 人</p>
  </div>

  <!-- RFMランクごとの人数 -->
  <div class="my-6">
    <h3 class="text-md font-semibold mb-2">RFMランク毎の人数</h3>
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ランク</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">R (最終購入日)</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F (購入頻度)</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">M (購入金額)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rank in [5, 4, 3, 2, 1]" :key="rank">
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ rank }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ data.eachCount?.r?.[rank] || 0 }} 人</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ data.eachCount?.f?.[rank] || 0 }} 人</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ data.eachCount?.m?.[rank] || 0 }} 人</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- RとFの集計表 -->
  <div class="my-6">
    <h3 class="text-md font-semibold mb-2">RとFの集計表 (R×F クロス集計)</h3>
    <table class="table-auto w-full text-left whitespace-no-wrap">
      <thead>
        <tr>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">R＼F</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F5</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F4</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F3</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F2</th>
          <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">F1</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in data.rfCrossTable" :key="row.r_rank">
          <td class="border-b-2 border-gray-200 px-4 py-3 font-semibold">R{{ row.r_rank }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ row.f_5 || 0 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ row.f_4 || 0 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ row.f_3 || 0 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ row.f_2 || 0 }}</td>
          <td class="border-b-2 border-gray-200 px-4 py-3">{{ row.f_1 || 0 }}</td>
        </tr>
      </tbody>
    </table>
  </div>
 </div>
</template>