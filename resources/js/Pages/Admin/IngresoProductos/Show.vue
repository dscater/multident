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
const props = defineProps({
    ingreso_producto: {
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

const { setIngresoProducto, oIngresoProducto } = useIngresoProductos();
setIngresoProducto(props.ingreso_producto, true);
const { axiosDelete } = useAxios();

onMounted(async () => {});
onBeforeUnmount(() => {});
</script>
<template>
    <Head title="Ingreso de Productos"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item">
            <Link :href="route('ingreso_productos.index')"
                >Ingreso de Productos</Link
            >
        </li>
        <li class="breadcrumb-item active">Ver</li>
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
                                    'ingreso_productos.index'
                                )
                            "
                            type="button"
                            class="btn btn-secondary"
                            :href="route('ingreso_productos.index')"
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
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mt-2 overflow-auto">
                                        <div class="col-12">
                                            <h4 class="w-100 text-center">
                                                Productos agregados
                                            </h4>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="font-weight-bold">Sucursal:</label>
                                            {{
                                                oIngresoProducto.sucursal.nombre
                                            }}
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="font-weight-bold">Fecha de Registro:</label>
                                            {{
                                                oIngresoProducto.fecha_registro_t
                                            }}
                                        </div>
                                        <div class="col-12 mt-2 mb-0">
                                            <table class="table table-bordered">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 120px;
                                                            "
                                                        >
                                                            Producto
                                                        </th>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 120px;
                                                            "
                                                        >
                                                            Cantidad
                                                        </th>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 200px;
                                                            "
                                                        >
                                                            Ubicación
                                                        </th>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 200px;
                                                            "
                                                        >
                                                            Fila
                                                        </th>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 120px;
                                                            "
                                                        >
                                                            Fecha Vencimiento
                                                        </th>
                                                        <th
                                                            class="text-white"
                                                            style="
                                                                min-width: 120px;
                                                            "
                                                        >
                                                            Descripción
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template
                                                        v-if="
                                                            oIngresoProducto
                                                                .ingreso_detalles
                                                                .length > 0
                                                        "
                                                    >
                                                        <tr
                                                            v-for="(
                                                                item, index
                                                            ) in oIngresoProducto.ingreso_detalles"
                                                        >
                                                            <td>
                                                                {{
                                                                    item
                                                                        .producto
                                                                        .nombre
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                    item.cantidad
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                    item
                                                                        .ubicacion_producto
                                                                        .lugar
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{ item.fila }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                    item.fecha_vencimiento_t
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                    item.descripcion
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <tr v-else>
                                                        <td
                                                            colspan="4"
                                                            class="text-center"
                                                        >
                                                            <strong
                                                                >No se agrego
                                                                ningún
                                                                producto</strong
                                                            >
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>
</template>
