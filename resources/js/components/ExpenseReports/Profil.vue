<script setup>
const firstName = defineModel('first_name');
const lastName = defineModel('last_name');
const address = defineModel('address');
</script>

<template>
    <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 mb-8">
        <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h2 class="text-xs font-black uppercase text-slate-900 tracking-wider">
                Informations Personnelles
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1">Prénom</label>
                <input
                    type="text"
                    v-model="firstName"
                    placeholder="Ex: Jean"
                    class="p-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all"
                />
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1">Nom</label>
                <input
                    type="text"
                    v-model="lastName"
                    placeholder="Ex: Dupont"
                    class="p-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all"
                />
            </div>

            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1">Adresse postale complète</label>
                <input
                    type="text"
                    v-model="address"
                    placeholder="Numéro, rue, code postal et ville"
                    class="p-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all"
                />
            </div>
        </div>
    </section>
</template>
