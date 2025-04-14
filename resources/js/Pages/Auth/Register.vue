<script>
import Login from "@/Layouts/Login.vue";
import TerminosCondiciones from "./TerminosCondiciones.vue";
import { onMounted, ref, computed } from "vue";
export default {
    layout: Login,
};
</script>
<script setup>
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";

import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
const { props } = usePage();
const { oConfiguracion } = useConfiguracion();
const form = useForm({
    nombres: "",
    apellidos: "",
    cel: "",
    correo: "",
    password: "",
    password_confirmation: "",
});

const muestra_password = ref(false);
const muestra_password2 = ref(false);

const errors = ref([]);

var url_assets = "";
var url_principal = "";

const validando = ref(false);
const validado = ref(false);
const muestraCheck = ref(false);
const modal_dialog_tc = ref(false);

const enviando = ref(false);
const submit = () => {
    enviando.value = true;
    let config = {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    };
    let formdata = new FormData();
    formdata.append("nombres", form.nombres);
    formdata.append("apellidos", form.apellidos);
    formdata.append("cel", form.cel);
    formdata.append("correo", form.correo);
    formdata.append("password", form.password);
    formdata.append("password_confirmation", form.password_confirmation);
    axios
        .post(route("register"), form, config)
        .then((response) => {
            window.location = route("portal.index");
        })
        .catch((error) => {
            enviando.value = false;
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                form.errors = {};
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        form.errors[field] = errors[field][0]; // Asignar solo el primer error
                    }
                }
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${error.response.data.message}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            }
        });
};

onMounted(() => {
    url_assets = props.url_assets;
    url_principal = props.url_principal;
});
</script>

