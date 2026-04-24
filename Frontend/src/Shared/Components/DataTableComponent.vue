<script setup lang="ts">
import { faChevronLeft, faChevronRight, faCircleNotch } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ButtonComponent } from '@/Shared'

interface Column {
  key: string
  label: string
}

const props = defineProps<{
  columns: Column[]
  rows: Record<string, any>[]
  loading?: boolean
  currentPage?: number
  totalPages?: number
  emptyMessage?: string
}>()

const emit = defineEmits<{
  (e: 'update:page', page: number): void
  (e: 'row-click', id: string): void
}>()

function prevPage() {
  if (props.currentPage && props.currentPage > 1) {
    emit('update:page', props.currentPage - 1)
  }
}

function nextPage() {
  if (props.currentPage && props.totalPages && props.currentPage < props.totalPages) {
    emit('update:page', props.currentPage + 1)
  }
}
</script>

<template>
  <div class="datatable">
    <!-- Loading -->
    <div v-if="loading" class="loading">
      <FontAwesomeIcon :icon="faCircleNotch" class="spinner-icon" spin />
      <span>Cargando...</span>
    </div>

    <!-- Empty -->
    <div v-else-if="!rows?.length" class="empty">
      <span>{{ emptyMessage || 'No hay datos' }}</span>
    </div>

    <!-- Table -->
    <template v-else>
      <table>
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.key">
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, index) in rows"
            :key="row.id ?? index"
            @click="emit('row-click', row.id)"
          >
            <td v-for="column in columns" :key="column.key">
              <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                {{ row[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="totalPages && totalPages > 1" class="pagination">
        <ButtonComponent
          :disabled="currentPage === 1"
          variant="primary"
          size="sm"
          @click="prevPage"
        >
          <FontAwesomeIcon :icon="faChevronLeft" />
        </ButtonComponent>

        <span class="page-info">
          {{ currentPage }} / {{ totalPages }}
        </span>

        <ButtonComponent
          :disabled="currentPage === totalPages"
          variant="primary"
          size="sm"
          @click="nextPage"
        >
          <FontAwesomeIcon :icon="faChevronRight" />
        </ButtonComponent>
      </div>
    </template>
  </div>
</template>

<style scoped>
.datatable {
  width: 100%;
  min-height: 200px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(212, 175, 55, 0.2);
  border-radius: 12px;
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: rgba(6, 68, 54, 0.5);
}

thead th {
  padding: 12px 16px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #d4af37;
  text-align: left;
  border-bottom: 2px solid rgba(212, 175, 55, 0.4);
}

tbody tr {
  transition: background 0.15s;
}

tbody tr:hover {
  background: rgba(212, 175, 55, 0.05);
  cursor: pointer;
}

tbody td {
  padding: 12px 16px;
  font-size: 13px;
  color: #fff;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.loading,
.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  gap: 12px;
  color: #94a3b8;
  font-size: 13px;
}

.spinner-icon {
  font-size: 20px;
  color: #d4af37;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.page-info {
  font-size: 12px;
  color: #94a3b8;
  min-width: 60px;
  text-align: center;
}
</style>