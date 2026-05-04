<script setup>
import { router, usePage, Link } from '@inertiajs/vue3';
import { ref, defineAsyncComponent, watch, onMounted, onUnmounted } from 'vue';
import { route } from 'ziggy-js';
import Header from "@/components/ExpenseReports/Header.vue";
import { useDistanceCalculation } from '@/composables/useDistanceCalculation.js';

const AddressAutocomplete = defineAsyncComponent(() => import('@/components/ExpenseReports/AddressAutocomplete.vue'));
const { fetchDistanceFromOSRM, calculateTotalDistance } = useDistanceCalculation();

const page = usePage();



const props = defineProps({
    user: Object,
    expenseReport : Object
});

const firstName = ref(props.user?.first_name || '');
const lastName = ref(props.user?.last_name || '');
const job = ref(props.expenseReport?.job || '');
const vehicle = ref(props.expenseReport?.vehicle || '');
const numberPlate = ref(props.expenseReport?.number_plate || '');
const km_rate = ref(props.expenseReport?.km_rate || 0.4449);
const homeWorkDistance = ref(props.user?.home_work_distance || 0);

const addressHomeRef = ref({
    label: props.user?.address_home.label || '',
    lat: props.user?.address_home.lat || null,
    lon: props.user?.address_home.lon || null,
});

const addressWorkRef = ref({
    label: props.user?.address_work.label || '',
    lat: props.user?.address_work.lat || null,
    lon: props.user?.address_work.lon|| null,
});

let unsubHomeRef = null;
let unsubWorkRef = null;
let debounceTimeout = null;

const logout = () => {
    router.post(route('user.logout'));
    localStorage.clear();
};

const submit = () => {
    router.put(route('user.profile.update'), {
        first_name: firstName.value,
        last_name: lastName.value,
        job: job.value,
        vehicle: vehicle.value,
        number_plate: numberPlate.value,
        km_rate: km_rate.value,
        address_home: addressHomeRef.value,
        address_work: addressWorkRef.value,
        home_work_distance: homeWorkDistance.value.toFixed(2),
    });
};

// Calculate home-work distance
const calculateHomeWorkDistance = async () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(async () => {
        if (addressHomeRef.value?.lat && addressHomeRef.value?.lon && addressWorkRef.value?.lat && addressWorkRef.value?.lon) {
            console.log("Calculating distance between", addressHomeRef.value, "and", addressWorkRef.value);
            const distance = await fetchDistanceFromOSRM(addressHomeRef.value, addressWorkRef.value, true);
            homeWorkDistance.value = distance;
            console.log("Distance domicile-travail calculée :",  homeWorkDistance.value);
        }
    }, 500); // Debounce 500ms

};

onMounted(() => {
    unsubHomeRef = watch(addressHomeRef, calculateHomeWorkDistance, { deep: true });
    unsubWorkRef = watch(addressWorkRef, calculateHomeWorkDistance, { deep: true });
})

onUnmounted(() => {
    if (unsubHomeRef) unsubHomeRef();
    if (unsubWorkRef) unsubWorkRef();
})

</script>
<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 font-sans text-slate-900">
        <div class="max-w-2xl mx-auto">

            <!-- Header -->
            <Header :totalDistance="0" />

            <!-- Alerts messages -->
            <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl">
                {{ page.props.flash.error }}
            </div>

            <!-- Button -->
            <div class="flex gap-2 mb-6">
                <Link :href="route('expenseReport.form')" class="px-4 py-2 rounded-xl font-bold shadow bg-white text-slate-600 text-xs">
                    ← Retour Missions
                </Link>
                <div class="px-4 py-2 rounded-xl font-bold shadow bg-slate-900 text-white text-xs">
                    Profil Utilisateur
                </div>
            </div>

            <!-- Formular -->
            <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-xs font-black uppercase text-slate-700 tracking-wider">
                        Mes Informations Personnelles
                    </h2>
                </div>

                <!-- Identity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Prénom</label>
                        <input v-model="firstName" type="text" placeholder="Prénom" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Nom</label>
                        <input v-model="lastName" type="text" placeholder="Nom" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Address home/work -->
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase text-slate-600 ml-1 mb-4">Localisations</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Domicile</label>
                            <AddressAutocomplete v-model:address="addressHomeRef" placeholder="Adresse privée" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Lieu de travail principal</label>
                            <AddressAutocomplete v-model:address="addressWorkRef" placeholder="Adresse du bureau" />
                        </div>
                    </div>
                </div>

                <!-- Professional infos -->
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase text-slate-600 ml-1 mb-4">Professionnel & Véhicule</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Métier</label>
                            <input v-model="job" type="text" placeholder="Ex: Développeur" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Modèle Véhicule</label>
                            <input v-model="vehicle" type="text" placeholder="Ex: Tesla Model 3" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Plaque</label>
                            <input v-model="numberPlate" type="text" placeholder="Ex: 1-ABC-123" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                </div>

                <!-- Admin section-->
                <div v-if="page.props.auth?.user?.roles?.includes('admin')" class="mb-6 pb-6 border-b border-slate-100">
                    <h3 class="text-[10px] font-black uppercase text-slate-600 ml-1 mb-4">Configuration Admin</h3>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Tarif Kilométrique (€/km)</label>
                        <input v-model="km_rate" type="number" step="0.001" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Button action -->
                <div class="flex gap-3">
                    <button @click="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all">
                        Enregistrer les modifications
                    </button>
                    <button @click="logout" class="flex-1 bg-white border border-slate-200 text-red-500 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-red-50 transition-all">
                        Déconnexion
                    </button>
                </div>
            </section>
        </div>
    </div>
</template>
