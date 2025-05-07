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
});

const { oProducto, limpiarProducto } = useProductos();
const { axiosGet, axiosPost, axiosDelete } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);

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
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i>`
        : `<i class="fa fa-random"></i> Relación de productos`;
});

const sucursal_id = ref(
    props_page.auth?.user.sucursals_todo == 0
        ? props_page.auth?.user.sucursal_id
        : ""
);
const producto_relacion_id = ref(null);
const guardarRelacion = async () => {
    if (producto_relacion_id.value) {
        const response = await axiosPost(
            route("productos.relacion", oProducto.value.id),
            {
                producto_relacion_id: producto_relacion_id.value,
            }
        );
        if (response) {
            cargarRelaciones();
        }
    } else {
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `Debes seleccionar un producto`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
    }
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

const listProductos = ref([]);
const listProductoRelacions = ref([]);
const listSucursals = ref([]);
const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
    });
};

const cargarProductos = async () => {
    const data = await axiosGet(route("productos.listadoSinProducto"), {
        producto_id: oProducto.value.id,
    });
    listProductos.value = data.productos;
};

const cargarRelaciones = async () => {
    const data = await axiosGet(
        route("producto_relacions.listadoPorProducto", oProducto.value.id),
        {
            sucursal_id: sucursal_id.value,
        }
    );
    listProductoRelacions.value = data.producto_relacions;
};

const eliminarRelacion = async (id) => {
    const resp = await axiosDelete(route("productos_relacion.destroy", id));
    if (resp.sw) {
        cargarRelaciones();
    }
};

const cargarListas = () => {
    cargarProductos();
    cargarRelaciones();
    cargarSucursals();
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
                        <hr class="my-2" />
                        <div
                            class="col-12 mb-2"
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'productos.relacion'
                                )
                            "
                        >
                            <div class="row">
                                <div class="col-12">
                                    <el-select
                                        class="w-100"
                                        clearable
                                        placeholder="- Seleccionar Producto -"
                                        no-data-text="Sin datos"
                                        filterable
                                        v-model="producto_relacion_id"
                                    >
                                        <el-option
                                            v-for="item in listProductos"
                                            :key="item.id"
                                            :value="item.id"
                                            :label="item.nombre"
                                        />
                                    </el-select>
                                </div>
                                <div class="col-12 mt-1">
                                    <div class="col-md-6 mx-auto">
                                        <button
                                            class="btn btn-primary btn-sm w-100"
                                            @click="guardarRelacion"
                                        >
                                            <i class="fa fa-plus"></i>Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h4 class="w-100 text-center">
                                Productos relacionados
                            </h4>
                            <!-- <div
                                class="row my-1"
                                v-if="props_page.auth?.user.sucursals_todo == 1"
                            >
                                <div class="col-md-6 mx-auto">
                                    <select
                                        name=""
                                        id=""
                                        class="form-control"
                                        @change="cargarRelaciones"
                                        v-model="sucursal_id"
                                    >
                                        <option value="">
                                            - Seleccionar Sucursal -
                                        </option>
                                        <option
                                            v-for="item in listSucursals"
                                            :value="item.id"
                                        >
                                            {{ item.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div> -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>C/U</th>
                                        <th>Con Factura</th>
                                        <th>Sin Factura</th>
                                        <!-- <th>Stock Actual</th> -->
                                        <th
                                            v-if="
                                                props_page.auth?.user
                                                    .permisos == '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'productos.relacion'
                                                )
                                            "
                                        ></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in listProductoRelacions">
                                        <td>
                                            {{ item.o_producto_relacion.nombre }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion
                                                    .precio_pred
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion.monto_cf
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.o_producto_relacion.monto_sf
                                            }}
                                        </td>
                                        <!-- <td>
                                            {{ item.stock_actual ?? "-" }}
                                        </td> -->
                                        <td
                                            v-if="
                                                props_page.auth?.user
                                                    .permisos == '*' ||
                                                props_page.auth?.user.permisos.includes(
                                                    'productos.relacion'
                                                )
                                            "
                                        >
                                            <button
                                                class="btn btn-danger"
                                                @click="
                                                    eliminarRelacion(item.id)
                                                "
                                            >
                                                <i class="fa fa-trash"></i>
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
