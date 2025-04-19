<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useSalidaProductos } from "@/composables/salida_productos/useSalidaProductos";
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

const { setSalidaProducto, limpiarSalidaProducto } = useSalidaProductos();
const { axiosDelete } = useAxios();

onMounted(async () => {});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Salida de Productos"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item">
            <Link :href="route('salida_productos.index')"
                >Salida de Productos</Link
            >
        </li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Salida de Productos</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
            <h4 class="panel-title btn-nuevo">
                <Link
                    v-if="
                        props_page.auth?.user.permisos == '*' ||
                        props_page.auth?.user.permisos.includes(
                            'salida_productos.index'
                        )
                    "
                    type="button"
                    class="btn btn-secondary"
                    :href="route('salida_productos.index')"
                >
                    <i class="fa fa-arrow-left"></i> Volver
                </Link>
            </h4>
            <Formulario></Formulario>
        </div>
    </div>
</template>
