<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useOrdenVentas } from "@/composables/orden_ventas/useOrdenVentas";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
import Formulario from "./Formulario.vue";
// import { useMenu } from "@/composables/useMenu";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const props = defineProps({
    orden_venta: {
        type: Object,
        default: null,
    },
});
const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { setOrdenVenta, oOrdenVenta } = useOrdenVentas();
setOrdenVenta(props.orden_venta);
const { axiosDelete } = useAxios();

onMounted(async () => {
});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Ordenes de Venta"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item">
            <Link :href="route('orden_ventas.index')"
                >Ordenes de Venta</Link
            >
        </li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Ordenes de Venta</h1>
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
                                    'orden_ventas.index'
                                )
                            "
                            type="button"
                            class="btn btn-secondary"
                            :href="route('orden_ventas.index')"
                        >
                            <i class="fa fa-arrow-left"></i> Volver
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
                    <Formulario></Formulario>
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>
</template>
