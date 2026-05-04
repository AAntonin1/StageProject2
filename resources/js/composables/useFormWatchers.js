import { watch } from 'vue';
import { route } from 'ziggy-js';
import { useDistanceCalculation } from './useDistanceCalculation';

export function useFormWatchers() {
    const setupFormWatchers = (form, homeWorkDistance, updateDistances) => {
        watch(() => form.homeWorkDistance, (newValue) => {
            if (typeof window !== 'undefined') {
                localStorage.setItem('home_work_dist', newValue);
            }
        });

        watch(
            () => form.data(),
            (formData) => {
                if (typeof window !== 'undefined') {
                    localStorage.setItem('form_cache', JSON.stringify(formData));


                    updateDistances();
                }
            },
            { deep: true }
        );
    };

    const handleFormSubmit = async (form, isOnline, offlineQueue, addressHomeRef, addressWorkRef, homeWorkDistance) => {
        // Ensure addresses are synced to form
        if (addressHomeRef?.value) {
            form.addressHome = { ...addressHomeRef.value };
        }
        if (addressWorkRef?.value) {
            form.addressWork = { ...addressWorkRef.value };
        }
        if (homeWorkDistance?.value) {
            form.homeWorkDistance = homeWorkDistance.value;
        }

        if (!isOnline) {
            offlineQueue.push(form.data());
            localStorage.setItem('offline_queue', JSON.stringify(offlineQueue));
            alert("Mode hors-ligne : Mission enregistrée localement. Elle sera envoyée dès le retour d'internet.");
            form.reset();
            if (typeof window !== 'undefined') localStorage.removeItem('form_cache');
            return;
        }

        const { fetchDistanceFromOSRM } = useDistanceCalculation();
        if (form.addressHome && form.addressWork) {
            form.homeWorkDistance = await fetchDistanceFromOSRM(form.addressHome, form.addressWork, isOnline);
        }

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

    return {
        setupFormWatchers,
        handleFormSubmit
    };
}

