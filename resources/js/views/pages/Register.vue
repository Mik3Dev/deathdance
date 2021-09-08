<template>
    <div class="register-wrapper">
        <v-card min-width="600">
            <v-card-title>
                <v-subheader class="text-uppercase pl-0">
                    User Registration
                </v-subheader>
            </v-card-title>
            <v-container>
                <v-form ref="form">
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field
                                v-model="user.name"
                                label="Username"
                                :rules="usernameRules"
                            />
                        </v-col>
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
                                counter
                                :rules="passwordRules"
                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                @click:append="showPassword = !showPassword"
                            />
                        </v-col>
                        <v-col>
                            <v-text-field
                                v-model="user.password_confirmation"
                                label="Confirm Password"
                                type="password"
                                counter
                                :rules="confirmPasswordRules"
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
                    Register
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import isEmail from 'validator/lib/isEmail'
import isStrongPassword from 'validator/lib/isStrongPassword'
import isLength from 'validator/lib/isLength'

export default {
    name: 'RegisterPage',
    data: () => ({
        user: {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        usernameRules: [
            v => !!v || 'This field is required.',
        ],
        emailRules: [
            v => isEmail(v) || 'This field must be a valid email.'
        ],
        passwordRules: [
            v => isLength(v, {
                min: 6
            }) || 'This fields must contains at least 6 characters.',
            v => isStrongPassword(v, {
                minLength: 6,
                minLowercase: 1,
                minUppercase: 1,
                minNumbers: 1,
                minSymbols: 0
            }) || 'This field must have at least 1 Uppercase char, 1 Lowercase char and a number.'
        ],
        confirmPasswordRules: [],
        showPassword: false,
    }),
    created () {
        this.confirmPasswordRules = [
            v => v === this.user.password || 'Password must be confirmed.'
        ]
    },
    methods: {
        async submit () {
            if (this.$refs.form.validate()) {
                await this.$store.dispatch('registerUser', {
                    name: this.user.name,
                    email: this.user.email,
                    password: this.user.password,
                    password_confirmation: this.user.password_confirmation
                })
                this.$router.push('/')
            }
        }
    }
}
</script>

<style scoped>
    .register-wrapper {
        height: 100vh;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>>
