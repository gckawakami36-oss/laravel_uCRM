<script setup> 
import { reactive } from 'vue' 
import { router } from '@inertiajs/vue3'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import { Inertia} from '@inertiajs/vue3';

defineProps({
    errors: Object
})

const form = reactive({ 

  title: null, 
  content: null,
  status: null,
  items: []
}) 

const storePurchase = () => {
  itemList.value.forEach( item => { 
    if( item.quantity > 0 ) // 0より大きいものだけ追加 
   form.items.push({ id : item.id, quantity: item.quantity }) }) 
   Inertia.post(route('purchases.store'), form);
}


const submitFunction = () => { 
router.post('/inertia', form, );
} 

</script> 

<template> 
  <BreezeValidationErrors :errors="errors" />
  <form @submit.prevent="submitFunction" class="flex flex-col gap-4 max-w-md mx-auto p-4"> <br>
    <div v-if="errors.title" >{{ errors.title }}</div>
    <div v-if="errors.content" >{{ errors.content }}</div>
    <input type="text" name="title" v-model="form.title" placeholder="タイトル" class="w-full"> 
    <input type="text" name="content" v-model="form.content" placeholder="内容" class="w-full"> 
    <div class="flex justify-end">
      <button>送信</button>
    </div>
  </form> 
</template>
