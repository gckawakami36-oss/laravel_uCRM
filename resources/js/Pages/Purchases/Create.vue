<script setup> 
import { getToday } from '@/common';
import { onMounted, reactive } from 'vue';
import{ref} from 'vue';
import{ computed } from 'vue';

onMounted(() => {
  form.date = getToday();
})

const props = defineProps({customers: Array, items: Array});

const form = reactive({ 

  date: null,
  customer_id: null,
  item_id: null,
}) 
const itemList = ref([])
onMounted(() => {
  props.items.forEach((item) => {
    itemList.value.push({ id: item.id, name: item.name, price: item.price, quantity: 0 });
  })
})
const quanty = [0,1,2,3,4,5,6,7,8,9];
const totalPrice = computed(() => {
  let total = 0;
  itemList.value.forEach((item) => {
    total += item.price * item.quantity;
  });
  return total;
});

</script> 

<template> 
  日付<br>
  <input type="date" name="date" v-model="form.date">
  <br>会員名<br>
  <select name="customer" v-model="form.customer_id"> 
   <option v-for="customer in customers" :value="customer.id" :key="customer.id"> 
  {{ customer.id }} : {{ customer.name }} 
  </option>
    </select>
  <br>商品・サービス<br>
  <table>
  <thead> 
    <tr>
      <th>ID</th> 
      <th>商品名</th> 
      <th>価格</th> 
      <th>数量</th> 
      <th>小計</th>
    </tr>
  </thead>
    <tbody> 
    <tr v-for="item in itemList" > 
      <td>{{ item.id }}</td> 
      <td>{{ item.name }}</td> 
      <td>{{ item.price }}</td> 
      <td> 
        <select name="quantity" v-model="item.quantity"> 
          <option v-for="q in quanty" :value="q">{{ q }}</option> 
        </select> 
      </td> 
      <td> 
        {{ item.price * item.quantity }} 
      </td> 
    </tr> 
  </tbody>
  
  </table>
  合計金額: {{ totalPrice }} 円
  </template>
 
 