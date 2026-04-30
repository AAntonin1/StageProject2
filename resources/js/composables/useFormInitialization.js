export function useFormInitialization() {
    const initFormFromProps = (form, user, expenseReport) => {
        if (typeof window !== 'undefined') {
            const savedForm = localStorage.getItem('form_cache');
            if (savedForm) {
                Object.assign(form, JSON.parse(savedForm));
            }
        }

        if (!form.firstName) form.firstName = user.first_name || '';
        if (!form.lastName) form.lastName = user.last_name || '';
        if (!form.job) form.job = expenseReport.job || '';
        if (!form.vehicle) form.vehicle = expenseReport.vehicle || '';
        if (!form.numberPlate) form.numberPlate = expenseReport.number_plate || '';
        if (!form.placeBusiness) form.placeBusiness = expenseReport.address_work || '';
    };

    const initHomeWorkDistance = (homeWorkDistance) => {
        if (typeof window !== 'undefined') {
            const savedDist = localStorage.getItem('home_work_dist');
            if (savedDist) {
                return parseFloat(savedDist);
            }
        }
        return homeWorkDistance.value;
    };

    return {
        initFormFromProps,
        initHomeWorkDistance
    };
}

