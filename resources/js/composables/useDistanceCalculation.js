export function useDistanceCalculation() {
    const fetchDistanceFromOSRM = async (start, end, isOnline) => {
        if (!isOnline) return 0;
        const coords = `${start.lon},${start.lat};${end.lon},${end.lat}`;
        try {
            const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${coords}?overview=false`);
            const data = await response.json();
            if (data.routes && data.routes.length > 0) {
                return (data.routes[0].distance / 1000);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération de la distance :', error);
        }
        return 0;
    };

    const calculateTotalDistance = (segments, homeWorkDistance) => {
        const hwDist = parseFloat(homeWorkDistance) || 0;

        const calculatedSum = segments.reduce((sum, segment, index) => {
            const dist = parseFloat(segment.distance) || 0;
            let segmentRemboursable = dist;

            if (index === 0) {
                segmentRemboursable = Math.max(0, dist - hwDist);
            } else if (index === segments.length - 1) {
                segmentRemboursable = Math.max(0, dist - hwDist);
            }

            return sum + segmentRemboursable;
        }, 0);

        return parseFloat(calculatedSum.toFixed(2));
    };


    return {
        fetchDistanceFromOSRM,
        calculateTotalDistance
    };
}

