<script setup lang="ts">
import {PageHeader, KPI, TableWrap, Ref, useModal} from '@/Shared'
import {useCustomer} from '@/Modules/Customer'
import DataTableComponent from "@Shared/Components/DataTableComponent.vue";
import {onMounted} from "vue";
import QuickActionsComponent from "@Shared/Components/QuickActionsComponent.vue";
import CustomerDetailComponent from "@Modules/Customer/Component/CustomerDetailComponent.vue";

const {
  columns,
  customers,
  customerKPI,loans,
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
      CustomerDetailComponent, {
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
  <QuickActionsComponent />
  <div class="page pu">
    <PageHeader title="Clientes" />

    <div class="kpi-grid" v-if="customerKPI">
      <KPI label="Total Clientes" :value="customerKPI.activeCustomers" sub="Registrados" :goldValue="true" class="kpi-grid-item"/>
      <KPI label="Con préstamos activos" :value="customerKPI.customersWithActiveLoans" sub="Activos" :goldValue="true" class="kpi-grid-item"/>
      <KPI label="Sin préstamos" :value="customerKPI.customersWithoutLoans" sub="Sin actividad" :goldValue="true" class="kpi-grid-item"/>
    </div>

    <DataTableComponent :columns="columns" :rows="customers" @row-click="handleRowClick"/>

  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 26px; }
.trow { cursor: pointer; }
.trow:hover > td { background: rgba(212,175,55,0.04) !important; }

@media screen and (max-width: 430px){
  .kpi-grid{
    grid-template-columns: 1fr;
    grid-template-rows: repeat(2, 1fr);
  }
}
</style>