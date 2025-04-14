<script setup>
import { onMounted, watch, ref, defineEmits } from "vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
const props = defineProps({
    openModal: {
        type: Boolean,
        default: false,
    },
});
const { props: propsPage } = usePage();
const { oConfiguracion } = useConfiguracion();
const form = useForm({
    usuario: "",
    password: "",
});

const open_modal = ref(props.openModal);
var url_assets = "";
var url_principal = "";
const muestra_password = ref(false);

watch(
    () => props.openModal,
    (newVal) => {
        open_modal.value = newVal;
    }
);

const submit = () => {
    axios
        .post(route("login"), form)
        .then((response) => {
            router.get(route("portal.miCarrito"));
            window.location.reload();
        })
        .catch((error) => {
            console.log(error.response);
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                form.errors = {};
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        form.errors[field] = errors[field][0]; // Asignar solo el primer error
                    }
                }
            }
        });
};

const emits = defineEmits(["cerrarModal"]);

const cerrarDialog = () => {
    open_modal.value = false;
    emits("cerrarModal");
};

onMounted(() => {
});
</script>

<template>
    <div
        class="modal fade"
        id="modal-dialog-form"
        :class="{
            show: open_modal,
        }"
        :style="{
            display: open_modal ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <!-- BEGIN login -->
                    <div class="login login-v2 fw-bold">
                        <!-- BEGIN login-cover -->
                        <div class="login-cover">
                            <div
                                class="login-cover-img"
                                data-id="login-cover-image"
                            ></div>
                            <div class="login-cover-bg"></div>
                        </div>
                        <!-- END login-cover -->
                        <!-- BEGIN login-container -->
                        <div class="login-container">
                            <div class="w-100 text-center">
                                <img
                                    :src="oConfiguracion.url_logo"
                                    alt="Logo"
                                    class="logo_login"
                                    width="100%"
                                    lazy
                                />
                            </div>
                            <!-- BEGIN login-header -->
                            <div class="login-header">
                                <div class="brand">
                                    <div class="d-flex align-items-center">
                                        <b>{{ oConfiguracion.razon_social }}</b>
                                    </div>
                                </div>

                                <!-- <div class="icon">
                            <i class="fa fa-lock"></i>
                        </div> -->
                            </div>
                            <!-- END login-header -->

                            <!-- BEGIN login-content -->
                            <div class="login-content">
                                <div class="alert alert-info mt-3">
                                    Debes iniciar sesión para poder continuar
                                </div>
                                <form @submit.prevent="submit()">
                                    <div class="form-floating mb-20px">
                                        <input
                                            type="text"
                                            name="usuario"
                                            class="form-control fs-13px h-45px"
                                            placeholder="Correo"
                                            id="name"
                                            v-model="form.usuario"
                                            autofocus
                                        />
                                        <label
                                            for="name"
                                            class="d-flex align-items-center text-gray-600 fs-13px"
                                            >Correo</label
                                        >
                                    </div>
                                    <div class="">
                                        <div
                                            class="input-group mb-3 form-floating mb-20px"
                                        >
                                            <input
                                                :type="
                                                    muestra_password
                                                        ? 'text'
                                                        : 'password'
                                                "
                                                name="password"
                                                class="form-control fs-13px h-45px border"
                                                v-model="form.password"
                                                autocomplete="false"
                                                placeholder="Contraseña"
                                            />

                                            <label
                                                for="name"
                                                class="d-flex align-items-center text-gray-600 fs-13px"
                                                style="z-index: 100"
                                                >Contraseña</label
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
                                        v-if="form.errors?.usuario"
                                    >
                                        <span
                                            class="invalid-feedback alert alert-danger"
                                            style="display: block"
                                            role="alert"
                                        >
                                            <strong>{{
                                                form.errors.usuario
                                            }}</strong>
                                        </span>
                                    </div>
                                    <div class="mb-15px">
                                        <button
                                            type="submit"
                                            class="btn btn-dark d-block w-100 h-45px btn-lg"
                                        >
                                            Ingresar
                                        </button>
                                    </div>
                                    <div class="mb-15px">
                                        <a
                                            :href="route('registro')"
                                            class="btn btn-default d-block w-100 h-45px btn-lg"
                                        >
                                            Registrarse
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <!-- END login-content -->
                        </div>
                        <!-- END login-container -->
                    </div>
                    <!-- END login -->
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
