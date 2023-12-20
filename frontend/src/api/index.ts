import axios from "axios"
import {useAuthStore} from '@/stores/authStore'
import router from '@/router'

const api = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL,
  withCredentials: true,
  withXSRFToken: true
})

export function makeRequest(url, options?){
  return api(url, options)
    .then(res => res.data)
}

api.interceptors.response.use(config => {
  return config
}, async error => {
  if(error.response.status === 401){
    const authStore = useAuthStore()
    authStore.user = null
    router.push('/login')
  }
  throw error
})
