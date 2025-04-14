<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useCategorias } from "@/composables/sucursals/useSucursals";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import MiPaginacion from "@/Components/MiPaginacion.vue";
import VerificacionOrdenVenta from "@/Components/VerificacionOrdenVenta.vue";

const props = defineProps({
    codigo: {
        type: String,
        default: "",
    },
});

const { props: props_page } = usePage();
const { setLoading } = useApp();
onMounted(() => {});

const { axiosGet, axiosDelete } = useAxios();
const modalVerificacion = ref(false);
const oOrdenVenta = ref(null);
const paramsOrdenesVenta = ref({
    perPage: 12,
    page: 1,
    search: props.codigo,
    estado_orden: "PENDIENTE",
    fecha: "",
    orderByCol: "id",
    desc: "desc",
});
const paginacionOrdenVentas = ref({
    totalData: 0,
    perPage: paramsOrdenesVenta.value.perPage,
    currentPage: paramsOrdenesVenta.value.page,
    lastPage: 0,
});

const listEstados = ref([
    { value: "", label: "Todos" },
    { value: "PENDIENTE", label: "Pendientes" },
    { value: "RECHAZADO", label: "Rechazados" },
    { value: "CONFIRMADO", label: "Confirmados" },
]);
const listOrdenVentas = ref([]);

const cargarOrdenVentas = async () => {
    modalVerificacion.value = false;
    const data = await axiosGet(
        route("orden_ventas.paginado"),
        paramsOrdenesVenta.value
    );
    listOrdenVentas.value = data.ordenVentas;
    paginacionOrdenVentas.value.totalData = data.total;
    paginacionOrdenVentas.value.currentPage = paramsOrdenesVenta.value.page;
    paginacionOrdenVentas.value.lastPage = data.lastPage;
};
const updatePageOrdenVenta = (value) => {
    paramsOrdenesVenta.value.page = value;
    if (paramsOrdenesVenta.value.page < 0) paramsOrdenesVenta.value.page = 1;
    if (paramsOrdenesVenta.value.page > paginacionOrdenVentas.value.totalData)
        paramsOrdenesVenta.value.page = paginacionOrdenVentas.value.lastPage;
    cargarOrdenVentas();
};

const intervalSearch = ref(null);
const buscarOrden = () => {
    clearInterval(intervalSearch.value);
    intervalSearch.value = setTimeout(async () => {
        await cargarOrdenVentas();
    }, 400);
};

const abreModalVerificacion = (item) => {
    oOrdenVenta.value = item;
    modalVerificacion.value = true;
};
const cierreVerificacion = () => {
    oOrdenVenta.value = null;
    modalVerificacion.value = false;
};
onMounted(async () => {
    cargarOrdenVentas();
});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Ordenes de venta"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Ordenes de venta</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Ordenes de venta</h1>
    <!-- END page-header -->

    <div class="row mb-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <label>Estado</label>
                            <select
                                v-model="paramsOrdenesVenta.estado_orden"
                                class="form-select"
                                @change="cargarOrdenVentas"
                            >
                                <option
                                    v-for="item in listEstados"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label>Fecha de Orden</label>
                            <input
                                type="date"
                                class="form-control"
                                @keyup="buscarOrden"
                                v-model="paramsOrdenesVenta.fecha"
                            />
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label>Buscar</label>
                            <input
                                type="text"
                                v-model="paramsOrdenesVenta.search"
                                class="form-control"
                                placeholder="Código"
                                @keyup="buscarOrden"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-4" v-for="item in listOrdenVentas">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Código:</strong>
                        </div>
                        <div class="col-8">{{ item.codigo }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Nombre(s):</strong>
                        </div>
                        <div class="col-8">{{ item.cliente.nombres }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Apellidos(s):</strong>
                        </div>
                        <div class="col-8">{{ item.cliente.apellidos }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Correo:</strong>
                        </div>
                        <div class="col-8">{{ item.cliente.correo }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Celular:</strong>
                        </div>
                        <div class="col-8">{{ item.cliente.cel }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Estado:</strong>
                        </div>
                        <div class="col-8 text-left">
                            <span
                                class="badge"
                                :class="{
                                    'bg-secondary':
                                        item.estado_orden == 'PENDIENTE',
                                    'bg-success':
                                        item.estado_orden == 'CONFIRMADO',
                                    'bg-danger':
                                        item.estado_orden == 'RECHAZADO',
                                }"
                                >{{ item.estado_orden }}</span
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Fecha:</strong>
                        </div>
                        <div class="col-8">{{ item.fecha_orden_t }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button
                            class="btn btn-sm btn-info"
                            @click="abreModalVerificacion(item)"
                        >
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <MiPaginacion
                class="justify-content-center mb-0"
                :totalData="paginacionOrdenVentas.totalData"
                :currentPage="paginacionOrdenVentas.currentPage"
                :perPage="paginacionOrdenVentas.perPage"
                @updatePage="updatePageOrdenVenta"
            />
        </div>
    </div>
    <VerificacionOrdenVenta
        :orden-venta="oOrdenVenta"
        :open_dialog="modalVerificacion"
        @cerrar-dialog="cierreVerificacion"
        @envio-formulario="cargarOrdenVentas"
    ></VerificacionOrdenVenta>
</template>
