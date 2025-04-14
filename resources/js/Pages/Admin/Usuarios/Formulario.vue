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
const listSedes = ref([]);
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
const foto = ref(null);

function cargaArchivo(e, key) {
    form[key] = null;
    form[key] = e.target.files[0];
}

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nuevo Usuario`
        : `<i class="fa fa-edit"></i> Editar Usuario`;
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
    cargarSedes();
};

const cargarRoles = async () => {
    axios.get(route("roles.listado")).then((response) => {
        listRoles.value = response.data.roles;
    });
};

const cargarSedes = async () => {
    axios.get(route("sedes.listado")).then((response) => {
        listSedes.value = response.data.sedes;
    });
};

const indeterminate = ref(false);
watch(
    () => form.id_almacens,
    (val) => {
        if (val.length === 0) {
            form.sedes_todo = false;
            indeterminate.value = false;
        } else if (val.length === listSedes.value.length) {
            form.sedes_todo = true;
            indeterminate.value = false;
        } else {
            indeterminate.value = true;
        }
    }
);

const handleCheckAll = (val) => {
    indeterminate.value = false;
    if (val) {
        form.array_sedes_id = listSedes.value.map((_) => _.id);
    } else {
        console.log(form.array_sedes_id)
        if(form.id == 0){
            form.array_sedes_id = [];
        }
    }
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
                            <div class="col-md-4">
                                <label>Nombre(s)*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.nombres,
                                    }"
                                    v-model="form.nombres"
                                />
                                <ul
                                    v-if="form.errors?.nombres"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.nombres }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Ap. Paterno*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.apellidos,
                                    }"
                                    v-model="form.apellidos"
                                />

                                <ul
                                    v-if="form.errors?.apellidos"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.apellidos }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Número de C.I.*</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.ci,
                                    }"
                                    v-model="form.ci"
                                />

                                <ul
                                    v-if="form.errors?.ci"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.ci }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Expedido*</label>
                                <select
                                    class="form-select"
                                    :class="{
                                        'parsley-error': form.errors?.ci_exp,
                                    }"
                                    v-model="form.ci_exp"
                                >
                                    <option value="">- Seleccione -</option>
                                    <option
                                        v-for="item in listExpedido"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                                <ul
                                    v-if="form.errors?.ci_exp"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.ci_exp }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Correo electrónico*</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.correo,
                                    }"
                                    v-model="form.correo"
                                />

                                <ul
                                    v-if="form.errors?.correo"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.correo }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar Rol*</label>
                                <select
                                    class="form-select"
                                    :class="{
                                        'parsley-error': form.errors?.role_id,
                                    }"
                                    v-model="form.role_id"
                                >
                                    <option value="">- Seleccione -</option>
                                    <option
                                        v-for="item in listRoles"
                                        :value="item.id"
                                    >
                                        {{ item.nombre }}
                                    </option>
                                </select>

                                <ul
                                    v-if="form.errors?.role_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.role_id }}
                                    </li>
                                </ul>
                            </div>    <div class="col-md-4 mt-2">
                                <label>Seleccionar Sede*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'border border-red rounded':
                                            form.errors?.array_sedes_id,
                                    }"
                                    multiple
                                    clearable
                                    collapse-tags
                                    placeholder="- Seleccione -"
                                    popper-class="custom-header"
                                    no-data-text="Sin datos"
                                    :max-collapse-tags="1"
                                    filterable
                                    v-model="form.array_sedes_id"
                                >
                                    <template #header>
                                        <el-checkbox
                                            v-model="form.almacen_todos"
                                            :indeterminate="indeterminate"
                                            @change="handleCheckAll"
                                        >
                                            Todos
                                        </el-checkbox>
                                    </template>
                                    <el-option
                                        v-for="item in listSedes"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.array_sedes_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.array_sedes_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Foto</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.foto,
                                    }"
                                    ref="foto"
                                    @change="cargaArchivo($event, 'foto')"
                                />

                                <ul
                                    v-if="form.errors?.foto"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.foto }}
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
                                    id="acceso"
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
