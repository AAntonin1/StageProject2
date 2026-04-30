export function useSegmentHelpers() {
    const toTimeStamp = (timeStr) => {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 3600 + minutes * 60;
    };

    const formatTime = (timestamp) => {
        const hours = Math.floor(timestamp / 3600) % 24;
        const minutes = Math.floor((timestamp % 3600) / 60);
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    };

    const calculateArrivalTime = (departureTime, distanceKm) => {
        if (!departureTime || !distanceKm) return '';

        const averageSpeed = 70;
        const timeBtw = (distanceKm / averageSpeed) * 3600;
        const departureTimestamp = toTimeStamp(departureTime);
        const arrivalTimestamp = departureTimestamp + timeBtw;

        return formatTime(arrivalTimestamp);
    };

    const createDefaultSegment = (lastSegment = null) => {
        return {
            id: crypto.randomUUID(),
            from_address: lastSegment ? { ...lastSegment.to_address } : { label: '', lat: null, lon: null },
            to_address: '',
            departure_time: lastSegment ? lastSegment.arrival_time : '',
            arrival_time: '',
            reason: '',
            distance: 0,
            typeDoc: 'EAM'
        };
    };

    const createReturnSegment = (lastSegment, firstSegment) => {
        return {
            id: crypto.randomUUID(),
            from_address: { ...lastSegment.to_address },
            to_address: { ...firstSegment.from_address },
            departure_time: lastSegment.arrival_time || '',
            arrival_time: '',
            reason: 'Retour au siège / domicile',
            distance: 0
        };
    };

    const hasReturnTrip = (segments) => {
        return segments.some(s => s.reason === 'Retour au siège / domicile');
    };

    const getReturnSegmentIndex = (segments) => {
        return segments.findIndex(s => s.reason === 'Retour au siège / domicile');
    };

    return {
        toTimeStamp,
        formatTime,
        calculateArrivalTime,
        createDefaultSegment,
        createReturnSegment,
        hasReturnTrip,
        getReturnSegmentIndex
    };
}

