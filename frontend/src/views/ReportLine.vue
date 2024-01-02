<script setup lang="ts">
import {defineProps} from 'vue'
import type {ReportLine} from "@/types"

withDefaults(defineProps<{line: ReportLine}>(), {
  line: () => ({} as ReportLine)
})
</script>

<template>
  <tr>
    <td>{{ line.name }}</td>
    <td>{{ line.orders }}</td>
    <td>{{ line.conversion ? `${line.conversion} %` : '-' }}</td>
    <td>{{ line.sales ? line.sales : '-'}}</td>
    <td>{{ line.revenue ? `${parseInt(line.revenue).toLocaleString('ru-Ru')} &#x20bd;` : '-'}}</td>
    <td>{{ line.average ? `${parseInt(line.average).toLocaleString('ru-Ru')} &#x20bd;` : '-'}}</td>
    <td>{{ line.income ? `${parseInt(line.income).toLocaleString('ru-Ru')} &#x20bd;` : '-'}}</td>
    <td>{{ line.roi ? `${line.roi} %` : '' }}</td>
  </tr>
</template>

<style>
</style>
