<template>
    <div class="p-">
        <div class="">
            <div class="grid grid-cols-3 gap-2 text-sm text-[#515365] font-bold p-[10px]">
                <Kpi title="total" value="120"/>
                <Kpi title="total" value="120"/>
                <Kpi title="total" value="120"/>
            </div>
        </div>
    </div>
</template>
<script setup>
  import { ref, computed, onMounted } from 'vue';
  import apexchart from 'vue3-apexcharts';
  import { useAppStore } from '@/stores/index';
  import Kpi from '../components/app/home/Kpi.vue';

//   import axios from 'axios';
  const store = useAppStore();

  onMounted(() => {
    // getStatus()
  })

  const consumed_users = ref();
  const data_users = ref([]);
  const consumed_socials = ref();
  const data_socials = ref([]);
  const consumed_physiques = ref();
  const data_physiques = ref([]);
  
  const getStatus = () => {
      axios.get(`/api/accountant/kpis/${localStorage.getItem("orgId")}`,{
        headers: {
          Authorization : `Bearer ${localStorage.getItem("access_token")}`
          }
        }).then(res => {
          console.log('kpis',res.data);
          consumed_users.value = res.data.userskpis.value;
          
          consumed_socials.value = res.data.socialkpis.value;
          
          consumed_physiques.value = res.data.physiquekpis.value;
          data_users.value = [{data: getStatistics(res.data.users)}]
          data_socials.value = [{data: getStatistics(res.data.socials)}]
          data_physiques.value = [{data: getStatistics(res.data.physiques)}]
        })
    }
    const getStatistics = (statics,type = null) => {
      let arr = []
      const timing = new Date();
      var total = 0
      for (let i = 10; i >= 0; i--) {
        total = 0
        const date = new Date();
        date.setDate(date.getDate() - i);
        statics.forEach(word => {
          const date1 = new Date(word.created_at);
          if (date1.getFullYear() === date.getFullYear() && date1.getMonth() === date.getMonth() && date1.getDate() === date.getDate()) {
            total = Number(total) + 1
          }
        });
        if (isNaN(total)) {
          total = 0;
        }
        arr.push(total);
        
    };   
    console.log(arr);
     return arr;
    }
</script>