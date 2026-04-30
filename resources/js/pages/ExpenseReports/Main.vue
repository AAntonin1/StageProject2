<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, defineAsyncComponent, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Composables
import { useOfflineQueue } from '@/composables/useOfflineQueue';
import { useAddresses } from '@/composables/useAddresses';
import { useSegmentHelpers } from '@/composables/useSegmentHelpers';
import { useDistanceCalculation } from '@/composables/useDistanceCalculation';
import { useFormInitialization } from '@/composables/useFormInitialization';
import { useFormWatchers } from '@/composables/useFormWatchers';

// Async Components
const AddressAutocomplete = defineAsyncComponent(() => import('@/components/ExpenseReports/AddressAutocomplete.vue'));
const Recap = defineAsyncComponent(() => import('@/components/ExpenseReports/Recap.vue'));
const Menu = defineAsyncComponent(() => import('@/components/ExpenseReports/Menu.vue'));

// Sync Components
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
import { route } from 'ziggy-js';

// Initialize composables
const { isOnline, offlineQueue, processQueue, initOfflineHandling, cleanupOfflineHandling } = useOfflineQueue();
const { addressHomeRef, addressWorkRef, updateAddressHome, initAddressesFromStorage, setupAddressWatchers } = useAddresses();
const { createDefaultSegment, createReturnSegment, toTimeStamp, getReturnSegmentIndex } = useSegmentHelpers();
const { fetchDistanceFromOSRM, calculateTotalDistance } = useDistanceCalculation();
const { initFormFromProps, initHomeWorkDistance } = useFormInitialization();
const { setupFormWatchers, handleFormSubmit } = useFormWatchers();

const page = usePage();

const props = defineProps({
    user: Object,
    segments: Array,
    expense_report: Object,
});

const homeWorkDistance = ref(0);
let debounceTimeout = null;

// Form initialization
const form = useForm({
    label: '',
    date: new Date().toISOString().split('T')[0],
    roundTrip: false,
    km_rate: 0.4449,
    firstName: '',
    lastName: '',
    addressHome: { lat: null, lon: null, label: '' },
    homeWorkDistance: homeWorkDistance.value,
    numAccount: '',
    placeBusiness: '',
    job: '',
    vehicle: '',
    numberPlate: '',
    addressWork: { lat: null, lon: null, label: '' },
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
            isManualUpdateKM : false,
            typeDoc: 'EAM'
        }
    ],
});

// Provide addresses to child components
provide('dataHomeAddress', {
    addressHomeRef,
    updateAddressHome
});

provide('dataWorkAddress', {
    addressWorkRef
});

// Segment management
const addStep = () => {
    const lastSegment = form.segments[form.segments.length - 1];
    form.segments.push(createDefaultSegment(lastSegment));
};

const removeStep = async (index) => {
    if (form.segments.length > 1) {
        form.segments.splice(index, 1);
        updateReturnSegment();
        await updateDistances();
    }
};

// Distance calculations
const updateDistances = async () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(async () => {
        const promises = form.segments.map(async (segment) => {
            if (segment.isManualUpdateKM) return;

            const start = segment.from_address;
            const end = segment.to_address;

            if (start?.lat && start?.lon && end?.lat && end?.lon) {
                const dist = await fetchDistanceFromOSRM(start, end, isOnline.value);
                segment.distance = dist.toFixed(2);
                if (!segment.manualArrivalTime) calcBtwToAddress(form.segments.indexOf(segment));
            } else {
                segment.distance = 0;
            }
        });

        await Promise.all(promises);
    }, 500);
};

// Return trip management
const toggleReturnTrip = () => {
    const returnIndex = getReturnSegmentIndex(form.segments);

    if (returnIndex !== -1) {
        form.segments.splice(returnIndex, 1);
    } else {
        const firstSegment = form.segments[0];
        const lastSegment = form.segments[form.segments.length - 1];

        if (firstSegment?.from_address && lastSegment?.to_address) {
            form.segments.push(createReturnSegment(lastSegment, firstSegment));
            updateDistances();
        }
    }
};

const hasReturnTrip = computed(() => {
    return getReturnSegmentIndex(form.segments) !== -1;
});

// Computed properties
const totalDistance = computed(() => {
    return calculateTotalDistance(form.segments, form.homeWorkDistance);
});

const updateReturnSegment = () => {
    const firstSegment = form.segments[0];
    const lastSegment = form.segments[form.segments.length - 1];
    const returnIndex = getReturnSegmentIndex(form.segments);

    if (returnIndex !== -1 && firstSegment && lastSegment) {
        const returnSegment = form.segments[returnIndex];
        const previousToReturn = form.segments[returnIndex - 1];

        if (previousToReturn) {
            returnSegment.from_address = { ...previousToReturn.to_address };
            returnSegment.to_address = { ...firstSegment.from_address };
            returnSegment.departure_time = previousToReturn.arrival_time || '';
            updateDistances();
        }
    }
};

