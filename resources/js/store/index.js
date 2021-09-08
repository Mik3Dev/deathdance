import axios from "axios";
import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        appointments: [],
        appointment: null,
        user: null,
        accessToken: null
    },
    mutations: {
        SET_APPOINTMENTS(state, payload) {
            state.appointments = payload;
        },
        SET_APPOINTMENT(state, payload) {
            state.appointment = payload;
        },
        SET_USER(state, payload) {
            state.user = payload;
        },
        SET_ACCESS_TOKEN(state, payload) {
            const { localStorage } = window;
            localStorage.setItem("accessToken", JSON.stringify(payload));
            state.accessToken = payload;
        },
        GET_ACCESS_TOKEN(state) {
            const { localStorage } = window;
            const accessToken = JSON.parse(localStorage.getItem("accessToken"));
            state.accessToken = accessToken;
        },
        CLEAR_ACCESS_TOKEN(state) {
            const { localStorage } = window;
            localStorage.removeItem("accessToken");
            state.accessToken = null;
        }
    },
    actions: {
        async registerUser({ commit }, payload) {
            const { name, email, password, password_confirmation } = payload;
            try {
                const { data } = await axios.post("/api/auth/register", {
                    name,
                    email,
                    password,
                    password_confirmation
                });
                commit("SET_USER", data.user);
                commit("SET_ACCESS_TOKEN", data.access_token);
            } catch (error) {
                commit("SET_USER", null);
                commit("CLEAR_ACCESS_TOKEN");
            }
        },
        async login({ commit }, payload) {
            const { email, password } = payload;
            try {
                const { data } = await axios.post("/api/auth/login", {
                    email,
                    password
                });
                commit("SET_ACCESS_TOKEN", data.access_token);
            } catch (error) {
                commit("SET_USER", null);
                commit("CLEAR_ACCESS_TOKEN");
            }
        },
        async getCurrentUser({ commit, state }, payload) {
            try {
                commit("GET_ACCESS_TOKEN");
                const { data } = await axios.get("/api/auth/me", {
                    headers: {
                        Authorization: `Bearer ${state.accessToken}`
                    }
                });
                commit("SET_USER", data.user);
            } catch (error) {
                // commit("SET_USER", null);
                // commit("CLEAR_ACCESS_TOKEN");
            }
        },
        async logout({ commit }) {
            try {
                await axios.post("/api/auth/logout", {
                    headers: {
                        Authorization: `Bearer ${state.accessToken}`
                    }
                });
                commit("SET_USER", null);
                commit("CLEAR_ACCESS_TOKEN");
            } catch (error) {
                commit("SET_USER", null);
                commit("CLEAR_ACCESS_TOKEN");
            }
        },
        async getAppointments({ commit }) {
            try {
                const { data } = await axios.get("api/appointments");
                commit("SET_APPOINTMENTS", data.data);
            } catch (error) {
                commit("SET_APPOINTMENTS", []);
            }
        },
        async getAppointment({ commit, state }, payload) {
            const { appointmentId } = payload;
            try {
                const { data } = await axios.get(
                    `api/appointments/${appointmentId}`,
                    {
                        headers: {
                            Authorization: `Bearer ${state.accessToken}`
                        }
                    }
                );
                commit("SET_APPOINTMENT", data.data);
            } catch (error) {
                commit("SET_APPOINTMENT", null);
            }
        },
        async storeAppointment({ commit, state }, payload) {
            const { description, appointment_datetime } = payload;
            try {
                const { data } = await axios.post(
                    "/api/appointments/",
                    {
                        description,
                        appointment_datetime
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${state.accessToken}`
                        }
                    }
                );
                commit("SET_APPOINTMENT", data.data);
            } catch (error) {
                commit("SET_APPOINTMENT", null);
            }
        },
        async updateAppointment({ commit, state }, payload) {
            const {
                appointmentId,
                description,
                appointment_datetime
            } = payload;
            try {
                const { data } = await axios.put(
                    `/api/appointments/${appointmentId}`,
                    {
                        description,
                        appointment_datetime
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${state.accessToken}`
                        }
                    }
                );
                commit("SET_APPOINTMENT", data.data);
            } catch (error) {
                commit("SET_APPOINTMENT", null);
            }
        },
        async deleteAppointment({ commit, state }, payload) {
            const { appointmentId } = payload;
            try {
                await axios.delete(`/api/appointments/${appointmentId}`, {
                    headers: {
                        Authorization: `Bearer ${state.accessToken}`
                    }
                });
                commit("SET_APPOINTMENT", null);
            } catch (error) {
                commit("SET_APPOINTMENT", null);
            }
        }
    }
});

export default store;
