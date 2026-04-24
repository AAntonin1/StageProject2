<script setup>
import {useForm} from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, defineAsyncComponent, provide, toRaw } from 'vue';
import { route } from 'ziggy-js';

const AddressAutocomplete = defineAsyncComponent(() => import('@/components/ExpenseReports/AddressAutocomplete.vue'));
const Recap = defineAsyncComponent(() => import('@/components/ExpenseReports/Recap.vue'));
const Menu = defineAsyncComponent(() => import('@/components/ExpenseReports/Menu.vue'));
import ButtonAddStep from "@/components/ExpenseReports/ButtonAddStep.vue";
import ButtonToggleReturnTrip from "@/components/ExpenseReports/ButtonToggleReturnTrip.vue";
import Header from "@/components/ExpenseReports/Header.vue";
import PersonalInformation from "@/components/ExpenseReports/PersonalInformation.vue";
import HomeWorkDistance from "@/components/ExpenseReports/HomeWorkDistance.vue";
import ButtonSwapAddress from "@/components/ExpenseReports/ButtonSwapAddress.vue";
import ButtonHomeAddress from "@/components/ExpenseReports/ButtonHomeAddress.vue";
import ButtonWorkAddress from "@/components/ExpenseReports/ButtonWorkAddress.vue";
import SelectTypeDoc from "@/components/ExpenseReports/SelectTypeDoc.vue";
import ReasonDeplacement from "@/components/ExpenseReports/ReasonDeplacement.vue";

// Fetch the home address from localStorage or initialize it to an empty object if not set
const addressHomeRef = ref({
    lat: 10,
    lon: 10,
    label: '42 rue de la paix, Paris'
});

// Fetch the work address from localStorage or initialize it to an empty object if not set
const addressWorkRef = ref({
    lat: 10,
    lon: 10,
    label: '42 rue de la paix, Paris'
});

const updateAddressHome = (newAddress) => {
    addressHomeRef.value = newAddress;
    if (typeof window !== 'undefined') {
        localStorage.setItem('home_address', JSON.stringify(newAddress));
    }
};

// Put home and work address data and update function in a provide so it can be used in child components
provide('dataHomeAddress', {
    addressHomeRef,
    updateAddressHome
});

provide('dataWorkAddress', {
    addressWorkRef
});

// Fetch the home-work distance from localStorage or initialize it to 0 if not set
const homeWorkDistance = ref(0);

const form = useForm({
    label: '',
    date: new Date().toISOString().split('T')[0],
    roundTrip: false,
    km_rate: 0.4449,
    firstName: '',
    lastName: '',
    addressFormular: '',
    addresshome : addressHomeRef.value ,
    homeWorkDistance: homeWorkDistance.value,
    numAccount : '',
    placeBusiness : '',
    job : '',
    vehicle : '',
    numberPlate : '',
    addressWork : addressWorkRef.value,
    segments: [
        {
            id: crypto.randomUUID(),
            from_address: '',
            to_address: '',
            departure_time: '08:00',
            arrival_time: '',
            reason: '',
            distance: 0,
            timeBtw: 0,
            manualArrivalTime: false,
            typeDoc: 'EAM'
        }
    ],
});

onMounted(() => {
    if (typeof window !== 'undefined') {
        const savedHome = localStorage.getItem('home_address');

        if (savedHome) addressHomeRef.value = JSON.parse(savedHome);

        const savedWork = localStorage.getItem('work_address');

        if (savedWork) addressWorkRef.value = JSON.parse(savedWork);

        const savedDist = localStorage.getItem('home_work_dist');

        if (savedDist) {
            homeWorkDistance.value = parseFloat(savedDist);
            form.homeWorkDistance = parseFloat(savedDist);
        }

        const savedForm = localStorage.getItem('form_cache');
        if (savedForm) {
            Object.assign(form, JSON.parse(savedForm));
        }
    }
});

let debounceTimeout = null;

// Add step
const addStep = () => {
    const lastSegment = form.segments[form.segments.length - 1];
    form.segments.push({
        id: crypto.randomUUID(),
        from_address: lastSegment ? lastSegment.to_address : '',
        to_address: '',
        departure_time: lastSegment ? lastSegment.arrival_time : '',
        arrival_time: '',
        reason: '',
        distance: 0,
        typeDoc: 'EAM'

    });
};

