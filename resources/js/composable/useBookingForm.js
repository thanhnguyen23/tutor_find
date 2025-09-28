import { ref, computed, watch } from 'vue'
import moment from 'moment'

export function useBookingForm(props, bookingData) {
    const editForm = ref({
        subject: null,
        level: null,
        date: null,
        startTime: null,
        endTime: null
    })

    const isInitializing = ref(false)
    const showValidationErrors = ref(false)

    // === Time helpers ===
    const lessonDurationHours = computed(() => {
        const start = bookingData.value.time?.start
        const end = bookingData.value.time?.end
        if (!start || !end) return 0
        const s = moment(start, 'HH:mm')
        const e = moment(end, 'HH:mm')
        if (!s.isValid() || !e.isValid() || e.isBefore(s)) return 0
        return moment.duration(e.diff(s)).asHours()
    })

    const hourlyPrice = computed(() => {
        const subj = props.tutorInfo?.user_subjects?.find(
            s => s.subject_id === bookingData.value.subject?.id
        )
        const lvl = subj?.user_subject_levels?.find(
            l => l.education_level_id === bookingData.value.level?.id
        )
        return lvl?.price || 0
    })

    const totalPrice = computed(() => hourlyPrice.value * lessonDurationHours.value)

    // === Validation ===
    const isFormValid = computed(() => {
        return (
            editForm.value.subject &&
            editForm.value.level &&
            editForm.value.date &&
            editForm.value.startTime &&
            editForm.value.endTime &&
            lessonDurationHours.value > 0
        )
    })

    // === Watch form changes ===
    watch(
        editForm,
        (val, oldVal) => {
            if (!isInitializing.value) {
                if (val.date !== oldVal?.date) {
                    editForm.value.startTime = ''
                    editForm.value.endTime = ''
                }
                if (val.startTime !== oldVal?.startTime) {
                    editForm.value.endTime = ''
                }
            }
            if (showValidationErrors.value && isFormValid.value) {
                showValidationErrors.value = false
            }
        },
        { deep: true }
    )

    return {
        editForm,
        isInitializing,
        showValidationErrors,
        isFormValid,
        lessonDurationHours,
        hourlyPrice,
        totalPrice
    }
}
