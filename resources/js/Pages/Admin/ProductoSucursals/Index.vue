<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
import axios from "axios";
// import { useMenu } from "@/composables/useMenu";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { axiosDelete } = useAxios();

const listSucursals = ref([]);
const sucursal_id = ref("");
const columns = [
    {
        title: "PRODUCTO",
        data: "nombre",
    },
    {
        title: "STOCK ACTUAL",
        data: "stock_actual",
        render: function (data, type, row) {
            return row.stock_actual ?? 0;
        },
    },
];

const cargarSucursals = () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
        listSucursals.value.unshift({
            id: "",
            nombre: "Seleccionar Sucursal",
        });
    });
};

const loading = ref(false);

const accionesRow = () => {};

var datatable = null;
var input_search = null;
var debounceTimeout = null;
const loading_table = ref(false);
const datatableInitialized = ref(false);

const cambiarSucursal = () => {
    if (!sucursal_id.value) {
        sucursal_id.value = "";
    }
    updateDatatable();
};

const updateDatatable = () => {
    datatable.ajax
        .url(
            route("producto_sucursals.api") +
                "?sucursal_id=" +
                sucursal_id.value ?? 0
        )
        .load();
};

onMounted(async () => {
    cargarSucursals();
    datatable = initDataTable(
        "#table-producto_sucursal",
        columns,
        route("producto_sucursals.api") + "?sucursal_id=" + sucursal_id.value ??
            0
    );
    input_search = document.querySelector('input[type="search"]');

    // Agregar un evento 'keyup' al input de búsqueda con debounce
    input_search.addEventListener("keyup", () => {
        loading_table.value = true;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            datatable.search(input_search.value).draw(); // Realiza la búsqueda manualmente
            loading_table.value = false;
        }, 500);
    });

    datatableInitialized.value = true;
});
onBeforeUnmount(() => {
    if (datatable) {
        datatable.clear();
        datatable.destroy(false); // Destruye la instancia del DataTable
        datatable = null;
        datatableInitialized.value = false;
    }
});
</script>
<template>
    <Head title="Stock de Productos"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Stock de Productos</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Stock de Productos</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN panel -->
            <div class="panel panel-inverse">
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <el-select
                        class="w-100 mb-2"
                        clearable
                        placeholder="- Seleccionar Sucursal -"
                        popper-class="custom-header"
                        no-data-text="Sin datos"
                        filterable
                        v-model="sucursal_id"
                        @change="cambiarSucursal"
                    >
                        <el-option
                            v-for="item in listSucursals"
                            :key="item.id"
                            :value="item.id"
                            :label="item.nombre"
                        />
                    </el-select>
                    <table
                        id="table-producto_sucursal"
                        width="100%"
                        class="table table-striped table-bordered align-middle text-nowrap tabla_datos"
                    >
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <div class="loading_table" v-show="loading_table">
                            Cargando...
                        </div>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>
</template>
