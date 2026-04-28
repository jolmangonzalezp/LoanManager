<script setup lang="ts">
import {useRoute} from "vue-router";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {
  faAngleRight,
} from "@fortawesome/free-solid-svg-icons";
import {useLayout} from "@Shared/Composable/useLayout";


const { isMenuOpened, routes, subroutes, menuHandle } = useLayout()
</script>

<template>
<nav :class="{ 'nav-open' : isMenuOpened }">
  <ul class="nav-menu">
    <li v-for="routeItem in routes" :key="routeItem.id" class="nav-item">
      <RouterLink
          :to="routeItem.to"
          :aria-label="routeItem.label"
          class="nav-link"
          v-slot="{ isActive }"
      >
        <div class="nav-icon-container">
          <FontAwesomeIcon :icon="routeItem.icon" class="nav-icon" />
        </div>
        <p class="nav-label">{{routeItem.label}}</p>
        <div class="nav-arrow">
          <FontAwesomeIcon v-if="isActive"  :icon="faAngleRight" class="nav-link-active"/>
        </div>
      </RouterLink>
      <div
          v-if="routeItem.label === 'Reportes'"
          class="submenu"
          :class="menuHandle(routeItem)"

      >
        <li
            v-for="subroute in subroutes"
            :key="subroute.id"
            class="submenu-item"
        >
          <RouterLink
              :to="subroute.to"
              :aria-label="subroute.label"
              v-slot="{ isActive }"
              class="submenu-link"
          >
            <div class="submenu-icon-container">
              <FontAwesomeIcon :icon="subroute.icon" class="nav-icon" />
            </div>
            <p class="submenu-label">{{subroute.label}}</p>
            <div class="submenu-arrow" v-if="isActive">
              <FontAwesomeIcon :icon="faAngleRight" class="submenu-link-active"/>
            </div>
          </RouterLink>
        </li>
      </div>
    </li>
  </ul>
  <nav-footer></nav-footer>
</nav>
</template>

<style scoped lang="sass">
nav
  position: fixed
  top: 60px
  left: 0
  width: 60px
  height: calc(100vh - 60px)
  background: #0a1f1a
  transition: width 0.3s ease-in-out
  z-index: 250
  .nav-menu
    padding: 16px 0
    margin: 0
    list-style: none
    display: flex
    flex-direction: column
    gap: 4px
    .nav-item
      .nav-link
        display: flex
        align-items: center
        width: 100%
        height: 50px
        color: #94a3b8
        text-decoration: none
        overflow: hidden
        &:hover
          background: rgba(212, 175, 55, 0.08)
          color: #e8c84a
        &.router-link-active, &.router-link-exact-active
          background: rgba(212, 175, 55, 0.12)
          color: #d4af37
        .nav-icon-container
          min-width: 60px
          display: flex
          justify-content: center
          align-items: center
          font-size: 1.5rem
          .nav-label
            opacity: 1
            width: 0
            transform: translateX(-20px)
            transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
            white-space: nowrap
            pointer-events: none
          .nav-arrow
            width: 30px
            opacity: 0
            transition: opacity 0.2s
nav.nav-open
  width: 220px
  .nav-menu
    .nav-item
      .nav-link
        .nav-label
          opacity: 1
          width: 135px
          transform: translateX(0)
          pointer-events: auto
        .nav-arrow
          opacity: 1
.submenu
  position: absolute
  top: 0

  width: 220px
  height: calc(100vh - 60px)
  background: #0a1f1a
  box-shadow: 10px 0 15px rgba(0,0,0,0.3)
  z-index: -1
  transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1)
  .submenu-item
    .submenu-link
      display: flex
      justify-content: space-between
      align-items: center
      width: 100%
      height: 50px
      color: #94a3b8
      text-decoration: none
      overflow: hidden
      &:hover
        background: rgba(212, 175, 55, 0.08)
        color: #e8c84a
      &.router-link-active, &.router-link-exact-active
        background: rgba(212, 175, 55, 0.12)
        color: #d4af37
      .submenu-icon-container
        min-width: 60px
        display: flex
        justify-content: center
        align-items: center
        font-size: 1.5rem
      .submenu-label
        opacity: 1
        width: 135px
        transform: translateX(-20px)
        transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
        white-space: nowrap
        pointer-events: none
      .submenu-arrow
        width: 30px
        opacity: 0
        transition: opacity 0.2s
.submenu-hidden
  left: -300px
  opacity: 0
.submenu-expanded-active
  left: 220px
  opacity: 1
.submenu-collapse-active
  left: 60px
  width: 60px;
  opacity: 1

</style>