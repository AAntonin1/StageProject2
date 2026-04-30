import { ref } from 'vue';
import { route } from 'ziggy-js';
import axios from 'axios';

export function useOfflineQueue() {
    const isOnline = ref(navigator.onLine);
    const offlineQueue = ref([]);
    let checkConnInterval = null;

    const handleOnline = async () => {
        try {
            await fetch('/favicon.ico', {
                method: 'HEAD',
                mode: 'no-cors',
                cache: 'no-store'
            });

            if (!isOnline.value) {
                console.log('Connecté !');
                isOnline.value = true;
                processQueue();
            }
        } catch (error) {
            if (isOnline.value) {
                console.log('Hors-ligne détecté (réel)');
                isOnline.value = false;
            }
        }
    };

    const processQueue = async () => {
        if (offlineQueue.value.length === 0) return;

        const queue = [...offlineQueue.value];
        for (const data of queue) {
            try {
                await axios.post(route('expenseReport.store'), data, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                offlineQueue.value = offlineQueue.value.filter(item => item !== data);
                localStorage.setItem('offline_queue', JSON.stringify(offlineQueue.value));
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    console.error("Erreur de validation Laravel :", error.response.data.errors);
                } else {
                    console.error("Échec de la synchro :", error);
                }
                break;
            }
        }
    };

    const initOfflineHandling = () => {
        if (typeof window !== 'undefined') {
            const savedQueue = localStorage.getItem('offline_queue');
            if (savedQueue) offlineQueue.value = JSON.parse(savedQueue);

            checkConnInterval = setInterval(handleOnline, 5000);

            window.addEventListener('online', handleOnline);
            window.addEventListener('offline', () => isOnline.value = false);

            if (isOnline.value) processQueue();
        }
    };

    const cleanupOfflineHandling = () => {
        clearInterval(checkConnInterval);
        if (typeof window !== 'undefined') {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', () => isOnline.value = false);
        }
    };

    return {
        isOnline,
        offlineQueue,
        processQueue,
        initOfflineHandling,
        cleanupOfflineHandling
    };
}

