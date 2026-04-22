<script setup>

const homeWorkDistance = defineModel('homeWorkDistance');

</script>
<template>
  <div class="bg-white rounded-3xl p-6 mb-6 border border-slate-200 shadow-xl shadow-slate-200/50">
    <label class="text-xs font-black uppercase text-slate-900 block mb-3" for="homeWorkDistance">
      Trajet Habituel Aller uniquement (Déduction)
    </label>
    <div class="flex items-center gap-4">
      <div class="relative">
        <input
            type="number"
            v-model="homeWorkDistance"
            min="0"
            id="homeWorkDistance"
            class="w-28 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-slate-900 p-3 pr-10"
        />
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-600">KM</span>
      </div>
      <p class="text-xs font-semibold text-slate-600">
        Kilomètres déduits du total car non remboursables (trajet domicile-travail).
      </p>
    </div>
  </div>
</template>