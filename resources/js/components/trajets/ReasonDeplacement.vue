<script setup>

const reason = defineModel('reason');

</script>
<template>
    <input type="text" v-model="reason" placeholder="Motif du déplacement..."
           class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"/>
</template>