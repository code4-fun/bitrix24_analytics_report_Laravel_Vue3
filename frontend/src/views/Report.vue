<script setup lang="ts">
import {onMounted, onUnmounted} from 'vue'
import {useBitrix24Store} from '@/stores/bitrix24Store'
import ReportLine from './ReportLine.vue'
import type {ReportLine as ReportLineType} from "@/types"

const bitrix24Store = useBitrix24Store()

onMounted(async () => {
  await bitrix24Store.fetchReportData()
})

onUnmounted(() => {
  bitrix24Store.reportMessage = ''
  bitrix24Store.reportStatus = 0
  bitrix24Store.reportData = []
  bitrix24Store.reportDataTotal = {}
})
</script>

<template>
  <div v-if="bitrix24Store.loading" class="loader" />
  <div v-else-if="bitrix24Store.reportStatus === 500" class="page_content">
    {{ bitrix24Store.reportMessage }}
  </div>
  <table v-else class="table">
    <thead>
      <tr>
        <th>Название канала</th>
        <th><div>Заявки</div></th>
        <th><div>Конверсия в продажи</div></th>
        <th><div>Продажи</div></th>
        <th><div>Выручка</div></th>
        <th><div>Средний чек</div></th>
        <th><div>Прибыль</div></th>
        <th><div>ROI</div></th>
      </tr>
    </thead>

    <tbody>
      <ReportLine :line="bitrix24Store.reportDataTotal as ReportLineType" />
      <ReportLine
        v-for="(line, index) of bitrix24Store.reportData"
        :key="`${index}-${line.name}`"
        :line="line" />
    </tbody>
  </table>
</template>

<style>
.table {
  border-collapse: collapse;
  width: 100%;
  font-size: 12px;
}
thead {
  font-weight: 600;
  background-color: #fff;
}
thead tr th:first-child {
  text-align: left;
}
thead tr th:not(:first-child) div {
  text-align:left;
  margin: 0 auto;
  width:min-content;
}
thead tr th:not(:last-child) {
  border-right: solid 1px #eeeeee;
}
tbody tr td:first-child {
  border-right: solid 1px #eeeeee;
}
tbody tr td:first-child {
  text-align: left;
}
tbody tr:first-child {
  background-color: #FAF7E9;
}
tbody tr:not(:first-child) {
  background-color: #fff;
}
.table th,
.table td {
  padding: 10px;
}
.table th {
  border-top: solid 1px #eeeeee;
  border-bottom: solid 1px #eeeeee;
  padding: 12px;
}
.table td {
  border-bottom: solid 1px #eeeeee;
  text-align: center;
  padding: 10px;
}
thead tr th:nth-child(1) {
  width: 30%;
}
thead tr th:nth-child(2) {
  width: 6%;
}
thead tr th:nth-child(3) {
  width: 12%;
}
thead tr th:nth-child(4) {
  width: 12%;
}
tbody tr:not(:first-child) td:first-child {
  padding-left: 25px;
}
tbody tr td:not(:first-child) {
  white-space: nowrap;
}
tbody tr:first-child td:first-child {
  color: lightgrey;
}
tbody tr:first-child td:not(:first-child) {
  font-weight: 600;
}
</style>
