import { ref, watch } from 'vue';

export function useAddresses() {
    const addressHomeRef = ref({
        lat: null,
        lon: null,
        label: ''
    });

    const addressWorkRef = ref({
        lat: null,
        lon: null,
        label: ''
    });

    const updateAddressHome = (newAddress) => {
        if (newAddress && newAddress.label && typeof newAddress.label === 'object') {
            addressHomeRef.value = { ...newAddress.label };
        } else {
            addressHomeRef.value = newAddress;
        }

        if (typeof window !== 'undefined') {
            localStorage.setItem('home_address', JSON.stringify(addressHomeRef.value));
        }
    };

    const initAddressesFromStorage = (userAddresses) => {
        if (typeof window !== 'undefined') {
            // Home address
            const savedHome = localStorage.getItem('home_address');
            if (savedHome) {
                const parsedHome = JSON.parse(savedHome);
                addressHomeRef.value = (parsedHome.label && typeof parsedHome.label === 'object') ? parsedHome.label : parsedHome;
            } else if (userAddresses.address_home) {
                const homeData = typeof userAddresses.address_home === 'string'
                    ? { label: userAddresses.address_home, lat: null, lon: null }
                    : userAddresses.address_home;
                addressHomeRef.value = homeData;
            }

            // Work address
            const savedWork = localStorage.getItem('work_address');
            if (savedWork) {
                const parsedWork = JSON.parse(savedWork);
                addressWorkRef.value = (parsedWork.label && typeof parsedWork.label === 'object') ? parsedWork.label : parsedWork;
            } else if (userAddresses.address_work) {
                const workData = typeof userAddresses.address_work === 'string'
                    ? { label: userAddresses.address_work, lat: null, lon: null }
                    : userAddresses.address_work;
                addressWorkRef.value = workData;
            }
        }
    };

    const setupAddressWatchers = (form) => {
        watch(addressHomeRef, (newVal) => {
            if (typeof window !== 'undefined' && newVal) {
                const clean = (newVal.label && typeof newVal.label === 'object') ? { ...newVal.label } : newVal;
                localStorage.setItem('home_address', JSON.stringify(clean));
                form.addressHome = { ...clean };
            }
        }, { deep: true });

        watch(addressWorkRef, (newVal) => {
            if (typeof window !== 'undefined' && newVal) {
                const clean = (newVal.label && typeof newVal.label === 'object') ? { ...newVal.label } : newVal;
                localStorage.setItem('work_address', JSON.stringify(clean));
                form.addressWork = { ...clean };
            }
        }, { deep: true });
    };

    return {
        addressHomeRef,
        addressWorkRef,
        updateAddressHome,
        initAddressesFromStorage,
        setupAddressWatchers
    };
}

