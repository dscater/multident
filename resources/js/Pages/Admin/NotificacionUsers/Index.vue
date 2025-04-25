<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
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

const { axiosDelete } = useAxios();

const columns = [
    {
        title: "NRO.",
        data: "id",
    },
    {
        title: "DESCRIPCIÓN",
        data: "notificacion.descripcion",
    },
    {
        title: "FECHA Y HORA",
        data: "notificacion.fecha_hora_t",
    },
    {
        title: "ACCIONES",
        data: null,
        render: function (data, type, row) {
            let buttons = ``;

            if (
                props_page.auth?.user.permisos == "*" ||
                props_page.auth?.user.permisos.includes("notificacions.edit")
            ) {
                buttons += `<button class="mx-0 rounded-0 btn btn-primary ver" data-id="${row.id}"><i class="fa fa-eye"></i></button>`;
            }

            return buttons;
        },
    },
];
const loading = ref(false);
const accionesRow = () => {
    // ver
    $("#table-notificacion").on("click", "button.ver", function (e) {
        e.preventDefault();
        let id = $(this).attr("data-id");
        router.get(route("notificacions.show", id));
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
        "#table-notificacion",
        columns,
        route("notificacions.api")
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
    <Head title="Notificaciones"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Notificaciones</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Notificaciones</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN panel -->
            <div class="panel panel-inverse">
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <table
                        id="table-notificacion"
                        width="100%"
                        class="table table-striped table-bordered align-middle text-nowrap tabla_datos"
                    >
                        <thead>
                            <tr>
                                <th width="5%"></th>
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
