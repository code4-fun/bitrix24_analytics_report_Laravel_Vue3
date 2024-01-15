<script setup lang="ts">
import {useAuthStore} from '@/stores/authStore'
import {useBitrix24Store} from '@/stores/bitrix24Store'

const authStore = useAuthStore()
const bitrix24Store = useBitrix24Store()
</script>

<template>
  <div class="wrapper">
    <header class="header">
      <div class="header_container container">
        <div class="header_left">
          <RouterLink to='/home'>Home</RouterLink>
          <RouterLink v-if='authStore.user' to='/integration'>Integration</RouterLink>
          <RouterLink v-if='authStore.user' to='/report'>Report</RouterLink>
        </div>
        <div class="header_right">
          <div v-if='authStore.user'>
            <div @click='authStore.logoutHandler'>Sign out</div>
          </div>
          <div v-else>
            <RouterLink to='/login'>Sigh in</RouterLink>
            <RouterLink to='/register'>Sign up</RouterLink>
          </div>
        </div>
      </div>
    </header>

    <main class="page">
      <div :class="['integration_message', bitrix24Store.integrationStatus === 200 ? 'success' : 'failure']"
           v-if="bitrix24Store.integrationMessage">
        {{ bitrix24Store.integrationMessage }}
      </div>
      <div class="main_container container">
        <router-view/>
      </div>
    </main>

    <footer class="footer">
      <div class="container">
        &copy; Bitrix24 analytics 2023
      </div>
    </footer>
  </div>
</template>

<style scoped>
.header {
  height: 50px;
  width: 100%;
  position: fixed;
  background-color: var(--primary-color);
  box-shadow: 0 0 7px 3px rgba(0, 0, 0, .1);
}
.header_container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 50px;
}
.header_left > *:not(:last-child) {
  padding-right: 20px;
}
.header_right *:not(:last-child) {
  padding-right: 20px;
}
.wrapper{
  display: flex;
  flex-direction: column;
  min-height: 100%;
  overflow: hidden;
}
.page {
  flex: auto;
  padding-top: 80px;
  display: flex;
}
.container {
  max-width: 1000px;
  padding: 0 15px;
  margin: 0 auto;
  box-sizing: content-box;
}
.main_container {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  width: 100%;
}
.page_content {
  margin: auto;
}
.footer {
  text-align: center;
  padding: 10px 0;
}
.integration_message {
  position: absolute;
  top: 51px;
  left: 1px;
  padding: 10px;
  margin-right: 10px;
  font-size: 18px;
}
.integration_message.success {
  background-color: var(--success-color);
}
.integration_message.failure {
  background-color: var(--failure-color);
}
</style>
