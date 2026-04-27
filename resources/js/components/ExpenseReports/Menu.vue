<script setup>
import { ref, inject, defineAsyncComponent } from 'vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

const AddressAutocomplete = defineAsyncComponent(() => import('@/components/ExpenseReports/AddressAutocomplete.vue'));

const isOpen = ref(false);

const km_rate = defineModel('km_rate');
const firstName = defineModel('first_name');
const lastName = defineModel('last_name');
const job = defineModel('job');
const vehicle = defineModel('vehicle');
const numberPlate = defineModel('number_plate');
const placeBusiness = defineModel('place_business');


const { addressHomeRef } = inject('dataHomeAddress');
const { addressWorkRef } = inject('dataWorkAddress');

const logout = () => {
    router.post(route('user.logout'));
    localStorage.clear();
};
</script>

<template>
    <button
        @click="isOpen = true"
        class="fixed top-3 md:top-6 right-6 z-30 p-3 bg-white shadow-lg rounded-2xl border border-slate-100 text-slate-900 hover:scale-110 active:scale-95 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <transition enter-active-class="transition-opacity duration-300" leave-active-class="transition-opacity duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    </transition>

    <transition enter-active-class="transition-transform duration-300 ease-out" leave-active-class="transition-transform duration-200 ease-in" enter-from-class="translate-x-full" leave-to-class="translate-x-full">
        <aside v-if="isOpen" class="fixed top-0 right-0 h-full w-full sm:w-[480px] bg-white shadow-2xl z-50 flex flex-col">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h2 class="text-sm font-black uppercase tracking-tighter text-slate-900">Paramètres du profil</h2>
                <button @click="isOpen = false" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8">

                <section class="space-y-4">
                    <label class="text-[10px] font-black uppercase text-slate-400">Identité</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Prénom</span>
                            <input type="text" v-model="firstName" placeholder="Prénom" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Nom</span>
                            <input type="text" v-model="lastName" placeholder="Nom" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <label class="text-[10px] font-black uppercase text-slate-400">Localisations</label>
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Domicile</span>
                            <AddressAutocomplete v-model:address="addressHomeRef" placeholder="Adresse privée" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Lieu de travail principal</span>
                            <AddressAutocomplete v-model:address="addressWorkRef" placeholder="Adresse du bureau" />
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <label class="text-[10px] font-black uppercase text-slate-400">Professionnel & Véhicule</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Métier</span>
                            <input type="text" v-model="job" placeholder="Ex: Développeur" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Ville d'affectation</span>
                            <input type="text" v-model="placeBusiness" placeholder="Ex: Libramont" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Modèle Véhicule</span>
                            <input type="text" v-model="vehicle" placeholder="Ex: Tesla Model 3" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-slate-400 ml-1 uppercase font-bold">Plaque</span>
                            <input type="text" v-model="numberPlate" placeholder="Ex: 1-ABC-123" class="w-full bg-slate-50 border-none rounded-xl p-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900" />
                        </div>
                    </div>
                </section>

                <section v-if="$page.props.auth.user.roles.includes('admin')" class="space-y-4 pt-4 border-t border-slate-50">
                    <label class="text-[10px] font-black uppercase text-slate-400">Configuration Admin (Tarif km)</label>
                    <div class="relative">
                        <input type="number" step="0.001" class="w-full bg-slate-900 border-none rounded-2xl py-4 px-5 font-black text-xl text-white focus:ring-0" v-model="km_rate" />
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">€ / KM</span>
                    </div>
                </section>
            </div>

            <div class="p-6 bg-slate-50 space-y-3">
                <button @click="isOpen = false" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black uppercase text-xs hover:bg-slate-800 transition-colors">Enregistrer les modifications</button>
                <button @click="logout" class="w-full py-4 bg-white border border-slate-200 text-red-500 rounded-xl text-xs font-black uppercase hover:bg-red-50 transition-colors">Déconnexion</button>
            </div>
        </aside>
    </transition>
</template>
