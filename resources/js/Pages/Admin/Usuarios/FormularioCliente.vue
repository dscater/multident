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
let switcheryInstanceCliente = null;
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            const accesoCheckbox = $("#accesoCliente");
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

const listExpedido = [
    { value: "LP", label: "La Paz" },
    { value: "CB", label: "Cochabamba" },
    { value: "SC", label: "Santa Cruz" },
    { value: "CH", label: "Chuquisaca" },
    { value: "OR", label: "Oruro" },
    { value: "PT", label: "Potosi" },
    { value: "TJ", label: "Tarija" },
    { value: "PD", label: "Pando" },
    { value: "BN", label: "Beni" },
];

function cargaArchivo(e, key) {
    form.cliente[key] = null;
    form.cliente[key] = e.target.files[0];
}

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nuevo Usuario`
        : `<i class="fa fa-edit"></i> Editar Usuario`;
});

const initializeSwitcher = () => {
    const accesoCheckbox = document.getElementById("accesoCliente");
    if (accesoCheckbox) {
        // Destruye la instancia previa si existe
        // Inicializa Switchery
        switcheryInstanceCliente = new Switchery(accesoCheckbox, {
            color: "#0078ff",
        });
    }
};

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("usuarios.store")
            : route("clientes.update", form.cliente.id);

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

onMounted(() => {
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
                <div class="modal-body" v-if="form.cliente">
                    <form @submit.prevent="enviarFormulario()">
                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <label>Nombre(s)*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors &&
                                            form.errors['cliente.nombres'],
                                    }"
                                    v-model="form.cliente.nombres"
                                />
                                <ul
                                    v-if="
                                        form.errors &&
                                        form.errors['cliente.nombres']
                                    "
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors["cliente.nombres"] }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Apellidos*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors &&
                                            form.errors['cliente.apellidos'],
                                    }"
                                    v-model="form.cliente.apellidos"
                                />

                                <ul
                                    v-if="
                                        form.errors &&
                                        form.errors['cliente.apellidos']
                                    "
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors["cliente.apellidos"] }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Correo*</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors &&
                                            form.errors['cliente.correo'],
                                    }"
                                    v-model="form.cliente.correo"
                                />

                                <ul
                                    v-if="
                                        form.errors &&
                                        form.errors['cliente.correo']
                                    "
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors["cliente.correo"] }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Celular*</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors &&
                                            form.errors['cliente.cel'],
                                    }"
                                    v-model="form.cliente.cel"
                                />

                                <ul
                                    v-if="
                                        form.errors &&
                                        form.errors['cliente.cel']
                                    "
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors["cliente.cel"] }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label for="flexSwitchCheckChecked"
                                    >Acceso*</label
                                ><br />
                                <input
                                    type="checkbox"
                                    :class="{
                                        'parsley-error': form.errors?.acceso,
                                    }"
                                    data-render="switchery"
                                    data-theme="blue"
                                    :true-value="'1'"
                                    :false-value="'0'"
                                    v-model="form.acceso"
                                    id="accesoCliente"
                                />

                                <ul
                                    v-if="form.errors?.acceso"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.acceso }}
                                    </li>
                                </ul>
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
                    <button
                        type="button"
                        @click="enviarFormulario()"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
