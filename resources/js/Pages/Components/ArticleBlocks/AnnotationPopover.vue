<!-- components/AnnotationPopover.vue -->
<script setup>
defineProps({
  title: String,
  description: String,
  x: Number,
  y: Number,
  visible: Boolean,
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="visible"
      class="fixed z-50 bg-white p-3 rounded shadow-lg border border-gray-200 max-w-xs text-sm"
      :style="{ left: x + 'px', top: y + 'px' }"
    >
      <h4 class="font-semibold text-gray-900">{{ title }}</h4>
      <p class="mt-1 text-gray-600">{{ description }}</p>
    </div>
  </Teleport>
</template>