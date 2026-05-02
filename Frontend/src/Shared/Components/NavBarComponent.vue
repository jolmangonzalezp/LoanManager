<script setup lang="ts">
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {
  faAngleRight, faRightFromBracket,
} from "@fortawesome/free-solid-svg-icons";
import {useLayout} from "@Shared/Composable/useLayout";
import {useAuth} from "@Modules/Auth";
import {computed, onMounted} from "vue";
import {Ava} from "@Shared";


const { isMenuOpened, routes, subroutes, layoutHandler, isReport } = useLayout()
const { user, me, logout, getName } = useAuth()

const initials = computed(() => {
  if (!user.value) return "??"
  const parts = [
    user.value.name?.[0] || '',
    user.value.lastname?.[0] || ''
  ].filter(Boolean)
  return parts.slice(0,2).join('').toUpperCase() || '??'
})

const closeMenu = () => {
  if (!isReport.value) {
    isMenuOpened.value = false
  }

}

onMounted(async () => {
  await me();
})
</script>

<template>
<nav :class=" [layoutHandler, isMenuOpened ? 'open' : 'close']">
  <ul class="nav-menu">
    <li v-for="routeItem in routes" :key="routeItem.id" class="nav-item" @click="closeMenu">
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
      <ul
          v-if="routeItem.label === 'Reportes'"
          class="submenu"
      >
        <li
            v-for="subroute in subroutes"
            :key="subroute.id"
            class="submenu-item"
            @click="closeMenu"
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
      </ul>
    </li>
  </ul>
  <div class="nav-footer">
    <div class="nav-footer-item">
      <div class="nav-footer-icon">
        <Ava :initials="initials"></Ava>
      </div>
      <p class="nav-footer-label">{{ user.name }}</p>
    </div>
    <div class="nav-footer-item" @click="logout">
      <div class="nav-footer-icon">
        <FontAwesomeIcon :icon="faRightFromBracket" />
      </div>
      <p class="nav-footer-label">Salir</p>
    </div>
  </div>
</nav>
</template>

<style scoped lang="sass">
nav
  position: fixed
  top: 60px
  left: 0
  padding: 0
  margin: 0
  height: calc(100vh - 60px)
  background: #0a1f1a
  transition: width 0.3s ease
  z-index: 250
  display: flex
  flex-direction: column
  justify-content: space-between
  align-items: flex-start
  .nav-menu
    max-width: 220px
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
        .nav-icon-container
          width: 60px
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
          opacity: 1
          transition: opacity 0.2s
        &:hover
          background: rgba(212, 175, 55, 0.08)
          color: #e8c84a
        &.router-link-active, &.router-link-exact-active
          background: rgba(212, 175, 55, 0.12)
          color: #d4af37
      .submenu
        position: absolute
        top: 0
        left: 60px
        height: calc(100vh - 60px)
        background: #0a1f1a
        box-shadow: 10px 0 15px rgba(0,0,0,0.3)
        z-index: -1
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1)
        .submenu-item
          list-style: none
          .submenu-link
            display: flex
            align-items: center
            width: 100%
            height: 50px
            color: #94a3b8
            text-decoration: none
            overflow: hidden
            .submenu-icon-container
              width: 60px
              display: flex
              justify-content: center
              align-items: center
              font-size: 1.5rem
            .submenu-label
              opacity: 1
              width: 0
              transform: translateX(-20px)
              transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
              white-space: nowrap
              pointer-events: none
            .submenu-arrow
              width: 30px
              opacity: 1
              transition: opacity 0.2s
            &:hover
              background: rgba(212, 175, 55, 0.08)
              color: #e8c84a
            &.router-link-active, &.router-link-exact-active
              background: rgba(212, 175, 55, 0.12)
              color: #d4af37
  .nav-footer
    max-width: 220px
    padding: 16px 0
    margin: 0
    list-style: none
    display: flex
    flex-direction: column
    gap: 4px
    .nav-footer-item
      display: flex
      align-items: center
      width: 100%
      height: 50px
      color: #94a3b8
      overflow: hidden
      .nav-footer-icon
        width: 60px
        display: flex
        justify-content: center
        align-items: center
        font-size: 1.5rem
      .nav-footer-label
        opacity: 1
        width: 0
        transform: translateX(-20px)
        transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
        white-space: nowrap
        pointer-events: none
      &:last-child
        background: #dc2626
        cursor: pointer
        .nav-footer-icon
          color: #fff
        .nav-footer-label
          color: #fff

