<script setup>
import { ref, inject, defineAsyncComponent, watch } from 'vue';
const AddressAutocomplete = defineAsyncComponent(() => import('@/components/trajets/AddressAutocomplete.vue'));

const isOpen = ref(false);
const km_rate = defineModel('km_rate');

const { addressHomeRef } = inject('dataHomeAddress');
const { addressWorkRef } = inject('dataWorkAddress');

</script>

<template>
  <button
      @click="isOpen = true"
      aria-label="Ouvrir les paramètres"
      class="fixed top-3 md:top-6 right-6 z-30 p-3 bg-white shadow-lg shadow-slate-200 rounded-2xl border border-slate-100 text-slate-900 hover:scale-110 active:scale-95 transition-all">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7" />
    </svg>
  </button>

  <transition
      enter-active-class="transition-opacity duration-300"
      leave-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0">
    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
  </transition>

  <transition
      enter-active-class="transition-transform duration-300 ease-out"
      leave-active-class="transition-transform duration-200 ease-in"
      enter-from-class="translate-x-full"
      leave-to-class="translate-x-full">
    <aside v-if="isOpen" class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 flex flex-col">
      <div class="p-6 border-b border-slate-50 flex justify-between items-center">
        <h2 class="text-sm font-black uppercase tracking-tighter text-slate-900">Paramètres de l'application</h2>
        <button
            name="Fermer les paramètres"
            @click="isOpen = false"
            class="p-2 hover:bg-slate-100 rounded-full transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Rate km -->
      <div class="flex-1 overflow-y-auto p-6 space-y-8">
        <section class="space-y-4">
          <div class="flex items-center justify-between">
            <label class="text-[10px] font-black uppercase text-slate-400">Tarif Kilométrique</label>
            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">€ / KM</span>
          </div>

          <div class="relative group">
            <input
                type="number"
                step="0.001"
                class="w-full bg-slate-50 border-2 border-transparent focus:border-slate-900 focus:ring-0 rounded-2xl py-4 px-5 font-black text-xl text-slate-900 transition-all"
                v-model="km_rate"
            />
            <div class="absolute right-12 top-1/2 -translate-y-1/2 pointer-events-none">
              <span class="text-slate-300 font-black">€</span>
            </div>
          </div>
        </section>

        <!-- Home address -->
        <section class="space-y-4">
          <div class="flex items-center justify-between">
            <label class="text-[10px] font-black uppercase text-slate-400">Adresse Maison</label>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">FAVORIS</span>
          </div>

          <div class="relative group">
            <AddressAutocomplete
                v-model:address="addressHomeRef"
                placeholder="Votre adresse de domicile"
                class="flex-1"/>
          </div>
          <p v-if="addressHomeRef?.label" class="text-[10px] text-slate-400 italic">
            Configuré : {{ addressHomeRef.label }}
          </p>
        </section>

        <!-- Work address -->
        <section class="space-y-4">
          <div class="flex items-center justify-between">
            <label class="text-[10px] font-black uppercase text-slate-400">Adresse travail</label>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">FAVORIS</span>
          </div>

          <div class="relative group">
            <AddressAutocomplete
                v-model:address="addressWorkRef"
                placeholder="Votre adresse de travail"
                class="flex-1"/>
          </div>
          <p v-if="addressWorkRef?.label" class="text-[10px] text-slate-400 italic">
            Configuré : {{ addressWorkRef.label }}
          </p>
        </section>
      </div>

      <div class="p-6 bg-slate-50">
        <button
            @click="isOpen = false"
            name="Appliquer les paramètres"
            class="w-full bg-slate-900 text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest hover:opacity-90 transition-opacity">
          Appliquer
        </button>
      </div>
    </aside>
  </transition>
</template>
