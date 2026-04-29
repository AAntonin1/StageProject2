<script setup>
import { computed } from 'vue';

const props = defineProps({
    realSumTotal: [Number, String],
    homeWorkDistance: [Number, String],
    totalDistance: [Number, String],
    form: Object
});

const realDeduction = computed(() => {
    const diff = (parseFloat(props.realSumTotal) || 0) - (parseFloat(props.totalDistance) || 0);

    return diff.toFixed(2);
});
</script>

<template>
    <div class="mt-8 bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-slate-200/50">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xs font-black uppercase text-slate-900 mb-3">Récapitulatif du calcul</h3>
            <div class="bg-blue-50 px-4 py-1 rounded-full text-[10px] font-bold text-blue-700 border border-blue-100 uppercase tracking-tighter">
                Triangulation par segment
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-600 uppercase tracking-tight">Distance parcourue</p>
                        <p class="text-[10px] text-slate-600 italic">Somme de tous vos segments</p>
                    </div>
                </div>
                <span class="text-lg font-black text-slate-700">{{ realSumTotal }} <small class="text-xs text-slate-600">KM</small></span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-600 uppercase tracking-tight">Déduction Triangulation</p>
                        <p class="text-[10px] text-slate-600 italic">Franchise sur 1er et dernier trajet</p>
                    </div>
                </div>
                <span class="text-lg font-black text-rose-600">- {{ realDeduction }} <small class="text-xs text-rose-600">KM</small></span>
            </div>

            <div class="h-px bg-slate-100 w-full relative"></div>

            <div class="flex items-end justify-between pt-2">
                <div>
                    <p class="text-xs font-black uppercase text-blue-600">Total Remboursable</p>
                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Calculé selon les règles URSSAF</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-black text-slate-900 leading-none tracking-tighter">
                        {{ totalDistance }}<span class="text-sm ml-1 text-slate-600 font-bold uppercase">km</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
