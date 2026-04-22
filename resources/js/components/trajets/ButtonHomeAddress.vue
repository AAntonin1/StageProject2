<script setup>
const emit = defineEmits(['add-address-button-to-segment']);
</script>
<template>
  <button type="button"
          @click="emit('add-address-button-to-segment')"
          name="Ajouter une adresse de domicile"
          class="flex items-center justify-center h-10 w-10 shrink-0 bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-700 rounded-xl transition-colors border border-slate-200 shadow-sm self-end mb-[1px]">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
      <polyline points="9 22 9 12 15 12 15 22"></polyline>
    </svg>
  </button>
</template>