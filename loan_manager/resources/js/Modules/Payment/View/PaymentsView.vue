<script setup lang="ts">
import {onMounted} from "vue";
import { PaymentDetail, usePayment} from '@/Modules/Payment'
import { DataTable, formatCurrency, KPI, PageHeader,  useModal} from '@/Shared'

const {columns, payments, monthlyReport, getAll, getById, getMonthlyReport } = usePayment();
const { open } = useModal();

const handleRowClick = async (id: string) => {
  getById(id)
  open(
      PaymentDetail, {
        size: "lg"
      },
  );
}

onMounted(async () => {
  await getAll();
  await getMonthlyReport();
})
</script>

<template>
  <div class="page pu">
    <!--<PageHeader :title="`Pagos - ${monthlyReport?.month} ${monthlyReport?.year}`" /> -->
    <PageHeader title="Pagos" />
      <div class="kpi-grid" v-if="monthlyReport">
        <KPI label="Capital Retornado" :value="formatCurrency(monthlyReport?.capitalReturned)" :goldValue="true" />
        <KPI label="Intereses Recaudados" :value="formatCurrency(monthlyReport?.interestCollected)" :goldValue="true" />
        <KPI label="Pagos Realizados" :value="monthlyReport?.paymentsCount.toString() || '0'" sub="este mes" :goldValue="true" />
      </div>
      <DataTable :columns="columns" :rows="payments" @row-click="handleRowClick" />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }

@media screen and (max-width: 663px){
  .kpi-grid{
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
  }
}

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>
