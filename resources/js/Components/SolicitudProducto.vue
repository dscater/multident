<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted } from "vue";
import DatosPago from "@/Components/DatosPago.vue";
import { useFormater } from "@/composables/useFormater";
import { useAxios } from "@/composables/axios/useAxios";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import Vue3Recaptcha2 from "vue3-recaptcha2";
const { oConfiguracion } = useConfiguracion();
const { getFormatoMoneda } = useFormater();
const { auth } = usePage().props;
const { axiosGet } = useAxios();
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const enviando = ref(false);
const dialog = ref(props.open_dialog);
const form = ref({
    sede_id: "",
    cliente_id: auth?.user?.cliente.id,
    token_captcha: "",
    solicitudes: [
        {
            nombre_producto: "",
            detalle_producto: "",
            links_referencia: "",
        },
    ],
});
const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);
const cargarFile = (e) => {
    const file = e.target.files[0];
    form.value.comprobante = null;
    if (file) {
        form.value.comprobante = file;
    }
};

watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            cargarSedes();
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
        }
    }
);
const tituloDialog = computed(() => {
    return `<i class="fa fa-clipboard-list"></i> Solicitud de producto`;
});

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const txtBotonEnviar = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spinner fa-spin"></i> Enviando...`;
    }
    return `<i class="fa fa-send"></i> Registrar solicitud`;
});

const cerrarDialog = () => {
    dialog.value = false;
    enviando.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const errors = ref(null);
const recaptchaRef = ref(null);
const token = ref("");

const onVerify = (response) => {
    token.value = response;
};

const resetRecaptcha = () => {
    recaptchaRef.value.reset(); // Reiniciar el captcha
};
const enviarFormulario = () => {
    form.value.token_captcha = token.value;
    enviando.value = true;
    axios
        .post(route("solicitud_productos.store"), form.value)
        .then((response) => {
            dialog.value = false;
            errors.value = null;
            resetRecaptcha();
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${
                    response.message ? response.message : "Proceso realizado"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            emits("envio-formulario");
        })
        .catch((error) => {
            console.log("ERROR");
            console.log(error);
            if (error.response && error.response.status == 422) {
                errors.value = error.response.data.errors;
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `Tienes errores en el formulario`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            } else if (error.response && error.response.status == 400) {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${error.response.data.message}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `Ocurrió un error inesperado intente mas tarde por favor`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            }
        })
        .finally(() => {
            enviando.value = false;
        });
};

const limpiarForm = () => {
    form.value.sede_id = "";
    form.value.cliente_id = auth?.user?.cliente.id;
    form.value.token_captcha = "";
    form.value.solicitudes = [
        {
            nombre_producto: "",
            detalle_producto: "",
            links_referencia: "",
        },
    ];
    errors.value = null;
};

const verificaInicioSesion = () => {
    if (!auth.user) {
        modalLogin.value = true;
        return null;
    }
    return auth;
};
const listSedes = ref([]);
const cargarSedes = async () => {
    const data = await axiosGet(route("sedes.listado"));
    listSedes.value = data.sedes;
};

onMounted(() => {});
</script>

<template>
    <div
        class="modal fade"
        :class="{
            show: dialog,
        }"
        id="modal-dialog-form"
        :style="{
            display: dialog ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="enviarFormulario">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info text-sm">
                                    Los campos con * son obligatorios
                                </div>
                            </div>
                            <h4>SOLICITANTE</h4>
                            <div class="col-md-6 form-group">
                                <label>Nombres*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    readonly
                                    :value="auth?.user?.cliente?.nombres"
                                />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Apellidos*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    readonly
                                    :value="auth?.user?.cliente?.apellidos"
                                />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Celular*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    readonly
                                    :value="auth?.user?.cliente?.cel"
                                />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Correo electrónico*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    readonly
                                    :value="auth?.user?.cliente?.correo"
                                />
                            </div>
                        </div>
                        <hr />
                        <div class="row" v-for="item in form.solicitudes">
                            <h4>SOLICITUD DE PRODUCTO</h4>
                            <div class="col-md-4 form-group">
                                <label>Nombre del producto*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="item.nombre_producto"
                                />
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Detalle del producto*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="item.detalle_producto"
                                />
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Links de referencia del producto*</label>
                                <el-input
                                    type="textarea"
                                    v-model="item.links_referencia"
                                    autosize
                                ></el-input>
                            </div>
                        </div>
                        <div class="row" v-if="errors && errors.solicitudes">
                            <div class="col-12">
                                <div
                                    class="alert alert-danger text-sm border-0"
                                >
                                    {{ errors.solicitudes[0] }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label
                                    >De que departamento solicita el
                                    producto*</label
                                >
                                <select
                                    class="form-select"
                                    v-model="form.sede_id"
                                >
                                    <option value="">- Seleccione -</option>
                                    <option
                                        v-for="item in listSedes"
                                        :value="item.id"
                                    >
                                        {{ item.nombre }}
                                    </option>
                                </select>

                                <span
                                    class="text-danger font-weight-bold d-block"
                                    v-if="errors && errors.sede_id"
                                    >{{ errors.sede_id[0] }}</span
                                >
                            </div>
                        </div>
                        <div class="row">
                            <hr />
                            <div class="col-12 form-group text-center mb-3">
                                <Vue3Recaptcha2
                                    class="d-flex justify-content-center w-100"
                                    v-if="
                                        oConfiguracion &&
                                        oConfiguracion.conf_captcha &&
                                        oConfiguracion.conf_captcha.claveSitio
                                    "
                                    ref="recaptchaRef"
                                    :sitekey="
                                        oConfiguracion?.conf_captcha?.claveSitio
                                    "
                                    @verify="onVerify"
                                />
                                <span
                                    class="text-danger font-weight-bold d-block"
                                    v-if="errors && errors.token_captcha"
                                    >{{ errors.token_captcha[0] }}</span
                                >
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-default"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                    <button
                        type="button"
                        @click="enviarFormulario()"
                        class="btn btn-dark"
                        v-html="txtBotonEnviar"
                        :disabled="enviando"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</template>
