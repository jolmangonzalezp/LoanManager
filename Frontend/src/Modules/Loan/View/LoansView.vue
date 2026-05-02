<script setup lang="ts">
import {PageHeader, KPI, TableWrap, Ref, Amount, Btn, useModal} from '@/Shared'
import DataTableComponent from "@Shared/Components/DataTableComponent.vue";
import { formatCurrency, formatDate, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'
import {useLoan} from "@Modules/Loan/index.ts";
import {onMounted} from "vue";
import LoanDetailComponent from "@Modules/Loan/Component/LoanDetailComponent.vue";
import QuickActionsComponent from "@Shared/Components/QuickActionsComponent.vue";

const {columns, loans, report, getAll, getById, getReport} = useLoan();
const {open} = useModal();

const handleRowClick = async (id: string) => {
  await getById(id);
  open(
      LoanDetailComponent,
      {
        size: "lg",
      }
  );
}

onMounted(() => {
  getReport();
  getAll();
})

</script>

<template>
  <QuickActionsComponent />
  <div class="page pu">
    <PageHeader title="Cartera de Préstamos" />
      <div v-if="report" class="kpi-grid">
        <KPI label="Cartera Total" :value="formatCurrency(report.totalCapital)" :goldValue="true" class="kpi-grid-item"/>
        <KPI label="Activos" :value="report.activeLoans || 0" :goldValue="true" class="kpi-grid-item" />
        <KPI label="En Mora" :value="report.defaultedLoans || 0" :goldValue="true" class="kpi-grid-item" />
      </div>

    <DataTableComponent :columns="columns" :rows="loans" @row-click="handleRowClick"/>
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }

@media screen and (max-width: 663px){
  .kpi-grid{
    grid-template-columns: 1fr 1fr;
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

@media screen and (max-width: 560px){
  .kpi-grid{
    grid-template-columns: 1fr;
  }
}
</style>
