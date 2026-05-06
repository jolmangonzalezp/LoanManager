<script setup lang="ts">
import {onMounted} from "vue";
import { CustomerDetail, useCustomer } from '@/Modules/Customer';
import { DataTable, KPI, PageHeader, QuickActions, useModal } from '@/Shared';

const {
  columns,
  customers,
  customerKPI,
  getAll,
  getById,
  getLoans,
  getCustomerKPI
} = useCustomer()

const {open} = useModal()


const handleRowClick = async (id: string) => {
  getById(id);
  getLoans(id);
  open(
      CustomerDetail, {
        size: 'lg',
      }
  );
}

onMounted(async () => {
  getCustomerKPI();
  getAll();
})
</script>

<template>
  <QuickActions />
  <div class="page pu">
    <PageHeader title="Clientes" />

    <div class="kpi-grid" v-if="customerKPI">
      <KPI label="Total Clientes" :value="customerKPI.activeCustomers" sub="Registrados" :goldValue="true" class="kpi-grid-item"/>
      <KPI label="Con préstamos activos" :value="customerKPI.customersWithActiveLoans" sub="Activos" :goldValue="true" class="kpi-grid-item"/>
      <KPI label="Sin préstamos" :value="customerKPI.customersWithoutLoans" sub="Sin actividad" :goldValue="true" class="kpi-grid-item"/>
    </div>

    <DataTable :columns="columns" :rows="customers" @row-click="handleRowClick"/>

  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 26px; }

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>
