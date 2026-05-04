<script setup>

const props = defineProps({
  required: Boolean
});

const typeDoc = defineModel('typeDoc');

</script>
<template>
  <label for="typeDoc" class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Type de document:</label>
  <select
      name="typeDoc"
      id="typeDoc"
      v-model="typeDoc"
      class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
      :required="props.required"
  >
    <option value="EAM">EAM</option>
    <option value="EP">EP</option>
    <option value="FLJ">FLJ</option>
    <option value="MAC-Lux">MAC-Lux</option>
    <option value="Drugs'Care - agrément">Drugs'Care - agrément</option>
    <option value="VIELSAM">VIELSAM</option>
    <option value="BDN Prison Arlon">BDN Prison Arlon</option>
    <option value="BDN Prison St-Hubert">BDN Prison St-Hubert</option>
    <option value="BDN Rue (remb. par MV)">BDN Rue (remb. par MV)</option>

  </select>
</template>
