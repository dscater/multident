<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useProformas } from "@/composables/proformas/useProformas";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
import Formulario from "./Formulario.vue";
// import { useMenu } from "@/composables/useMenu";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const props = defineProps({
    proforma: {
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

const { setProforma, oProforma } = useProformas();
setProforma(props.proforma);
const { axiosDelete } = useAxios();

onMounted(async () => {
});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Proformas"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item">
            <Link :href="route('proformas.index')"
                >Proformas</Link
            >
        </li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Proformas</h1>
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
                                    'proformas.index'
                                )
                            "
                            type="button"
                            class="btn btn-secondary"
                            :href="route('proformas.index')"
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
