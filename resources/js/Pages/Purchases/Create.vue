<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { getToday } from '@/common';
import { onMounted, reactive, ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import MicroModal from '@/Components/micromodal.vue';

const errors = computed(() => usePage().props.errors || {});

const props = defineProps({customers: Array, items: Array});

const form = reactive({ 
  date: null,
  customer_id: null,
  status: true,
  items: []
});

const selectedCustomer = ref(null);
const handleCustomerSelected = (customerId) => {
  form.customer_id = customerId;
  selectedCustomer.value = props.customers.find(c => c.id === customerId);
};

onMounted(() => {
  form.date = getToday();
  props.items.forEach((item) => {
    itemList.value.push({ id: item.id, name: item.name, price: item.price, quantity: 0 });
  });
});

const storePurchase = () => {
  form.items = [];
  itemList.value.forEach(item => { 
    if(item.quantity > 0) {
      form.items.push({ id: item.id, quantity: item.quantity });
    }
  });
  router.post(route('purchases.store'), form);
};

const itemList = ref([]);
const quantity = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

const totalPrice = computed(() => {
  let total = 0;
  itemList.value.forEach((item) => {
    total += item.price * item.quantity;
  });
  return total;
});

</script>

<template>
    <Head title="購入画面" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">購入画面</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <BreezeValidationErrors class="mb-4" :errors="errors" />
                        <section class="text-gray-600 body-font relative">
                            <form @submit.prevent="storePurchase">
                                <div class="container px-5 py-8 mx-auto">
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                        <div class="flex flex-wrap -m-2">
                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label for="date" class="leading-7 text-sm text-gray-600">日付</label>
                                                    <input type="date" id="date" name="date" v-model="form.date" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                  
                                                  <div class="mt-2">
                                                    <label for="customer_id" class="leading-7 text-sm text-gray-600">会員名</label>
                                                   <MicroModal @update:customerId="handleCustomerSelected" />
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <div class="relative">
                                                    <label class="leading-7 text-sm text-gray-600">商品・サービス</label>
                                                    <table class="w-full mt-2 border-collapse border border-gray-300">
                                                        <thead>
                                                            <tr class="bg-gray-100">
                                                                <th class="border border-gray-300 px-2 py-1">ID</th>
                                                                <th class="border border-gray-300 px-2 py-1">商品名</th>
                                                                <th class="border border-gray-300 px-2 py-1">価格</th>
                                                                <th class="border border-gray-300 px-2 py-1">数量</th>
                                                                <th class="border border-gray-300 px-2 py-1">小計</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="item in itemList" :key="item.id">
                                                                <td class="border border-gray-300 px-2 py-1 text-center">{{ item.id }}</td>
                                                                <td class="border border-gray-300 px-2 py-1">{{ item.name }}</td>
                                                                <td class="border border-gray-300 px-2 py-1 text-right">{{ item.price.toLocaleString() }}</td>
                                                                <td class="border border-gray-300 px-2 py-1">
                                                                    <select v-model="item.quantity" class="bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-2">
                                                                        <option v-for="q in quantity" :key="q" :value="q">{{ q }}</option>
                                                                    </select>
                                                                </td>
                                                                <td class="border border-gray-300 px-2 py-1 text-right">{{ (item.price * item.quantity).toLocaleString() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <div class="relative text-right text-lg font-semibold">
                                                    合計金額: {{ totalPrice.toLocaleString() }} 円
                                                </div>
                                            </div>
                                            <div class="p-2 w-full">
                                                <button type="submit" class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">登録する</button>
                                            </div>
                                            <div class="p-2 w-full text-center">
                                                <Link :href="route('dashboard')" class="text-indigo-500 hover:text-indigo-600">戻る</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>