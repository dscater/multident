<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useUsuarios } from "@/composables/usuarios/useUsuarios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    accion_dialog: {
        type: Number,
        default: 0,
    },
});

const { oUsuario, limpiarUsuario } = useUsuarios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oUsuario.value);
let switcheryInstance = null;
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            const accesoCheckbox = $("#acceso");
            if (oUsuario.value.acceso == 1) {
                accesoCheckbox.prop("checked", false).trigger("click");
            } else {
                accesoCheckbox.prop("checked", true).trigger("click");
            }

            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oUsuario.value);
        }
    }
);
watch(
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const { flash } = usePage().props;

const listRoles = ref([]);
// const listExpedido = [
//     { value: "LP", label: "La Paz" },
//     { value: "CB", label: "Cochabamba" },
//     { value: "SC", label: "Santa Cruz" },
//     { value: "CH", label: "Chuquisaca" },
//     { value: "OR", label: "Oruro" },
//     { value: "PT", label: "Potosi" },
//     { value: "TJ", label: "Tarija" },
//     { value: "PD", label: "Pando" },
//     { value: "BN", label: "Beni" },
// ];
const foto = ref(null);

function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];
}

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nuevo Usuario`
        : `<i class="fa fa-id-card"></i> Información Cliente`;
});

const initializeSwitcher = () => {
    const accesoCheckbox = document.getElementById("acceso");
    if (accesoCheckbox) {
        // Destruye la instancia previa si existe
        // Inicializa Switchery
        switcheryInstance = new Switchery(accesoCheckbox, {
            color: "#0078ff",
        });
    }
};
const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("usuarios.store")
            : route("usuarios.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            dialog.value = false;
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            limpiarUsuario();
            emits("envio-formulario");
        },
        onError: (err) => {
            console.log("ERROR");
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const cerrarDialog = () => {
    dialog.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const cargarListas = () => {
    cargarRoles();
};

const cargarRoles = async () => {
    axios.get(route("roles.listado")).then((response) => {
        listRoles.value = response.data.roles;
    });
};

onMounted(() => {
    cargarListas();
    initializeSwitcher();
});
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
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="enviarFormulario()">
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Nombre: </strong> {{ oUsuario.cliente?.full_name }}</p>
                                <p><strong>C.I.: </strong>{{ oUsuario.cliente?.full_ci }}</p>
                                <p><strong>Complemento: </strong>{{ oUsuario.cliente?.complemento }}</p>
                                <p><strong>Número de celular: </strong>{{ oUsuario.cliente?.fono }}</p>
                                <p><strong>Departamento de residencia: </strong>{{ oUsuario.cliente?.dpto_residencia }}</p>
                                <p class="mb-4"><strong>Correo electrónico: </strong>{{ oUsuario.cliente?.email }}</p>
                                <h4>Datos complementario</h4>
                                <p><strong>Foto del C.I. anverso: </strong><a :href="oUsuario.cliente?.url_ci_anverso" target="_blank">Descargar</a></p>
                                <p class="mb-4"><strong>Foto del C.I. reverso: </strong><a :href="oUsuario.cliente?.url_ci_reverso" target="_blank">Descargar</a></p>
                                <h4>Datos para devolución</h4>
                                <p><strong>Banco: </strong>{{ oUsuario.cliente?.banco }}</p>
                                <p><strong>Número de cuenta: </strong>{{ oUsuario.cliente?.nro_cuenta }}</p>
                                <p><strong>Moneda de cuenta bancaria: </strong>{{ oUsuario.cliente?.moneda }}</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-white"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
