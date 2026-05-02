<script setup>
import {onMounted} from "vue";
import ModalHost from '@/Shared/Components/ModalHost.vue'
import HeaderComponent from "@Shared/Components/HeaderComponent.vue";
import NavBarComponent from "@Shared/Components/NavBarComponent.vue";
import {useLayout} from "@Shared/Composable/useLayout.ts";

const { isMenuOpened, layoutHandler, login } = useLayout()

</script>

<template>
  <div class="app">
    <div v-if="!login">
      <HeaderComponent />
      <NavBarComponent />
    </div>
    <main class="main" :class="[layoutHandler, isMenuOpened ? 'open' : 'close']">
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
@media screen and (max-width: 769px)
  main, main.layout-slim, main.layout-wide, main.layout-slim-drawer, main.layout-wide-drawer
    margin-left: 0
    width: 100%
    height: 100%;
</style>
