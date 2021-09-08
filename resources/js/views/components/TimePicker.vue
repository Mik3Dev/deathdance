<template>
    <v-time-picker
        :value="value"
        ampm-in-title
        landscape
        format="ampm"
        min="09:00"
        max="18:00"
        :allowed-minutes="allowedMinutes"
        full-width
        @input="emit"
    />
</template>

<script>
export default {
    name: 'TimePicker',
    props: {
        value: {
            type: String,
            default: ''
        }
    },
    methods: {
        allowedMinutes: v => v === 0,
        emit (time) {
            this.$emit('input', time)
        }
    }
}
</script>

<style>

</style>
