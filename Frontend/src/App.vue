<script setup>
import {ref} from "vue";
import ModalHost from '@/Shared/Components/ModalHost.vue'
import HeaderComponent from "@Shared/Components/HeaderComponent.vue";
import NavBarComponent from "@Shared/Components/NavBarComponent.vue";
import {useLayout} from "@Shared/Composable/useLayout.ts";

const { isMenuOpened } = useLayout()
</script>

<template>
  <div class="app">
    <HeaderComponent />
    <NavBarComponent/>
    <main class="main" :class="{ 'navbar-show': isMenuOpened }">
      <router-view />
      <ModalHost />
    </main>
  </div>
</template>

<style scoped lang="sass">
.app
  margin: 0
  padding: 0
  box-sizing: border-box
main
  flex: 1
  padding: 32px 30px 80px
  height: calc(100vh - 60px)
  margin-top: 60px
  margin-left: 60px
  transition: margin-left 0.3s ease
main.navbar-show
  margin-left: 220px

/* Responsive */
@media screen and (max-width: 1024px)
  .main
    margin-left: 80px

@media screen and (max-width: 768px)
  .main
    margin-left: 0
    padding: 0
</style>
