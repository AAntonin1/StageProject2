<script setup>
import { computed } from 'vue';
import AddressAutocomplete from '@/components/ExpenseReports/AddressAutocomplete.vue';

const props = defineProps({
    user: Object,
    expense_report : Object
});

const firstName = defineModel('first_name');
const lastName = defineModel('last_name');
const address = defineModel('address');
const placeBusiness = defineModel('place_business');
const job = defineModel('job');
const vehicle = defineModel('vehicle');
const numberPlate = defineModel('number_plate');

//Check if any of the required fields are missing
const isMissingData = computed(() => {
    const userFields = [
        props.user.first_name,
        props.user.last_name,
        props.user.address_home,
        props.user.address_work,
        props.expense_report?.job,
        props.expense_report?.vehicle,
        props.expense_report?.number_plate
    ];


    //Check if props fields are empty
    return userFields.some((val) =>
        val == null || (typeof val === 'string' && val.trim() === '')
    );
});
</script>

<template>
    <div v-if="isMissingData" class="bg-white rounded-3xl p-6 mb-6 border border-slate-200 shadow-xl shadow-slate-200/50">
        <div class="flex items-center gap-2 mb-4">
            <h2 class="text-xs font-black uppercase text-slate-900 mb-3">Informations personnelles</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Prénom</label>
                <input type="text" v-model="firstName" placeholder="Jean" class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3" />
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Nom</label>
                <input type="text" v-model="lastName" placeholder="Dupont" class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3" />
            </div>
            <div class="col-span-2">
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Adresse Domicile</label>
                <AddressAutocomplete v-model:address="address" placeholder="Votre adresse de domicile" />
            </div>
        </div>
    </div>

    <div v-if="isMissingData" class="bg-white rounded-3xl p-6 mb-6 border border-slate-200 shadow-xl shadow-slate-200/50">
        <h2 class="text-xs font-black uppercase text-slate-900 mb-3">Informations relatives au travail</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Lieu de travail</label>
                <AddressAutocomplete v-model:address="placeBusiness" placeholder="Votre adresse travail" />
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Profession</label>
                <input type="text" v-model="job" placeholder="Profession..." class="text-slate-600 w-full bg-slate-100 border-none rounded-xl text-sm font-bold p-3" />
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Véhicule</label>
                <input type="text" v-model="vehicle" placeholder="Peugeot 208" class="text-slate-600 w-full bg-slate-100 border-none rounded-xl text-sm font-bold p-3" />
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Plaque d'immatriculation</label>
                <input type="text" v-model="numberPlate" placeholder="ABC-123" class="text-slate-600 w-full bg-slate-100 border-none rounded-xl text-sm font-bold p-3" />
            </div>
        </div>
    </div>
</template>
