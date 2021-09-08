<template>
    <div class="login-wrapper">
        <v-card min-width="600">
            <v-card-title>
                <v-subheader class="text-uppercase pl-0">
                    Login
                </v-subheader>
            </v-card-title>
            <v-container>
                <v-form ref="form">
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field
                                v-model="user.email"
                                label="Email"
                                :rules="emailRules"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="user.password"
                                label="Password"
                                :type="showPassword ? 'text' : 'password'"
                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                @click:append="showPassword = !showPassword"
                            />
                        </v-col>
                    </v-row>
                </v-form>
            </v-container>
            <v-card-actions>
                <v-btn text to="/">
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn text color="primary" @click="submit">
                    Login
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import isEmail from 'validator/lib/isEmail'

export default {
    name: 'LoginPage',
    data: () => ({
        user: {
            email: '',
            password: '',
        },
        emailRules: [
            v => isEmail(v) || 'This field must be a valid email.'
        ],
        showPassword: false,
    }),
    methods: {
        async submit () {
            if (this.$refs.form.validate()) {
                await this.$store.dispatch('login', {
                    email: this.user.email,
                    password: this.user.password,
                })
                this.$router.push('/')
            }
        }
    }
}
</script>

<style scoped>
    .login-wrapper {
        height: 100vh;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
