<script setup lang="ts">
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { faBars, faXmark } from "@fortawesome/free-solid-svg-icons";
import {useLayout} from "@Shared/Composable/useLayout";

const { isMenuOpened, toggleIconMenu } = useLayout()

</script>

<template>
<header>
  <div class="toggle" @click="toggleIconMenu">
    <FontAwesomeIcon :icon="isMenuOpened ? faXmark : faBars"></FontAwesomeIcon>
  </div>
  <div class="logo">
    <img src="/logo.webp" alt="LoanManager-logo">
  </div>
  <div class="header-spacer"></div>
</header>
</template>

<style scoped lang="sass">
header
  width: 100%
  height: 60px
  position: fixed
  top: 0
  left: 0
  margin: 0
  padding: 0
  display: flex
  align-items: center
  justify-content: space-between
  background: #0a1f1a
  z-index: 100
  .toggle
    width: 60px
    height: 60px
    display: flex
    align-items: center
    justify-content: center
    font-size: 2rem
    color: #d4af37
    cursor: pointer
  .logo
    width: 60px
    height: 60px
    padding: 7.5px
    border-radius: 50%
    background: #fff
    display: flex
    justify-content: center
    align-items: center
    img
      width: 48px
  .header-spacer
    width: 60px
</style>