// Address management
const addAddressButtonToSegment = (index, field, type) => {
    if (type === 'work') {
        if (!addressWorkRef.value?.lat) {
            alert("Veuillez configurer votre adresse travail dans les paramètres (bouton en haut à droite).");
            return;
        }
        form.segments[index][field] = {
            label: addressWorkRef.value.label,
            lat: addressWorkRef.value.lat,
            lon: addressWorkRef.value.lon
        };
    } else if (type === 'home') {
        if (addressHomeRef.value?.lat) {
            form.segments[index][field] = {
                label: addressHomeRef.value.label,
                lat: addressHomeRef.value.lat,
                lon: addressHomeRef.value.lon
            };
        } else {
            alert("Veuillez configurer votre adresse domicile dans les paramètres (bouton en haut à droite).");
        }
    }
    form.segments[index].isManualUpdateKM = false;
    updateDistances();
};

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

// Time calculations
const calcBtwToAddress = (index) => {
    const segment = form.segments[index];
    if (!segment) return;

    if (segment.departure_time && segment.distance) {
        const averageSpeed = 70;
        const timeBtw = (segment.distance / averageSpeed) * 3600;
        segment.timeBtw = timeBtw;

        const departureTimestamp = toTimeStamp(segment.departure_time);
        const arrivalTimestamp = departureTimestamp + timeBtw;
        const arrivalHours = Math.floor(arrivalTimestamp / 3600) % 24;
        const arrivalMinutes = Math.floor((arrivalTimestamp % 3600) / 60);
        segment.arrival_time = `${String(arrivalHours).padStart(2, '0')}:${String(arrivalMinutes).padStart(2, '0')}`;
    }
};

// Form submission
const submit = () => {
    handleFormSubmit(form, isOnline.value, offlineQueue.value);
};

// Lifecycle
onMounted(() => {
    initOfflineHandling();
    initAddressesFromStorage(props.user);
    homeWorkDistance.value = initHomeWorkDistance(homeWorkDistance.value);
    form.homeWorkDistance = homeWorkDistance.value;
    initFormFromProps(form, props.user, props.expense_report);
    setupFormWatchers(form, homeWorkDistance, updateDistances);
    setupAddressWatchers(form);
});

onUnmounted(() => {
    cleanupOfflineHandling();
});


</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 font-sans text-slate-900">
        <div class="max-w-2xl mx-auto">
            <Header :totalDistance="totalDistance"/>

            <!-- Alerts online/offline -->
            <div v-if="!isOnline" class="mb-4 bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded-xl flex items-center gap-3 animate-pulse">
                <span class="font-bold">MODE HORS-LIGNE</span>
                <span class="text-xs">Les missions seront envoyées automatiquement dès que vous aurez du réseau.</span>
            </div>

            <div v-if="offlineQueue.length > 0 && isOnline" class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-xl flex items-center gap-2">
                <span class="font-bold">Synchronisation en cours...</span>
                <span class="text-xs ml-2">{{ offlineQueue.length }} mission(s) restante(s).</span>
            </div>

            <div v-if="$page.props.auth.user.roles.includes('admin')" class="flex gap-2 mb-6">
                <Link
                    :href="route('users.index')"
                    class="px-4 py-2 rounded-xl font-bold shadow bg-white text-slate-600 hover:bg-slate-100 transition-all inline-block">
                    Admin
                </Link>
            </div>

            <div>
                <PersonalInformation
                    v-model:first_name="form.firstName"
                    v-model:last_name="form.lastName"
                    v-model:address="addressHomeRef"
                    v-model:place_business="addressWorkRef"
                    v-model:job="form.job"
                    v-model:vehicle="form.vehicle"
                    v-model:number_plate="form.numberPlate"
                    :user="props.user"
                    :expense_report="props.expense_report"
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
                                        @change="segment.isManualUpdateKM = false"
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
                                        @change="calcBtwToAddress(index); segment.isManualUpdateKM = false"
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

                            <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1">Motif de déplacement</label>
                            <div class="pt-2 flex items-center gap-4">
                                <ReasonDeplacement v-model:reason="segment.reason"/>

                                <div class="ml-auto flex items-center gap-2">
                                    <label
                                        for="distance"
                                        class="text-[10px] font-black uppercase text-slate-600 block mb-1">
                                        Distance (km)
                                    </label>
                                    <input
                                        @click="segment.isManualUpdateKM = true"
                                        id="distance"
                                        type="text" v-model="segment.distance"
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

                    <Recap
                        :realSumTotal="form.segments.reduce((sum, s) => sum + (parseFloat(s.distance) || 0), 0).toFixed(2)"
                        :homeWorkDistance="form.homeWorkDistance"
                        :totalDistance="totalDistance.toFixed(2)"
                        :form="form"
                    />

                    <button :disabled="form.processing"
                            type="submit"
                            class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-slate-300 hover:bg-black transition-all active:scale-[0.98] disabled:opacity-50">
                        <span v-if="!form.processing" class="text-lg">
                            {{ isOnline ? 'Enregistrer la mission' : 'Mettre en file d’attente' }}
                        </span>
                        <span v-else class="text-lg">Traitement...</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <p>{{ form }}</p>

    <Menu
        v-model:km_rate="form.km_rate"
        v-model:first_name="form.firstName"
        v-model:last_name="form.lastName"
        v-model:job="form.job"
        v-model:vehicle="form.vehicle"
        v-model:number_plate="form.numberPlate"
    />
</template>
