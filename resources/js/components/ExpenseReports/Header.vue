<script setup>

const props = defineProps({
  totalDistance: {
    type: Number,
    required: true
  }
});
</script>
<template>
  <header class="mb-8 flex flex-row justify-between items-center md:items-end">
    <div class="flex flex-row items-baseline gap-3 md:flex-col md:gap-0">
      <h1 class="text-xl md:text-2xl text-slate-900 font-black uppercase">Itinéraire</h1>
      <div class="md:hidden flex items-baseline gap-1">
        <span class="text-xl font-black text-slate-900">{{ totalDistance }}</span>
        <span class="text-[10px] font-bold text-slate-600 uppercase">km</span>
      </div>
      <p class="hidden md:block text-slate-500 text-sm font-medium">Saisie de vos déplacements</p>
    </div>

    <div class="hidden md:block text-right">
      <span class="block text-xs font-bold text-slate-400 uppercase">Distance totale</span>
      <span class="text-3xl font-black">{{ totalDistance }} <small class="text-sm font-bold text-slate-500">KM</small></span>
    </div>

  </header>
</template>