nav.layout-slim
  width: 60px
  .nav-menu
    .nav-item
      .nav-link
        .nav-label
          opacity: 0
          width: 0
          transform: translateX(-20px)
          transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
          white-space: nowrap
          pointer-events: none
        .nav-arrow
          width: 0
          opacity: 0
          transition: opacity 0.2s
      .submenu
        left: -220px
  .nav-footer
    .nav-footer-item
      .nav-footer-label
        opacity: 0
        width: 0
        transform: translateX(-20px)
        transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
        white-space: nowrap
        pointer-events: none

nav.layout-wide
  width: 220px
  .nav-menu
    .nav-item
      .nav-link
        .nav-label
          opacity: 1
          width: 130px
          transform: translateX(0)
          pointer-events: auto
      .submenu
        left: -220px
  .nav-footer
    .nav-footer-item
      .nav-footer-label
        opacity: 1
        width: 160px
        transform: translateX(0)
        pointer-events: auto
nav.layout-slim-drawer
  width: 60px
  .nav-menu
    .nav-item
      .nav-link
        .nav-label
          opacity: 0
          width: 0
          transform: translateX(-20px)
          transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
          white-space: nowrap
          pointer-events: none
        .nav-arrow
          width: 0
          opacity: 0
          transition: opacity 0.2s
      .submenu
        left: 60px
        width: 220px
        .submenu-item
          .submenu-link
            .submenu-label
              opacity: 1
              width: 130px
              transform: translateX(0)
              pointer-events: auto
  .nav-footer
    .nav-footer-item
      .nav-footer-label
        opacity: 0
        width: 0
        transform: translateX(-20px)
        transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
        white-space: nowrap
        pointer-events: none

nav.layout-wide-drawer
  width: 220px
  .nav-menu
    .nav-item
      .nav-link
        .nav-label
          opacity: 1
          width: 130px
          transform: translateX(0)
          pointer-events: auto
        .nav-arrow
          width: 30px
          opacity: 1
          transition: opacity 0.2s
      .submenu
        left: 220px
        width: 60px
        .submenu-item
          .submenu-link
            .submenu-label
              opacity: 0
              width: 0
              transform: translateX(-20px)
              transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
              white-space: nowrap
              pointer-events: none
            .submenu-arrow
              width: 0
              opacity: 0
              transition: opacity 0.2s
  .nav-footer
    .nav-footer-item
      .nav-footer-label
        opacity: 1
        width: 160px
        transform: translateX(0)
        pointer-events: auto

@media (screen and max-width: 769px)
  nav.close
    height: auto
    transform: translateY(-100vh)
    transition: transform 0.3s ease
  nav.open
    height: auto
    transform: translateY(0)
    transition: transform 0.3s ease

  nav.layout-slim-drawer.open, nav.layout-wide-drawer.open
    width: 60px
    .nav-menu
      .nav-item
        .nav-link
          .nav-label
            opacity: 0
            width: 0
            transform: translateX(-20px)
            transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
            white-space: nowrap
            pointer-events: none
          .nav-arrow
            width: 0
            opacity: 0
            transition: opacity 0.2s
        .submenu
          left: 60px
          width: 220px
          height: auto
          .submenu-item
            .submenu-link
              .submenu-label
                opacity: 1
                width: 130px
                transform: translateX(0)
                pointer-events: auto
    .nav-footer
      .nav-footer-item
        .nav-footer-label
          opacity: 0
          width: 0
          transform: translateX(-20px)
          transition: opacity 0.2s ease, transform 0.3s ease, width 0.3s ease
          white-space: nowrap
          pointer-events: none


</style>