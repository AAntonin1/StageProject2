<script setup>
const emit = defineEmits(['add-step']);


</script>
<template>
  <button @click="emit('add-step')" type="button"
          name="Ajouter une mission / destination"
          class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-600 text-sm font-bold hover:bg-slate-50 hover:border-slate-400 transition-all flex items-center justify-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
    Ajouter une mission / destination
  </button>
</template>