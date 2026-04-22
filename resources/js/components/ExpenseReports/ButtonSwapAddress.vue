<script setup>
const emit = defineEmits(['swap-address']);
</script>
<template>
  <div class="relative flex h-0 z-20 pointer-events-none">
    <div class="absolute inset-x-0 -top-4 flex justify-center pl-6 pr-[104px] pt-1">
      <button type="button"
              @click="emit('swap-address')"
              name="Permuter les adresses de domicile et de travail"
              class="pointer-events-auto flex items-center justify-center h-8 w-8 bg-white border border-slate-200 rounded-full shadow-md text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all duration-300 hover:rotate-180 active:scale-90 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/>
        </svg>
      </button>
    </div>
  </div>
</template>