<template>
    <Head title="Registro"></Head>
    <div class="loading" v-if="enviando">
        <i class="fa fa-spinner fa-spin fa-3x text-blue"></i>
    </div>
    <TerminosCondiciones
        :open_dialog="modal_dialog_tc"
        @cerrar-dialog="modal_dialog_tc = false"
    ></TerminosCondiciones>
    <div class="contenedor_login">
        <div id="app" class="app">
            <div class="login login-v2 fw-bold">
                <div class="login-cover">
                    <div
                        class="login-cover-img"
                        data-id="login-cover-image"
                    ></div>
                    <div class="login-cover-bg"></div>
                </div>
                <div class="login-container">
                    <div class="w-100 text-center">
                        <img
                            :src="oConfiguracion.url_logo"
                            alt="Logo"
                            class="logo_login"
                            lazy
                        />
                    </div>
                    <!-- BEGIN login-header -->
                    <div class="login-header">
                        <div class="brand">
                            <div class="d-flex align-items-center">
                                <b>Regístrate</b>
                            </div>
                        </div>
                    </div>
                    <!-- END login-header -->
                    <!-- BEGIN login-content -->
                    <div class="login-content">
                        <div class="row mb-0">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    Todos los campos con <strong>*</strong> son
                                    obligatorios
                                </div>
                            </div>
                        </div>
                        <form @submit.prevent="submit()">
                            <div class="row" v-show="!validado">
                                <div class="col-12">
                                    <div class="form-floating mt-20px">
                                        <input
                                            type="text"
                                            name="nombres"
                                            class="form-control fs-13px h-45px border-0"
                                            placeholder="Nombre(s)"
                                            v-model="form.nombres"
                                            autofocus
                                        />
                                        <label
                                            for="name"
                                            class="d-flex align-items-center text-gray-600 fs-13px"
                                            >Nombre(s)*</label
                                        >
                                    </div>
                                    <div
                                        class="w-100"
                                        v-if="form.errors?.nombres"
                                    >
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.nombres
                                            }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-floating mt-20px">
                                        <input
                                            type="text"
                                            name="apellidos"
                                            class="form-control fs-13px h-45px border-0"
                                            placeholder="Apellido Paterno"
                                            v-model="form.apellidos"
                                        />
                                        <label
                                            for="name"
                                            class="d-flex align-items-center text-gray-600 fs-13px"
                                            >Apellidos*</label
                                        >
                                    </div>
                                    <div
                                        class="w-100"
                                        v-if="form.errors?.apellidos"
                                    >
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.apellidos
                                            }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-floating mt-20px">
                                        <input
                                            type="text"
                                            name="cel"
                                            class="form-control fs-13px h-45px border-0"
                                            placeholder="Documento de identidad"
                                            v-model="form.cel"
                                        />
                                        <label
                                            for="name"
                                            class="d-flex align-items-center text-gray-600 fs-13px"
                                            >Celular*</label
                                        >
                                    </div>
                                    <div class="w-100" v-if="form.errors?.cel">
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.cel
                                            }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-floating mt-20px">
                                        <input
                                            type="email"
                                            name="correo"
                                            class="form-control fs-13px h-45px border-0"
                                            placeholder="Correo electrónico"
                                            v-model="form.correo"
                                        />
                                        <label
                                            for="name"
                                            class="d-flex align-items-center text-gray-600 fs-13px"
                                            >Correo electrónico*</label
                                        >
                                    </div>
                                    <div
                                        class="w-100"
                                        v-if="form.errors?.correo"
                                    >
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.correo
                                            }}</strong>
                                        </span>
                                    </div>

                                    <div class="mt-20px">
                                        <div
                                            class="input-group mb-3 form-floating mb-1px"
                                        >
                                            <input
                                                :type="
                                                    muestra_password
                                                        ? 'text'
                                                        : 'password'
                                                "
                                                name="password"
                                                class="form-control fs-13px h-45px border-0"
                                                v-model="form.password"
                                                autocomplete="false"
                                                placeholder="Contraseña"
                                            />

                                            <label
                                                for="name"
                                                class="d-flex align-items-center text-gray-600 fs-13px"
                                                style="z-index: 100"
                                                >Contraseña*</label
                                            >
                                            <button
                                                class="btn btn-default"
                                                type="button"
                                                @click="
                                                    muestra_password =
                                                        !muestra_password
                                                "
                                            >
                                                <i
                                                    class="fa"
                                                    :class="[
                                                        muestra_password
                                                            ? 'fa-eye'
                                                            : 'fa-eye-slash',
                                                    ]"
                                                ></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="w-100"
                                        v-if="form.errors?.password"
                                    >
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.password
                                            }}</strong>
                                        </span>
                                    </div>
                                    <p>
                                        La contraseña debe tener al menos 8
                                        caracteres, incluyendo una letra
                                        mayúscula, un número y un símbolo
                                        (@$!%*?&).
                                    </p>
                                    <div>
                                        <div
                                            class="input-group mb-3 form-floating mb-20px"
                                        >
                                            <input
                                                :type="
                                                    muestra_password2
                                                        ? 'text'
                                                        : 'password'
                                                "
                                                name="password_confirmation"
                                                class="form-control fs-13px h-45px border-0"
                                                placeholder="Confirmar Contraseña"
                                                v-model="
                                                    form.password_confirmation
                                                "
                                                autocomplete="false"
                                            />
                                            <label
                                                for="name"
                                                class="d-flex align-items-center text-gray-600 fs-13px"
                                                style="z-index: 100"
                                                >Confirmar Contraseña*</label
                                            >
                                            <button
                                                class="btn btn-default"
                                                type="button"
                                                @click="
                                                    muestra_password2 =
                                                        !muestra_password2
                                                "
                                            >
                                                <i
                                                    class="fa"
                                                    :class="[
                                                        muestra_password2
                                                            ? 'fa-eye'
                                                            : 'fa-eye-slash',
                                                    ]"
                                                ></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button
                                        type="submit"
                                        class="btn btn-theme h-45px btn-lg w-100"
                                    >
                                        Registrar
                                    </button>
                                </div>
                            </div>

                            <div class="mt-20px">
                                <Link
                                    :href="route('login')"
                                    class="text-white d-block w-100 text-center"
                                    >Ya tengo una cuenta</Link
                                >
                            </div>
                            <div class="mt-20px mb-20px">
                                <a
                                    :href="route('portal.index')"
                                    class="text-white d-block w-100 text-center"
                                    >Volver al portal</a
                                >
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.contenedor_login {
    justify-content: center;
    width: 100%;
    height: 100%;
}

.logo_login {
    width: 100%;
}

.login-cover .login-cover-bg {
    /* background: linear-gradient(to bottom, #153f59, #94b8d7); */
    background: var(--principal);
}

.loading {
    position: fixed;
    height: 100vh;
    width: 100vw;
    background-color: rgba(212, 212, 212, 0.863);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
