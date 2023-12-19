import {ref} from 'vue'
import {defineStore} from 'pinia'
import { getReportData } from '@/api/bitrix24'

export const useBitrix24Store = defineStore('bitrix24Store', () => {
  const reportData =ref([])
  const loading = ref(false)

  const fetchReportData = async () => {
    try{
      loading.value = true
      const data = await getReportData('/api/v1/report')
      reportData.value = data
    } catch(e){
      console.log(e)
    } finally {
      loading.value = false
    }
  }

  return {
    reportData,
    loading,
    fetchReportData
  }
})
