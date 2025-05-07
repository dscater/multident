<script setup>
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref, nextTick } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import Highcharts from "highcharts";
import exporting from "highcharts/modules/exporting";
import accessibility from "highcharts/modules/accessibility";
import { useFormater } from "@/composables/useFormater";
const { getFormatoMoneda } = useFormater();
const { auth } = usePage().props;
const user = ref(auth.user);
exporting(Highcharts);
accessibility(Highcharts);
Highcharts.setOptions({
    lang: {
        downloadPNG: "Descargar PNG",
        downloadJPEG: "Descargar JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        printChart: "Imprimir gráfico",
        contextButtonTitle: "Menú de exportación",
        viewFullscreen: "Pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
    },
});
const { setLoading } = useApp();

const listProductos = ref([]);
const listSucursals = ref([]);
const listFactura = ref([
    { value: "todos", label: "TODOS" },
    { value: "SI", label: "SI" },
    { value: "NO", label: "NO" },
]);

const cargarProductos = async () => {
    axios.get(route("productos.listado")).then((response) => {
        listProductos.value = response.data.productos;
        listProductos.value.unshift({
            id: "todos",
            nombre: "TODOS",
        });
    });
};

const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
        listSucursals.value.unshift({
            id: "todos",
            nombre: "TODOS",
        });
    });
};

const cargarListas = () => {
    cargarProductos();
    cargarSucursals();
};

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Grafico...";
    }
    return "Generar Grafico";
});

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    producto_id: "todos",
    sucursal_id:
        auth?.user.sucursals_todo == 0 ? user.value.sucursal_id : "todos",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
    factura: "todos",
});

const generarGrafico = async () => {
    generando.value = true;
    axios
        .get(route("reportes.r_g_cantidad_orden_ventas"), {
            params: form.value,
        })
        .then((response) => {
            nextTick(() => {
                const containerId = `container`;
                const container = document.getElementById(containerId);
                // Verificar que el contenedor exista y tenga un tamaño válido
                if (container) {
                    renderChart(
                        containerId,
                        response.data.categories,
                        response.data.data
                    );
                } else {
                    console.error(`Contenedor ${containerId} no válido.`);
                }
            });
            // Create the chart
            generando.value = false;
        });
};

const renderChart = (containerId, categories, data) => {
    const rowHeight = 80;
    const minHeight = 200;
    const calculatedHeight = Math.max(minHeight, categories.length * rowHeight);
    Highcharts.chart(containerId, {
        chart: {
            type: "bar",
            height: calculatedHeight,
        },
        title: {
            align: "center",
            text: `CANTIDAD DE ORDENES DE VENTAS`,
        },
        subtitle: {
            align: "center",
            text: ``,
        },
        accessibility: {
            announceNewData: {
                enabled: true,
            },
        },
        xAxis: {
            categories: categories,
        },
        yAxis: {
            title: {
                text: "CANTIDAD",
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    // format: "{point.y}",
                    style: {
                        fontSize: "11px",
                        fontWeight: "bold",
                    },
                    formatter: function () {
                        return parseInt(this.point.y); // Aquí se aplica el formato de moneda
                    },
                },
            },
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                return `<h4 style="font-size:13px" class="w-100 text-center mb-1">${this.x}</h4><br>
                <h5><strong>Cantidad: </strong>${this.point.y}</h5>`;
            },
        },

        series: [
            {
                name: "Ordenes de venta",
                data: data,
                colorByPoint: true,
            },
        ],
    });
};

onMounted(() => {
    cargarListas();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Cantidad de Ordenes de Ventas"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">
            Gráficas > Cantidad de Ordenes de Ventas
        </li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Gráficas > Cantidad de Ordenes de Ventas</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div
                                class="col-12 mb-2"
                                v-if="user.sucursals_todo == 1"
                            >
                                <label>Seleccionar Sucursal*</label>
                                <el-select
                                    :class="{
                                        'parsley-error':
                                            form.errors?.sucursal_id,
                                    }"
                                    v-model="form.sucursal_id"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listSucursals"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Seleccionar producto*</label>
                                <el-select
                                    :hide-details="
                                        form.errors?.producto_id ? false : true
                                    "
                                    :error="
                                        form.errors?.producto_id ? true : false
                                    "
                                    :error-messages="
                                        form.errors?.producto_id
                                            ? form.errors?.producto_id
                                            : ''
                                    "
                                    v-model="form.producto_id"
                                    class="w-100"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label>Con Factura</label>
                                <el-select
                                    :hide-details="
                                        form.errors?.factura ? false : true
                                    "
                                    :error="form.errors?.factura ? true : false"
                                    :error-messages="
                                        form.errors?.factura
                                            ? form.errors?.factura
                                            : ''
                                    "
                                    v-model="form.factura"
                                    class="w-100"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listFactura"
                                        :key="item.value"
                                        :value="item.value"
                                        :label="item.label"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                            <div class="col-12">
                                <label>Rango de fechas</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input
                                            type="date"
                                            class="form-control"
                                            v-model="form.fecha_ini"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <input
                                            type="date"
                                            class="form-control"
                                            v-model="form.fecha_fin"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-3">
                                <button
                                    class="btn btn-primary"
                                    block
                                    @click="generarGrafico"
                                    :disabled="generando"
                                    v-text="txtBtn"
                                ></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row overflow-auto" style="max-height: 600px">
        <div class="col-12 mt-3" id="container"></div>
    </div>
</template>
