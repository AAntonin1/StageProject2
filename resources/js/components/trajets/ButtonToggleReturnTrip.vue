<script setup>
import { computed } from 'vue';

const props = defineProps({
  hasReturnTrip: Boolean
});

const emit = defineEmits(['toggle-return-trip']);
</script>
<template>
  <button
      @click="emit('toggle-return-trip')"
      type="button"
      name="Toggle return trip"
      :class="hasReturnTrip
                ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'
                : 'bg-white text-slate-900 border-slate-900 hover:bg-slate-900 hover:text-white'"
      class="w-full py-4 border-2 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2"
  >
    <svg v-if="!hasReturnTrip" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>

    {{ hasReturnTrip ? 'Supprimer le trajet de retour' : 'Générer le trajet de retour' }}
  </button>
</template>