// Delete step
const removeStep = async (index) => {
    if (form.segments.length > 1) {
        form.segments.splice(index, 1);
        updateReturnSegment();
        await updateDistances();
    }
};

// Calculate distance between two points using OSRM API
const fetchDistanceFromOSRM = async (start, end) => {
    const coords = `${start.lon},${start.lat};${end.lon},${end.lat}`;
    try {
        const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${coords}?overview=false`);
        const data = await response.json();
        if (data.routes && data.routes.length > 0) {
            return (data.routes[0].distance / 1000); // Convert to kilometers
        }

    } catch (error) {
        console.error('Erreur lors de la récupération de la distance :', error);
    }
    return 0;
};

// Update distances whenever waypoints change
const updateDistances = async () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(async () => {
        const promises = form.segments.map(async (segment) => {
            const start = segment.from_address;
            const end = segment.to_address;

            if (start?.lat && start?.lon && end?.lat && end?.lon) {
                segment.distance = (await fetchDistanceFromOSRM(start, end)).toFixed(2); // Update distance to next waypoint
                if (!segment.manualArrivalTime)calcBtwToAddress(form.segments.indexOf(segment)); // Update arrival time based on new distance
            } else {
                segment.distance = 0;
            }
        });

        await Promise.all(promises);
    }, 500); // Wait 500ms after the last change before updating distances
};

// Toggle return trip
const toggleReturnTrip = () => {
    const returnIndex = form.segments.findIndex(s => s.reason === 'Retour au siège / domicile');

    if (returnIndex !== -1) {
        // if the return trip already exists, remove it
        form.segments.splice(returnIndex, 1);
    } else {
        const firstSegment = form.segments[0];
        const lastSegment = form.segments[form.segments.length - 1];

        if (firstSegment?.from_address && lastSegment?.to_address) {
            form.segments.push({
                id: crypto.randomUUID(),
                from_address: {...lastSegment.to_address},
                to_address: {...firstSegment.from_address},
                departure_time: lastSegment.arrival_time || '',
                arrival_time: '',
                reason: 'Retour au siège / domicile',
                distance: 0
            });
            updateDistances();
        }
    }
};

// Calculate if a return trip segment exists
const hasReturnTrip = computed(() => {
    return form.segments.some(s => s.reason === 'Retour au siège / domicile');
});

// Calculate total distance
const totalDistance = computed(() => {
    const realSum = form.segments.reduce((sum, segment) => sum + (parseFloat(segment.distance) || 0), 0);
    const hwDist = parseFloat(form.homeWorkDistance) || 0;
    const calcul = realSum - (hwDist * 2);
    return calcul > 0 ? calcul.toFixed(2) : "0.00";
});


const updateReturnSegment = () => {
    const firstSegment = form.segments[0];
    const lastSegment = form.segments[form.segments.length - 1];

    // Look for the return trip segment
    const returnIndex = form.segments.findIndex(s => s.reason === 'Retour au siège / domicile');

    if (returnIndex !== -1 && firstSegment && lastSegment) {
        const returnSegment = form.segments[returnIndex];

        // if the return segment exists, we update its addresses and departure time based on the first and last segments
        const previousToReturn = form.segments[returnIndex - 1];

        if (previousToReturn) {
            returnSegment.from_address = {...previousToReturn.to_address};
            returnSegment.to_address = {...firstSegment.from_address};
            returnSegment.departure_time = previousToReturn.arrival_time || '';

            updateDistances();
        }
    }
};

// Add work address to a segment
const addAddressButtonToSegment = (index, field, type) => {
    if(type === 'work') {
        if(!form.addressWork?.lat) {
            alert("Veuillez configurer votre adresse travail dans les paramètres (bouton en haut à droite).");
            return;
        }
        form.segments[index][field] = {
            label: form.addressWork.label,
            lat: form.addressWork.lat,
            lon: form.addressWork.lon
        }} else if(type === 'home') {
        if (addressHomeRef.value?.lat) {
            form.segments[index][field] = {
                label: addressHomeRef.value.label || addressHomeRef.value.display_name,
                lat: addressHomeRef.value.lat,
                lon: addressHomeRef.value.lon
            };
        } else {
            alert("Veuillez configurer votre adresse domicile dans les paramètres (bouton en haut à droite).");
        }
    }
};

// Swap addresses and times of a segment
const swapAddresses = (index) => {
    const segment = form.segments[index];
    const tempAddress = segment.from_address;
    segment.from_address = segment.to_address;
    segment.to_address = tempAddress;

    const tempTime = segment.departure_time;
    segment.departure_time = segment.arrival_time;
    segment.arrival_time = tempTime;

    updateDistances();
};

// Convert time string "HH:MM" to timestamp in seconds
const toTimeStamp = (timeStr) => {
    const [hours, minutes] = timeStr.split(':').map(Number);
    return hours * 3600 + minutes * 60;
};

// Calculate time between addresses based on distance and average speed, then update arrival time
const calcBtwToAddress = (index) => {
    //If someone change the arrival_time return without calculating
    const segment = form.segments[index];
    if (segment.departure_time && segment.distance) {
        const averageSpeed = 70; // km/h
        const timeBtw = (segment.distance / averageSpeed) * 3600; // time in seconds
        segment.timeBtw = timeBtw;

        // Update arrival_time without arrival_time
        if (true) {
            const departureTimestamp = toTimeStamp(segment.departure_time);
            const arrivalTimestamp = departureTimestamp + timeBtw;
            const arrivalHours = Math.floor(arrivalTimestamp / 3600) % 24; // Modulo 24 to wrap around if it goes past midnight
            const arrivalMinutes = Math.floor((arrivalTimestamp % 3600) / 60); // Calculate remaining minutes
            segment.arrival_time = `${String(arrivalHours).padStart(2, '0')}:${String(arrivalMinutes).padStart(2, '0')}`;
        }
    }

};

const submit = () => {
    form.post(route('expenseReport.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            if (typeof window !== 'undefined') localStorage.removeItem('form_cache');
        },
        onError: (errors) => {
            console.error("Erreur lors de l'envoi :", errors);
        },
    });
};

// Save homeWorkDistance in localStorage whenever it changes
watch(() => form.homeWorkDistance, (newValue) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('home_work_dist', newValue);
    }
});

// if the address of any waypoint changes, update the distances
watch(
    () => form.data(),
    (formData) => {
        if (typeof window !== 'undefined') {
            //If someone change to_arrival time change manualArrivalTime to true
            localStorage.setItem('form_cache', JSON.stringify(formData));
            console.log("form updated : ", formData);

            formData.segments.forEach((segment, index) => {
                if(segment.arrival_time && !segment.manualArrivalTime) {
                    form.segments[index].manualArrivalTime = true;
                }
            });

            updateDistances();
        }
    },
    {deep: true},
);

// Sync the home address in the form with the addressHomeRef,
watch(addressHomeRef, (newVal) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('home_address', JSON.stringify(newVal));
        form.addresshome = newVal;
    }
}, { deep: true });

watch(addressWorkRef, (newVal) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('work_address', JSON.stringify(newVal));
        form.addressWork = newVal;
    }
}, { deep: true });

</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 font-sans text-slate-900">
        <div class="max-w-2xl mx-auto">
            <Header :totalDistance="totalDistance"/>

            <PersonalInformation
                v-model:first_name="form.firstName"
                v-model:last_name="form.lastName"
                v-model:address="form.addressFormular"
                v-model:place-business="form.placeBusiness"
                v-model:job="form.job"
                v-model:vehicle="form.vehicle"
                v-model:number-plate="form.numberPlate"
            />

            <HomeWorkDistance v-model:homeWorkDistance="form.homeWorkDistance"  />


            <form @submit.prevent="submit" class="space-y-6">
                <div v-for="(segment, index) in form.segments" :key="segment.id"
                     class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 relative">

                    <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-3">
                        <h2 class="text-xs font-black uppercase text-blue-700 tracking-wider">
                            Trajet #{{ index + 1 }}
                        </h2>
                        <button v-if="form.segments.length > 1" @click="removeStep(index)" type="button"
                                class="text-slate-400 hover:text-red-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="flex gap-3 items-end">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-white mb-3 border-2 shadow-md border-slate-900"></div>
                                <div class="w-0.5 h-full border-l-2 border-slate-200 border-dashed"></div>
                            </div>
                            <div class="flex-1 flex gap-2 items-end">

                                <AddressAutocomplete
                                    v-model:address="segment.from_address"
                                    placeholder="Lieu de départ"
                                    labelName="Lieu de départ"
                                    class="flex-1"/>

                                <ButtonHomeAddress
                                    @add-address-button-to-segment="addAddressButtonToSegment(index, 'from_address', 'home')"/>

                                <ButtonWorkAddress
                                    @add-address-button-to-segment="addAddressButtonToSegment(index, 'from_address', 'work')"/>

                                <div class="flex flex-col gap-1">
                                    <label :for="'departure_time_' + index" class="text-[10px] font-black uppercase text-slate-600">Départ</label>
                                    <input type="time" v-model="segment.departure_time" lang="fr-FR" :id="'departure_time_' + index"
                                           class="p-3 w-24 bg-slate-100 border-none rounded-xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-slate-900"/>
                                </div>
                            </div>
                        </div>

                        <ButtonSwapAddress @swap-address="swapAddresses(index)"/>

                        <div class="flex gap-3 items-end">
                            <div class="w-3 h-3 rounded-full bg-slate-900 mb-3 shadow-md shadow-blue-900/20"></div>
                            <div class="flex-1 flex gap-2 items-end">

                                <AddressAutocomplete
                                    @change="calcBtwToAddress(index)"
                                    v-model:address="segment.to_address"
                                    placeholder="Lieu d'arrivée"
                                    labelName="Lieu d'arrivée"
                                    class="flex-1"/>

                                <ButtonHomeAddress
                                    @add-address-button-to-segment="addAddressButtonToSegment(index, 'to_address', 'home')"/>

                                <ButtonWorkAddress
                                    @add-address-button-to-segment="addAddressButtonToSegment(index, 'to_address', 'work')"/>


                                <div class="flex flex-col gap-1">

                                    <label :for="'arrival_time_' + index" class="text-[10px] font-black uppercase text-slate-600">Arrivée</label>
                                    <input type="time" v-model="segment.arrival_time" lang="fr-FR" :id="'arrival_time_' + index"
                                           class="p-3 w-24 bg-slate-100 border-none rounded-xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 "/>
                                </div>
                            </div>
                        </div>

                        <label for="typeDoc" class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Motif de déplacement</label>
                        <div class="pt-2 flex items-center gap-4">
                            <ReasonDeplacement v-model:reason="segment.reason"/>

                            <div class="ml-auto flex items-center gap-2">
                                <label for="distance" class="text-[10px] font-black uppercase text-slate-600 block mb-1">
                                    Distance (km)
                                </label>
                                <input type="text" v-model="segment.distance" id="distance"
                                       class="text-right w-20 p-3 text-sm font-bold bg-blue-100 text-blue-800 px-3 rounded-lg border border-blue-200"/>
                            </div>
                        </div>
                        <SelectTypeDoc v-model:typeDoc="segment.typeDoc" />
                    </div>
                </div>

                <ButtonAddStep @add-step="addStep"/>


                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                    <ButtonToggleReturnTrip :hasReturnTrip="hasReturnTrip" @toggle-return-trip="toggleReturnTrip"/>
                </div>

                <Recap :realSumTotal="form.segments.reduce((sum, s) => sum + (parseFloat(s.distance) || 0), 0).toFixed(2)"
                       :homeWorkDistance="form.homeWorkDistance"
                       :totalDistance="totalDistance"
                       :form="form"/>

                <button :disabled="form.processing"
                        @click="submit"
                        class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-slate-300 hover:bg-black transition-all active:scale-[0.98] disabled:opacity-50">
                    <span v-if="!form.processing" class="text-lg">Enregistrer la mission</span>
                    <span v-else class="text-lg">Traitement...</span>
                </button>
            </form>
        </div>
    </div>

    <Menu
        v-model:km_rate="form.km_rate"
    />
</template>
