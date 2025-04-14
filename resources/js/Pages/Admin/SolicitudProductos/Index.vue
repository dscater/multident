<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useCategorias } from "@/composables/categorias/useCategorias";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import MiPaginacion from "@/Components/MiPaginacion.vue";
import VerificacionSolicitudProducto from "@/Components/VerificacionSolicitudProducto.vue";
import SeguimientoSolicitudProducto from "@/Components/SeguimientoSolicitudProducto.vue";
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
const modalSeguimiento = ref(false);
const oSolicitudProducto = ref(null);
const paramsSolicitudProductos = ref({
    perPage: 12,
    page: 1,
    search: props.codigo,
    estado_solicitud: "PENDIENTE",
    estado_seguimiento: "",
    fecha: "",
    orderByCol: "id",
    desc: "desc",
});
const paginacionSolicitudProductos = ref({
    totalData: 0,
    perPage: paramsSolicitudProductos.value.perPage,
    currentPage: paramsSolicitudProductos.value.page,
    lastPage: 0,
});

const listEstadosSolicitud = ref([
    { value: "", label: "Todos" },
    { value: "PENDIENTE", label: "Pendientes" },
    { value: "RECHAZADO", label: "Rechazados" },
    { value: "APROBADO", label: "Aprobados" },
]);

const listEstadosSeguimiento = ref([
    { value: "", label: "Todos" },
    { value: "PENDIENTE", label: "Pendientes" },
    { value: "EN PROCESO", label: "En proceso" },
    { value: "EN ALMACÉN", label: "En almacén" },
    { value: "ENTREGADO", label: "Entregado" },
]);

const listSolicitudProductos = ref([]);

const cargarSolicitudProductos = async () => {
    modalVerificacion.value = false;
    modalSeguimiento.value = false;
    const data = await axiosGet(
        route("solicitud_productos.paginado"),
        paramsSolicitudProductos.value
    );
    listSolicitudProductos.value = data.solicitudProductos;
    paginacionSolicitudProductos.value.totalData = data.total;
    paginacionSolicitudProductos.value.currentPage =
        paramsSolicitudProductos.value.page;
    paginacionSolicitudProductos.value.lastPage = data.lastPage;
};
const updatePageSolicitudProducto = (value) => {
    paramsSolicitudProductos.value.page = value;
    if (paramsSolicitudProductos.value.page < 0)
        paramsSolicitudProductos.value.page = 1;
    if (
        paramsSolicitudProductos.value.page >
        paginacionSolicitudProductos.value.totalData
    )
        paramsSolicitudProductos.value.page =
            paginacionSolicitudProductos.value.lastPage;
    cargarSolicitudProductos();
};

const intervalSearch = ref(null);
const buscarSolicitud = () => {
    clearInterval(intervalSearch.value);
    intervalSearch.value = setTimeout(async () => {
        await cargarSolicitudProductos();
    }, 400);
};

const abreModalVerificacion = (item) => {
    oSolicitudProducto.value = item;
    modalVerificacion.value = true;
};
const abreModalSeguimiento = (item) => {
    oSolicitudProducto.value = item;
    modalSeguimiento.value = true;
};
const cierreVerificacion = () => {
    oSolicitudProducto.value = null;
    modalVerificacion.value = false;
};
const cierreSeguimiento = () => {
    oSolicitudProducto.value = null;
    modalSeguimiento.value = false;
};
onMounted(async () => {
    cargarSolicitudProductos();
});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Solicitud de Productos"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Solicitud de Productos</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Solicitud de Productos</h1>
    <!-- END page-header -->

    <div class="row mb-1">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <label>Estado Solicitud</label>
                            <select
                                v-model="
                                    paramsSolicitudProductos.estado_solicitud
                                "
                                class="form-select"
                                @change="cargarSolicitudProductos"
                            >
                                <option
                                    v-for="item in listEstadosSolicitud"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label>Estado Seguimiento</label>
                            <select
                                v-model="
                                    paramsSolicitudProductos.estado_seguimiento
                                "
                                class="form-select"
                                @change="cargarSolicitudProductos"
                            >
                                <option
                                    v-for="item in listEstadosSeguimiento"
                                    :value="item.value"
                                >
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label>Fecha de Solicitud</label>
                            <input
                                type="date"
                                class="form-control"
                                @keyup="buscarSolicitud"
                                v-model="paramsSolicitudProductos.fecha"
                            />
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label>Buscar</label>
                            <input
                                type="text"
                                v-model="paramsSolicitudProductos.search"
                                class="form-control"
                                placeholder="Código"
                                @keyup="buscarSolicitud"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-4" v-for="item in listSolicitudProductos">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Código:</strong>
                        </div>
                        <div class="col-8">{{ item.codigo_solicitud }}</div>
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
                            <strong>Dpto.:</strong>
                        </div>
                        <div class="col-8">{{ item.sede.nombre }}</div>
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
                                        item.estado_solicitud == 'PENDIENTE',
                                    'bg-success':
                                        item.estado_solicitud == 'APROBADO',
                                    'bg-danger':
                                        item.estado_solicitud == 'RECHAZADO',
                                }"
                                >{{ item.estado_solicitud }}</span
                            >
                        </div>
                    </div>
                    <div class="row my-1">
                        <div class="col-4 text-right">
                            <strong>Seguimiento:</strong>
                        </div>
                        <div class="col-8 text-left">
                            <span
                                class="badge"
                                :class="{
                                    'bg-secondary':
                                        item.estado_seguimiento ==
                                            'PENDIENTE' ||
                                        !item.estado_seguimiento,
                                    'bg-primary':
                                        item.estado_seguimiento == 'EN PROCESO',
                                    'bg-info':
                                        item.estado_seguimiento == 'EN ALMACÉN',
                                    'bg-success':
                                        item.estado_seguimiento == 'ENTREGADO',
                                }"
                                >{{
                                    item.estado_seguimiento ?? "PENDIENTE"
                                }}</span
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-right">
                            <strong>Fecha:</strong>
                        </div>
                        <div class="col-8">{{ item.fecha_solicitud_t }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button
                            class="btn btn-sm btn-success"
                            v-if="item.estado_solicitud == 'APROBADO'"
                            @click="abreModalSeguimiento(item)"
                        >
                            <i class="fa fa-clipboard-check"></i>
                        </button>
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
                :totalData="paginacionSolicitudProductos.totalData"
                :currentPage="paginacionSolicitudProductos.currentPage"
                :perPage="paginacionSolicitudProductos.perPage"
                @updatePage="updatePageSolicitudProducto"
            />
        </div>
    </div>
    <VerificacionSolicitudProducto
        :solicitud-producto="oSolicitudProducto"
        :open_dialog="modalVerificacion"
        @cerrar-dialog="cierreVerificacion"
        @envio-formulario="cargarSolicitudProductos"
    ></VerificacionSolicitudProducto>

    <SeguimientoSolicitudProducto
        :solicitud-producto="oSolicitudProducto"
        :open_dialog="modalSeguimiento"
        @cerrar-dialog="cierreSeguimiento"
        @envio-formulario="cargarSolicitudProductos"
    ></SeguimientoSolicitudProducto>
</template>
