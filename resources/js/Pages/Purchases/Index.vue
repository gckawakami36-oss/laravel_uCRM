<script setup> 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({ 
  orders : Object,
  search : String,
  date : String
})

const search = ref(props.search || '');
const searchDate = ref(props.date || '');

const searchOrders = () => {
  router.get('/purchases', { 
    search: search.value,
    date: searchDate.value
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearSearch = () => {
  search.value = '';
  searchDate.value = '';
  router.get('/purchases');
};
</script>

<template> 
<Head title="購買履歴" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">購買履歴</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <section class="text-gray-600 body-font">
                          <div class="container px-5 py-8 mx-auto">
                            <FlashMessage />
                            
                            <div class="pl-4 my-4 lg:w-2/3 w-full mx-auto">
                              <div class="flex flex-wrap gap-2 items-center">
                                <input 
                                  v-model="search" 
                                  type="text" 
                                  placeholder="顧客名で検索" 
                                  class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex-1 min-w-[200px]"
                                  @keyup.enter="searchOrders"
                                />
                                <div class="flex items-center gap-2">
                                  <label class="text-sm text-gray-600">日付:</label>
                                  <input 
                                    v-model="searchDate" 
                                    type="date" 
                                    class="border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    @keyup.enter="searchOrders"
                                  />
                                </div>
                                <button 
                                  @click="searchOrders" 
                                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded"
                                >
                                  検索
                                </button>
                                <button 
                                  @click="clearSearch" 
                                  class="text-gray-700 bg-gray-200 border-0 py-2 px-6 focus:outline-none hover:bg-gray-300 rounded"
                                >
                                  クリア
                                </button>
                              </div>
                            </div>
                            
                            <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                              <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                  <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">名前</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">合計金額</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ステータス</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">購入日</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-for="order in orders.data" :key="order.id">
                                    <td class="border-b-2 border-gray-200 px-4 py-3">
                                        <Link :href="route('purchases.show', { purchase: order.id })" class="text-indigo-600 hover:text-indigo-900">
                                          {{ order.id }}
                                        </Link>
                                    </td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.customer_name }}</td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.total?.toLocaleString() }}円</td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.status }}</td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ dayjs(order.created_at).format('YYYY-MM-DD HH:mm:ss') }}</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <Pagination class="mt-4" :links="orders.links" />
                          </div>
                        </section>
                        
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

