export function useBookingStorage(bookingData, locationType, updateLocationDefault) {
    const loadBookingData = () => {
        const storedData = sessionStorage.getItem('bookingData')
        if (storedData) {
            bookingData.value = JSON.parse(storedData)
            if (bookingData.value.studyLocation) {
                locationType.value = bookingData.value.studyLocation.value
            } else {
                updateLocationDefault()
            }
        } else {
            updateLocationDefault()
        }
    }

    const saveBookingData = () => {
        sessionStorage.setItem('bookingData', JSON.stringify(bookingData.value))
    }

    return { loadBookingData, saveBookingData }
}
