<script setup> 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({ 
  'items' : Array,
  'order' : Array
})

onMounted(() => { 
  console.log(props.items) 
  console.log(props.order)
});

</script>

<template> 
<Head title="購買履歴　詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">購買履歴　詳細</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <section class="text-gray-600 body-font">
                          <div class="container px-5 py-8 mx-auto">
                            
                            <!-- 購入情報 -->
                            <div class="mb-8 bg-gray-50 p-6 rounded-lg" v-if="order && order.length > 0">
                              <h3 class="text-lg font-bold mb-4">購入情報</h3>
                              <div class="grid grid-cols-2 gap-4">
                                <div>
                                  <span class="font-semibold">購入ID:</span> {{ order[0].id }}
                                </div>
                                <div>
                                  <span class="font-semibold">顧客名:</span> {{ order[0].customer_name }}
                                </div>
                                <div>
                                  <span class="font-semibold">合計金額:</span> {{ order[0].total?.toLocaleString() }}円
                                </div>
                                <div>
                                  <span class="font-semibold">ステータス:</span> 
                                  <span v-if="props.order[0].status == true">未キャンセル</span>
                                  <span v-if="props.order[0].status == false">キャンセル済み</span>
                                  <div v-if="props.order[0].status == true"> 
                               </div>
                                </div>
                                <div>
                                  <span class="font-semibold">購入日:</span> {{ dayjs(order[0].created_at).format('YYYY-MM-DD HH:mm:ss') }}
                                </div>
                                <div>
                                  <span class="font-semibold">キャンセル日:</span> {{ dayjs(order[0].cancelled_at).format('YYYY-MM-DD HH:mm:ss') }}</div>
                              </div>
                            </div>
                            
                            <!-- 購入商品一覧 -->
                            <div class="lg:w-full w-full mx-auto overflow-auto">
                              <h3 class="text-lg font-bold mb-4">購入商品</h3>
                              <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                  <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">商品名</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">数量</th>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br">小計</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-for="item in items" :key="item.id">
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.item_name }}</td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.quantity }}</td>
                                    <td class="border-b-2 border-gray-200 px-4 py-3">{{ item.subtotal?.toLocaleString() }}円</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            
                            <!-- 戻るボタン -->
                            <div class="mt-8">
                              <Link 
                                :href="route('purchases.index')" 
                                class="text-white bg-gray-500 border-0 py-2 px-8 focus:outline-none hover:bg-gray-600 rounded"
                              >
                                一覧に戻る
                              </Link>
                              <Link 
                                v-if="props.order[0].status == true || props.order[0].status == 1"
                                as="button" 
                                :href="route('purchases.edit', { purchase: props.order[0].id })" 
                                class="ml-4 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded"
                              >
                                編集する
                              </Link> 

                            </div>
                            
                          </div>
                        </section>
                        
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>