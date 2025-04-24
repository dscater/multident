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

const { axiosGet, axiosPost, axiosDelete } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
const listProductoSucursals = ref([]);
const buscar = ref("");
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
        ? `<i class="fa fa-list"></i> Productos por Sucursal`
        : `<i class="fa fa-list"></i> Productos por Sucursal`;
});

const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const setIntervalPS = ref(null);
const buscarProductosSucursal = () => {
    clearInterval(setIntervalPS.value);

    setIntervalPS.value = setTimeout(() => {
        axios
            .get(route("producto_sucursals.getProductoSucursales"), {
                params: { search: buscar.value },
            })
            .then((response) => {
                listProductoSucursals.value = response.data.producto_sucursals;
            });
    }, 400);
};

const cerrarDialog = () => {
    dialog.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const cargarListas = () => {};

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
                        <div class="col-12">
                            <label>Buscar producto:</label>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Buscar..."
                                v-model="buscar"
                                @keyup="buscarProductosSucursal"
                            />
                        </div>
                        <div class="col-12 mt-3">
                            <h4 class="w-100 text-center">Productos</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sucursal</th>
                                        <th>Producto</th>
                                        <th>P/U</th>
                                        <th>Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in listProductoSucursals">
                                        <td>
                                            {{ item.sucursal.nombre }}
                                        </td>
                                        <td>
                                            {{
                                                item.producto.nombre
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                item.producto.monto_sf
                                            }}
                                        </td>
                                        <td>
                                            {{ item.stock_actual }}
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
