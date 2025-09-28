import { ref, computed, watch } from 'vue'

export function useBookingLocation(props, store, bookingData) {
    const locationType = ref(null)

    const locationTypes = computed(() => {
        const all = store.state.configuration.studyLocations || []
        const tutorLocs = props.tutorInfo?.user_study_locations || []
        const tutorIds = tutorLocs.map(l => l.study_location_id)

        return all.filter(l => tutorIds.includes(l.id)).map(l => {
            const tutor = tutorLocs.find(t => t.study_location[0].id === l.id)
            return {
                value: l.id,
                label: l.name?.trim(),
                description: l.description,
                icon: l.icon,
                home_tutor: l.home_tutor,
                home_user: l.home_user,
                transportation_fee: tutor?.transportation_fee,
                max_distance: tutor?.max_distance
            }
        })
    })

    const getLocationTypeDefault = () => locationTypes.value[0]?.value || null

    const updateLocationDefault = () => {
        locationType.value = getLocationTypeDefault()
    }

    watch(locationType, val => {
        const studyLoc = locationTypes.value.find(l => l.value === val)
        bookingData.value.studyLocation = studyLoc || null
        bookingData.value.transportationFee = studyLoc?.transportation_fee || null
        bookingData.value.maxDistance = studyLoc?.max_distance || null
    })

    return {
        locationType,
        locationTypes,
        getLocationTypeDefault,
        updateLocationDefault
    }
}
