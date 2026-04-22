<script setup>

const first_name = defineModel('first_name');
const last_name = defineModel('last_name');
const address = defineModel('address');
const placeBusiness = defineModel('place-business');
const job = defineModel('job');
const vehicle = defineModel('vehicle');
const numberPlate = defineModel('number-plate');

</script>
<template>
  <div class="bg-white rounded-3xl p-6 mb-6 border border-slate-200 shadow-xl shadow-slate-200/50">
    <h2 class="text-xs font-black uppercase text-slate-900 mb-3">Informations personnelles</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="first_name">Prénom</label>
        <input
            type="text"
            v-model="first_name"
            id="first_name"
            placeholder="Jean"
            class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        />
      </div>
      <div>
        <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="last_name">Nom</label>
        <input
            type="text"
            v-model="last_name"
            id="last_name"
            placeholder="Dupont"
            class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        />
      </div>
      <div class="col-span-2">
        <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="address">Adresse</label>
        <input
            type="text"
            v-model="address"
            id="address"
            placeholder="42 Rue du Moulin, 6800 Libramont"
            class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        />
      </div>
    </div>
  </div>
  <div class="bg-white rounded-3xl p-6 mb-6 border border-slate-200 shadow-xl shadow-slate-200/50">
    <h2 class="text-xs font-black uppercase text-slate-900 mb-3">Informations relatives au travail</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="place-business">Lieu de travail</label>
          <input
              type="text"
              v-model="placeBusiness"
              id="place-business"
              placeholder="2 rue de l'Ancienne gare, 6800 Libramont"
              class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
          />
        </div>
        <div>
          <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="job">Profession</label>
          <input
              type="text"
              v-model="job"
              id="job"
              placeholder="Assistante de direction"
              class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
          />
      </div>
      <div>
        <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="vehicle">Véhicule</label>
        <input
            type="text"
            v-model="vehicle"
            id="vehicle"
            placeholder="Peugeot 208"
            class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        />
      </div>
      <div>
        <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="number-plate">Plaque d'immatriculation</label>
        <input
            type="text"
            v-model="numberPlate"
            id="number-plate"
            placeholder="ABC-123"
            class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        />
      </div>
      </div>
  </div>
</template>