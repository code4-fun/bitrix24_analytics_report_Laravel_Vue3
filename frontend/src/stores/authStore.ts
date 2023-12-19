import { ref, watch } from 'vue'
import type { Ref } from 'vue'
import { defineStore } from 'pinia'
import { getTokenApi, getUserApi, loginApi, registerApi, logoutApi } from '@/api/auth'
import router from '@/router'
import type { IUser } from '@/types'
import type { AxiosResponse } from "axios";

export const useAuthStore = defineStore('authStore', () => {
  const user: Ref<IUser> = ref(null)
  const errors = ref([])
  const loading = ref(false)

  const userInLocalStorage = localStorage.getItem("user")

  if (userInLocalStorage) {
    user.value = JSON.parse(userInLocalStorage)._value
  }

  watch(
    () => user,
    newState => localStorage.setItem("user", JSON.stringify(newState)),
    { deep: true }
  )

  const getToken = async () => {
    await getTokenApi('/sanctum/csrf-cookie')
  }

  const getUser = async () => {
    const data = await getUserApi<AxiosResponse<IUser>>('/api/user')
    user.value = data.data
  }

  const loginHandler = async data => {
    errors.value = []
    await getToken()
    try {
      await loginApi('/login', {
        email: data.email,
        password: data.password
      })
      await getUser()
      router.push({name: data.to})
    } catch (e) {
      if (e.response.status === 422) {
        errors.value = e.response.data.errors
      }
    } finally {
      loading.value = false
    }
  }

  const registerHandler = async data => {
    errors.value = []
    await getToken()
    try {
      await registerApi('/register', {
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation
      })
      await getUser()
      router.push('/')
    } catch (e) {
      if (e.response.status === 422) {
        errors.value = e.response.data.errors
      }
    } finally {
      loading.value = false
    }
  }

  const logoutHandler = async () => {
    await logoutApi('/logout')
    user.value = null
    router.push('/')
  }

  return {
    user,
    errors,
    loading,
    getUser,
    loginHandler,
    registerHandler,
    logoutHandler
  }
})
