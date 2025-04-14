<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});

import { onMounted, ref } from "vue";
// componentes
import { usePage, Head, useForm } from "@inertiajs/vue3";
const props_page = defineProps({
    configuracion: {
        type: Object,
        default: null,
    },
});
const { props } = usePage();

let form = null;
if (props_page.configuracion != null) {
    props_page.configuracion["_method"] = "put";
    form = useForm(props_page.configuracion);
} else {
    form = useForm({
        _method: "put",
        id: 0,
        nombre_sistema: "",
        alias: "",
        logo: "",
        fono: "",
        dir: "",
        conf_correos: {
            host: "smtp.hostinger.com",
            correo: "mensaje@emsytsrl.com",
            driver: "smtp",
            nombre: "unibienes",
            puerto: "587",
            password: "8Z@d>&kj^y",
            encriptado: "tls",
        },
        conf_moneda: {
            abrev: "Bs",
            moneda: "Bolivianos",
        },
        conf_captcha: {
            claveSitio: "AAAAAAA",
            claveBackend: "BBBBBBB",
        },
    });
}

const enviarFormulario = () => {
    form.post(route("configuracions.update"), {
        onSuccess: () => {
            // Mostrar mensaje de éxito
            limpiaRefs();
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>Proceso realizado con éxito</strong>`,
                showCancelButton: false,
                confirmButtonColor: "#056ee9",
                confirmButtonText: "Aceptar",
            });
        },
        onError: (err, code) => {
            console.log(code);
            console.log(err.response);
            console.log("error");
        },
    });
};
const logo = ref(null);
function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];

    // Generar la URL del archivo cargado
    const fileUrl = URL.createObjectURL(form[key]);
    form["url_" + key] = fileUrl;
}
function limpiaRefs() {
    logo.value = null;
}
onMounted(() => {});
</script>
<template>
    <Head title="Parametrización"></Head>
    <h3 class="text-center text-h4">CONFIGURACIÓN</h3>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                Todos los campos con <strong>*</strong> son obligatorios
            </div>
        </div>
    </div>
    <div class="row">
        <form @submit.prevent="enviarFormulario()">
            <div class="col-12">
                <div class="row">
                    <h4>Información</h4>
                    <div class="col-md-4 form-group mb-3">
                        <label for="">Nombre del sistema*</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="form.nombre_sistema"
                        />
                        <span
                            class="text-danger"
                            v-if="form.errors?.nombre_sistema"
                            >{{ form.errors.nombre_sistema }}</span
                        >
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label for="">Alias*</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="form.alias"
                        />
                        <span class="text-danger" v-if="form.errors?.alias">{{
                            form.errors.alias
                        }}</span>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label for="">Teléfono/Celular Principal*</label>
                        <input
                            type="text"
                            v-model="form.fono"
                            class="form-control"
                        />
                        <span class="text-danger" v-if="form.errors?.fono">{{
                            form.errors.fono
                        }}</span>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label for="">Dirección*</label>
                        <el-input
                            type="textarea"
                            v-model="form.dir"
                            autosize
                        ></el-input>
                        <span class="text-danger" v-if="form.errors?.dir">{{
                            form.errors.dir
                        }}</span>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label for="">Logo</label>
                        <input
                            type="file"
                            class="form-control"
                            @change="cargaArchivo($event, 'logo')"
                            ref="logo"
                        />
                        <div class="logo_muestra w-100 text-center">
                            <img
                                :src="form.url_logo"
                                alt=""
                                v-if="form.url_logo"
                                width="80%"
                            />
                        </div>
                        <span class="text-danger" v-if="form.errors?.logo">{{
                            form.errors.logo
                        }}</span>
                    </div>
                </div>
                <div class="row">
                    <h4>Servidor de correos</h4>
                    <template v-if="form.conf_correos">
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Host*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.host"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.host']
                                "
                                >{{ form.errors["conf_correos.host"] }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Puerto*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.puerto"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.puerto']
                                "
                                >{{ form.errors["conf_correos.puerto"] }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Encriptado*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.encriptado"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.encriptado']
                                "
                                >{{
                                    form.errors["conf_correos.encriptado"]
                                }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Correo*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.correo"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.correo']
                                "
                                >{{ form.errors["conf_correos.correo"] }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Nombre*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.nombre"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.nombre']
                                "
                                >{{ form.errors["conf_correos.nombre"] }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Password*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.password"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.password']
                                "
                                >{{
                                    form.errors["conf_correos.password"]
                                }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Driver*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_correos.driver"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_correos.driver']
                                "
                                >{{ form.errors["conf_correos.driver"] }}</span
                            >
                        </div>
                    </template>
                </div>
                <div class="row">
                    <h4>Moneda</h4>
                    <template v-if="form.conf_moneda">
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Seleccionar moneda*</label>
                            <select
                                class="form-control"
                                v-model="form.conf_moneda.moneda"
                            >
                                <option value="">- Seleccione -</option>
                                <option value="Bolivianos">Bolivianos</option>
                                <option value="Dólares">Dólares</option>
                            </select>
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_moneda.moneda']
                                "
                                >{{ form.errors["conf_moneda.moneda"] }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Abreviatura*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_moneda.abrev"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_moneda.abrev']
                                "
                                >{{ form.errors["conf_moneda.abrev"] }}</span
                            >
                        </div>
                    </template>
                </div>
                <div class="row">
                    <h4>Captcha</h4>
                    <template v-if="form.conf_captcha">
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Clave Sitio Web*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_captcha.claveSitio"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_captcha.claveSitio']
                                "
                                >{{
                                    form.errors["conf_captcha.claveSitio"]
                                }}</span
                            >
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="">Clave Backend*</label>
                            <input
                                class="form-control"
                                v-model="form.conf_captcha.claveBackend"
                            />
                            <span
                                class="text-danger"
                                v-if="
                                    form.errors &&
                                    form.errors['conf_captcha.claveBackend']
                                "
                                >{{
                                    form.errors["conf_captcha.claveBackend"]
                                }}</span
                            >
                        </div>
                    </template>
                </div>
            </div>
            <div
                class="col-12"
                v-if="
                    props.auth.user.permisos == '*' ||
                    props.auth.user.permisos.includes('configuracions.edit')
                "
            >
                <button type="submit" class="btn btn-primary">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</template>
<style scoped>
.logo_muestra {
    margin-top: 10px;
    width: 100%;
    text-align: center;
}
.logo_muestra img {
    max-width: 100%;
}
</style>
