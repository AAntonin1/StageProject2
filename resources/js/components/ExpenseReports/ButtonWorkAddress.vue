<script setup>
  const emit = defineEmits(['add-address-button-to-segment']);
</script>
<template>
  <button type="button"
          @click="emit('add-address-button-to-segment')"
          name="Ajouter une adresse de travail"
          class="flex items-center justify-center h-10 w-10 shrink-0 bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-700 rounded-xl transition-colors border border-slate-200 shadow-sm self-end mb-[1px]">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
    </svg>
  </button>
</template>