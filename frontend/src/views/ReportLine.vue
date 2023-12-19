<script setup lang="ts">
defineProps({
  line: {
    type: Object,
    required: true,
    default: () => {},
  }
})
</script>

<template>
  <tr>
    <td>{{ line.name }}</td>
    <td>{{ line.orders }}</td>
    <td>{{ line.conversion ? `${line.conversion} %` : '-' }}</td>
    <td>{{ line.sales ? line.sales : '-'}}</td>
    <td>{{ line.revenue ? `${line.revenue} &#x20bd;` : '-'}}</td>
    <td>{{ line.average ? `${line.average} &#x20bd;` : '-'}}</td>
    <td>{{ line.income ? `${line.income} &#x20bd;` : '-'}}</td>
    <td>{{ line.roi ? `${line.roi} %` : '' }}</td>
  </tr>
</template>

<style>
</style>
