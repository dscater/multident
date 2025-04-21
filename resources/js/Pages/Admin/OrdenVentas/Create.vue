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
const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { setOrdenVenta, limpiarOrdenVenta } = useOrdenVentas();
const { axiosDelete } = useAxios();

onMounted(async () => {});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Ordenes de Venta"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item">
            <Link :href="route('orden_ventas.index')">Ordenes de Venta</Link>
        </li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Ordenes de Venta</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
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
            <Formulario></Formulario>
        </div>
    </div>
</template>
