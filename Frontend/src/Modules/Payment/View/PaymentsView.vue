<script setup lang="ts">
import {PageHeader,  useModal} from '@/Shared'
import {usePayment} from '@/Modules/Payment'
import { formatCurrency, formatDate } from '@/Shared'
import DataTableComponent from "@Shared/Components/DataTableComponent.vue";
import {onMounted} from "vue";
import PaymentDetailComponent from "@Modules/Payment/Component/PaymentDetailComponent.vue";
import {KPI} from "@Shared";

const {columns, payments, monthlyReport, getAll, getById, getMonthlyReport } = usePayment();
const { open } = useModal();

const handleRowClick = async (id: string) => {
  getById(id)
  open(
      PaymentDetailComponent, {
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
      <div class="kpi-grid">
        <KPI label="Capital Retornado" :value="formatCurrency(monthlyReport?.capital_returned)" :goldValue="true" />
        <KPI label="Intereses Recaudados" :value="formatCurrency(monthlyReport?.interest_collected)" :goldValue="true" />
        <KPI label="Pagos Realizados" :value="monthlyReport?.payments_count || 0" sub="este mes" :goldValue="true" />
      </div>
      <DataTableComponent :columns="columns" :rows="payments" @row-click="handleRowClick" />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.empty { text-align: center; padding: 40px; color: #64748b; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
.trow { cursor: pointer; }
.trow:hover > td { background: rgba(212,175,55,0.04) !important; }

@media screen and (max-width: 663px){
  .kpi-grid{
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
  }
  .kpi-grid-item:first-child{
    grid-column: 1/3;
  }
  .kpi-grid-item:nth-child(2){
    grid-column: 1/2;
    grid-row: 2/2;
  }
  .kpi-grid-item:last-child{
    grid-column: 2/2;
    grid-row: 2/2;
  }
}

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>