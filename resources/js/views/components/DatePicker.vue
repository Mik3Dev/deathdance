<template>
    <v-menu
        v-model="menu"
        ref="menu"
        min-width="auto"
    >
        <template #activator="{ on, attrs }">
            <v-text-field
                :value="value"
                label="Date"
                readonly
                v-bind="attrs"
                v-on="on"
                @change="$emit(date)"
            />
        </template>

        <v-date-picker
            v-model="date"
            no-title
            scrollable
            :allowed-dates="allowedDates"
        >
            <v-btn
                text
                color="primary"
                @click="menu = false"
            >
                Cancel
            </v-btn>
            <v-btn
                text
                color="primary"
                @click="update"
            >
                OK
            </v-btn>
        </v-date-picker>
    </v-menu>
</template>

<script>
export default {
    name: 'DatePicker',
    data: () => ({
        menu: false,
        date: (new Date()).toISOString().substring(0,10)
    }),
    props: {
        value: {
            type: String,
            default: (new Date()).toISOString().substring(0,10)
        }
    },
    methods: {
        allowedDates (value) {
            const date = new Date(value)
            if (date.getDay() === 5 || date.getDay() === 6) {
                return false
            }
            return true
        },
        update () {
            this.$refs.menu.save(this.date)
            this.$emit('input', this.date)
        }
    }
}
</script>

<style>

</style>
