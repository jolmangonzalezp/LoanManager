<script setup>
import {ref} from "vue";
import ModalHost from '@/Shared/Components/ModalHost.vue'
import HeaderComponent from "@Shared/Components/HeaderComponent.vue";
import NavBarComponent from "@Shared/Components/NavBarComponent.vue";
import {useLayout} from "@Shared/Composable/useLayout.ts";

const { layoutHandler } = useLayout()
</script>

<template>
  <div class="app">
    <HeaderComponent />
    <NavBarComponent/>
    <main class="main" :class="layoutHandler">
      <router-view />
      <ModalHost />
    </main>
  </div>
</template>

<style scoped lang="sass">
ul, ol, li
  list-style: none
  text-decoration: none
.app
  margin: 0
  padding: 0
  box-sizing: border-box
main
  flex: 1
  padding: 32px 30px 80px
  height: calc(100vh - 60px)
  margin-top: 60px
  transition: margin-left 0.3s ease
main.layout-slim
  margin-left: 60px
main.layout-wide
  margin-left: 220px
main.layout-slim-drawer, main.layout-wide-drawer
  margin-left: 280px

/* Responsive */
@media screen and (max-width: 1024px)
  .main
    margin-left: 80px

@media screen and (max-width: 768px)
  .main
    margin-left: 0
    padding: 0
</style>
