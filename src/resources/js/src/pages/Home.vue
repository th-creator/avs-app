<template>
    <div class="p-">
        <div class="">
            <div class="grid grid-cols-2 gap-2 text-sm text-[#515365] font-bold p-[10px]">
                <Kpi title="total" value="120"/>
                <!-- <Kpi title="total" value="120"/> -->
                <Kpi title="total" value="120"/>
            </div>
        </div>
        <div>
          <paymentTable />
        </div>
        <div>
          <feeTable />
        </div>
    </div>
</template>
<script setup>
  import { ref } from 'vue';
  import Kpi from '../components/app/home/Kpi.vue';
  import paymentTable from '@/components/app/home/payments/Table.vue';
  import feeTable from '@/components/app/home/fees/Table.vue';

  const consumed_users = ref();
  const consumed_socials = ref();
  const consumed_physiques = ref();
  
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
        })
    }
</script>