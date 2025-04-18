<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useIngresoProductos } from "@/composables/ingreso_productos/useIngresoProductos";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
// import { useMenu } from "@/composables/useMenu";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { setIngresoProducto, limpiarIngresoProducto } = useIngresoProductos();
const { axiosDelete } = useAxios();

const columns = [
    {
        title: "",
        data: "id",
    },
    {
        title: "SUCURSAL",
        data: "sucursal.nombre",
    },
    {
        title: "TOTAL PRODUCTOS INGRESADOS",
        data: null,
        render: function (data, type, row) {
            return row.ingreso_detalles.length;
        },
    },
    {
        title: "FECHA DE REGISTRO",
        data: "fecha_registro_t",
    },
    {
        title: "ACCIONES",
        data: null,
        render: function (data, type, row) {
            let buttons = ``;

            if (
                props_page.auth?.user.permisos == "*" ||
                props_page.auth?.user.permisos.includes(
                    "ingreso_productos.edit"
                )
            ) {
                buttons += `<button class="mx-0 rounded-0 btn btn-warning editar" data-id="${row.id}"><i class="fa fa-edit"></i></button>`;
            }

            if (
                props_page.auth?.user.permisos == "*" ||
                props_page.auth?.user.permisos.includes(
                    "ingreso_productos.destroy"
                )
            ) {
                buttons += ` <button class="mx-0 rounded-0 btn btn-danger eliminar"
                 data-id="${row.id}"
                 data-nombre="${row.sucursal.nombre} | ${row.fecha_registro_t} | ${row.ingreso_detalles.length}"
                 data-url="${route(
                     "ingreso_productos.destroy",
                     row.id
                 )}"><i class="fa fa-trash"></i></button>`;
            }

            return buttons;
        },
    },
];
const loading = ref(false);

const agregarRegistro = () => {
    limpiarIngresoProducto();
};

const accionesRow = () => {
    // editar
    $("#table-ingreso_producto").on("click", "button.editar", function (e) {
        e.preventDefault();
        let id = $(this).attr("data-id");
        router.get(route("ingreso_productos.edit", id));
    });
    // eliminar
    $("#table-ingreso_producto").on("click", "button.eliminar", function (e) {
        e.preventDefault();
        let nombre = $(this).attr("data-nombre");
        let id = $(this).attr("data-id");
        Swal.fire({
            title: "¿Quierés eliminar este registro?",
            html: `<strong>${nombre}</strong>`,
            showCancelButton: true,
            confirmButtonColor: "#B61431",
            confirmButtonText: "Si, eliminar",
            cancelButtonText: "No, cancelar",
            denyButtonText: `No, cancelar`,
        }).then(async (result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                let respuesta = await axiosDelete(
                    route("ingreso_productos.destroy", id)
                );
                if (respuesta && respuesta.sw) {
                    updateDatatable();
                }
            }
        });
    });
};

var datatable = null;
var input_search = null;
var debounceTimeout = null;
const loading_table = ref(false);
const datatableInitialized = ref(false);
const updateDatatable = () => {
    datatable.ajax.reload();
};

onMounted(async () => {
    datatable = initDataTable(
        "#table-ingreso_producto",
        columns,
        route("ingreso_productos.api")
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
    accionesRow();
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
    <Head title="Ingreso de Productos"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Ingreso de Productos</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Ingreso de Productos</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN panel -->
            <div class="panel panel-inverse">
                <!-- BEGIN panel-heading -->
                <div class="panel-heading">
                    <h4 class="panel-title btn-nuevo">
                        <Link
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'ingreso_productos.create'
                                )
                            "
                            type="button"
                            class="btn btn-primary"
                            :href="route('ingreso_productos.create')"
                        >
                            <i class="fa fa-plus"></i> Nuevo
                        </Link>
                    </h4>
                    <!-- <panel-toolbar
                        :mostrar_loading="loading"
                        @loading="updateDatatable"
                    /> -->
                </div>
                <!-- END panel-heading -->
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <table
                        id="table-ingreso_producto"
                        width="100%"
                        class="table table-striped table-bordered align-middle text-nowrap tabla_datos"
                    >
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th width="5%"></th>
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
