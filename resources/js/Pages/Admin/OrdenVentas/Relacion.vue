<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useProductos } from "@/composables/productos/useProductos";
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
    producto: {
        type: Object,
        default: null,
    },
    idSucursal: {
        type: Number,
        default: 0,
    },
});

const oProducto = ref(props.producto);
const { axiosGet, axiosPost, axiosDelete } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
const sucursal_id = ref(
    props_page.auth?.user.sucursals_todo == 0
        ? props_page.auth?.user.sucursal_id
        : props.idSucursal
);

watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            cargarListas();
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
        }
    }
);
watch(
    () => props.idSucursal,
    (newValue) => {
        sucursal_id.value = newValue;
        cargarListas();
    }
);
watch(
    () => props.producto,
    (newValue) => {
        oProducto.value = newValue;
        cargarListas();
    }
);
watch(
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-random"></i> Productos relacionados`
        : `<i class="fa fa-random"></i> Relación de productos`;
});

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

const seleccionarProducto = (producto_id) => {
    emits("envio-formulario", producto_id);
};

const listProductoRelacions = ref([]);

const cargarRelaciones = async () => {
    const data = await axiosGet(
        route("producto_relacions.listadoPorProducto", oProducto.value?.id),
        {
            sucursal_id: sucursal_id?.value,
        }
    );
    listProductoRelacions.value = data.producto_relacions;
    console.log(listProductoRelacions.value);
};

const cargarListas = () => {
    if (dialog.value) {
        cargarRelaciones();
    }
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
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" v-if="oProducto">
                            <div class="card">
                                <div class="card-body">
                                    <p>
                                        <strong>Producto: </strong
                                        >{{ oProducto.nombre }}
                                    </p>
                                    <p>
                                        <strong>C/U: </strong
                                        >{{ oProducto.precio_pred }}
                                    </p>
                                    <p>
                                        <strong>C/F: </strong
                                        >{{ oProducto.monto_cf }}
                                    </p>
                                    <p>
                                        <strong>S/F: </strong
                                        >{{ oProducto.monto_sf }}
                                    </p>
                                    <p>
                                        <strong>Descripción: </strong
                                        >{{ oProducto.descripcion }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <h4 class="w-100 text-center">
                                Productos relacionados
                            </h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>C/U</th>
                                        <th>Con Factura</th>
                                        <th>Sin Factura</th>
                                        <th>Stock Actual</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in listProductoRelacions">
                                        <td>
                                            {{
                                                item.o_producto_relacion.nombre
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion
                                                    .precio_pred
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion
                                                    .monto_cf
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion
                                                    .monto_sf
                                            }}
                                        </td>
                                        <td>
                                            {{ item.stock_actual ?? "-" }}
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-primary"
                                                @click.prevent="
                                                    seleccionarProducto(
                                                        item.producto_relacion
                                                    )
                                                "
                                            >
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
