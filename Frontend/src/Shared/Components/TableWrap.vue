<script setup>
defineProps({
  headers: { type: Array, required: true }
})
</script>

<template>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th v-for="h in headers" :key="h">{{ h }}</th>
        </tr>
      </thead>
      <tbody>
        <slot />
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.table-wrap {
  background: rgba(0,0,0,0.2);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 8px;
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead tr th {
  padding: 12px 16px;
  text-align: center;
  font-size: 12px;
  color: #d4af37;
  font-weight: 600;
  border-bottom: 1px solid rgba(212,175,55,0.28);
  background: rgba(0,0,0,0.2);
}

tbody tr {
  cursor: default;
  transition: background 0.12s;
}

tbody tr:hover {
  background: rgba(212,175,55,0.04);
}

tbody tr td {
  padding: 13px 16px;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.03);
  font-size: 13.5px;
  color: #94a3b8;
  vertical-align: middle;
}
</style>