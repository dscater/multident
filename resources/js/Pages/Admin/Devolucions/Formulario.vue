<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useDevolucions } from "@/composables/devolucions/useDevolucions";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
const { props: props_page } = usePage();
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

const { oDevolucion, limpiarDevolucion } = useDevolucions();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oDevolucion.value);
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            form.sucursal_id =
                props_page.auth?.user.sucursals_todo == 0
                    ? props_page.auth?.user.sucursal_id
                    : form.sucursal_id;

            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oDevolucion.value);

            if (form.orden_venta_id) {
                getOrdenVenta({
                    detalle_orden_id: form.detalle_orden_id,
                });
            }
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

const listSucursals = ref([]);
const listOrdenVentas = ref([]);
const listDetalleOrdens = ref([]);
const listRazonDevolucions = ref(["DEFECTUOSO", "INCORRECTO", "VENCIDO"]);
const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nueva Promoción`
        : `<i class="fa fa-edit"></i> Editar Promoción`;
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("devolucions.store")
            : route("devolucions.update", form.id);

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
            limpiarDevolucion();
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
    cargarSucursals();
    cargarOrdenVentas();
};

const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
    });
};

const cargarOrdenVentas = async () => {
    axios.get(route("orden_ventas.listado")).then((response) => {
        listOrdenVentas.value = response.data.orden_ventas;
    });
};

const getOrdenVenta = (params = {}) => {
    listDetalleOrdens.value = [];
    if (form.orden_venta_id) {
        axios
            .get(route("orden_ventas.show", form.orden_venta_id), params)
            .then((response) => {
                listDetalleOrdens.value =
                    response.data.orden_venta.detalle_ordens;
            });
    }
};

onMounted(() => {
    cargarListas();
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
                            <div
                                class="col-md-12 form-group"
                                v-if="props_page.auth?.user.sucursals_todo == 1"
                            >
                                <label>Seleccionar Sucursal*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'border border-red rounded':
                                            form.errors?.sucursal_id,
                                    }"
                                    clearable
                                    placeholder="- Seleccione -"
                                    popper-class="custom-header"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="form.sucursal_id"
                                >
                                    <el-option
                                        v-for="item in listSucursals"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.sucursal_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.sucursal_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Nro. Orden de Venta*</label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="- Seleccionar Nro. Orden de Venta -"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="form.orden_venta_id"
                                    @change="getOrdenVenta"
                                >
                                    <el-option
                                        v-for="item in listOrdenVentas"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nro"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.orden_venta_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.orden_venta_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Seleccionar Producto*</label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="- Seleccionar Producto -"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="form.detalle_orden_id"
                                >
                                    <el-option
                                        v-for="item in listDetalleOrdens"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="`${item.producto.nombre} (${item.cantidad})`"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.detalle_orden_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.detalle_orden_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Razón de Devolución*</label>
                                <select
                                    class="form-control"
                                    v-model="form.razon"
                                >
                                    <option value="">- Seleccione -</option>
                                    <option
                                        v-for="item in listRazonDevolucions"
                                        :value="item"
                                    >
                                        {{ item }}
                                    </option>
                                </select>
                                <ul
                                    v-if="form.errors?.razon"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.razon }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Descripción</label>
                                <el-input
                                    type="textarea"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.descripcion,
                                    }"
                                    v-model="form.descripcion"
                                    autosize
                                >
                                </el-input>
                                <ul
                                    v-if="form.errors?.descripcion"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.descripcion }}
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
