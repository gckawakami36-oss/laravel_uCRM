<script setup>
import { ref } from 'vue' 
import axios from 'axios'
import { onMounted } from 'vue'

const search = ref('')
const customers = ref({ data: [] })
const isShow = ref(false) 
const toggleStatus = () => { isShow.value = !isShow.value} 

const emit = defineEmits(['customerSelected'])
const setCustomer = e => { 
  search.value = e.kana 
  emit('update:customerId', e.id)
    toggleStatus()
}


const searchCustomers = async () => { 
  try{ 
    const res = await axios.get(`/api/searchCustomers/?search=${search.value}`)
    console.log(res.data) 
    customers.value = res.data 
    toggleStatus() 
  } catch (e){ 
    console.error('検索エラー:', e)
    console.error('レスポンス:', e.response)
  }
}

onMounted(() => {
  axios.get('/api/user') 
 .then( res => { 
    console.log(res)
    });
  });
</script>

<template>
    <div class="modal micromodal-slide" :class="{'is-open': isShow}" id="modal-1" :aria-hidden="!isShow">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
        <header class="modal__header">
          <h2 class="modal__title" id="modal-1-title">
            顧客検索
          </h2>
          <button @click="toggleStatus" type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content" id="modal-1-content">
          <div class="lg:w-2/3 w-full mx-auto overflow-auto">
      <table class="table-auto w-full text-left whitespace-no-wrap">
        <thead>
          <tr>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">名前</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">カナ</th>
            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">電話番号</th>
            
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in customers.data" :key="customer.id">
            <td class="border-b-2 border-gray-200 px-4 py-3 cursor-pointer text-blue-600 hover:text-blue-800" @click="setCustomer(customer)">
              {{ customer.id }}
            </td>
            <td class="border-b-2 border-gray-200 px-4 py-3">{{ customer.name }}</td>
            <td class="border-b-2 border-gray-200 px-4 py-3">{{ customer.kana }}</td>
            <td class="border-b-2 border-gray-200 px-4 py-3 text-lg">{{ customer.tel }}</td>
            
          </tr>
        </tbody>
      </table>
    </div>
        </main>
        <footer class="modal__footer">
        
          <button @click="toggleStatus" type="button" class="modal__btn" aria-label="Close this dialog window">閉じる</button>
        </footer>
      </div>
    </div>
  </div>

 <input name="customer" v-model="search"> 
 
<button type="button" @click="searchCustomers"  class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">検索する</button>
</template>