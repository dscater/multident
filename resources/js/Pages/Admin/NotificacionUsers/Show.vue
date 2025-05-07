<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { usePromocions } from "@/composables/promocions/usePromocions";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
// import { useMenu } from "@/composables/useMenu";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const props = defineProps({
    notificacion_user: {
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

onMounted(async () => {});
onBeforeUnmount(() => {});
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
                <!-- BEGIN panel-heading -->
                <!-- END panel-heading -->
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <p>
                        <strong>Descripción: </strong>
                        {{ props.notificacion_user.notificacion.descripcion }}
                    </p>
                    <p
                        v-if="
                            props.notificacion_user.notificacion.ingreso_detalle
                            && props.notificacion_user.notificacion.modulo == 'IngresoDetalle'
                        "
                    >
                        <strong>Ubicación: </strong>
                        {{
                            props.notificacion_user.notificacion.ingreso_detalle
                                .ubicacion_producto?.lugar
                        }}
                        -
                        {{
                            props.notificacion_user.notificacion.ingreso_detalle
                                .fila
                        }}
                    </p>
                    <p>
                        <strong>Fecha: </strong>
                        {{ props.notificacion_user.notificacion.fecha_t }}
                    </p>
                    <p>
                        <strong>Hora: </strong>
                        {{ props.notificacion_user.notificacion.hora }}
                    </p>
                    <Link :href="route('notificacions.index')">Volver</Link>
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>
</template>
