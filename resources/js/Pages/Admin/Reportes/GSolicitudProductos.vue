<script setup>
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref, nextTick } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import Highcharts from "highcharts";
import exporting from "highcharts/modules/exporting";
import accessibility from "highcharts/modules/accessibility";
import { useFormater } from "@/composables/useFormater";
const { getFormatoMoneda } = useFormater();
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
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
    estado: "todos",
});

const listPublicacions = ref([]);
const listEstados = ref([
    { value: "todos", label: "TODOS" },
    { value: "PENDIENTE", label: "PENDIENTE" },
    { value: "RECHAZADO", label: "RECHAZADO" },
    { value: "CONFIRMADO", label: "CONFIRMADO" },
]);

const cargarPublicacions = () => {
    // axios.get(route("publicacions.listado")).then((response) => {
    //     listPublicacions.value = response.data.publicacions;
    // });
};

const cargarListas = () => {
    cargarPublicacions();
};

const aPublicacions = ref([]);

const generarGrafico = async () => {
    generando.value = true;
    axios
        .get(route("reportes.r_g_solicitud_productos"), { params: form.value })
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
    Highcharts.chart(containerId, {
        chart: {
            type: "column",
        },
        title: {
            align: "center",
            text: `SOLICITUD DE COMPRA DE PRODUCTOS`,
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
                // console.log(this.point.solicitudProductos);

                let trTbody = ``;
                this.point.solicitudProductos.forEach((elem) => {
                    elem.solicitud_detalles.forEach((elemDetalle) => {
                        trTbody += `<tr>`;
                        trTbody += `<td class="border p-1">${elem.codigo_solicitud}</td>`;
                        trTbody += `<td class="border p-1">${elemDetalle.nombre_producto}</td>`;
                        trTbody += `<td class="border p-1">1</td>`;
                        trTbody += `<td class="border p-1">${elem.cliente.full_name}</td>`;
                        trTbody += `</tr>`;
                    });
                });

                return `<h4 style="font-size:13px" class="w-100 text-center mb-0">${this.x}</h4><br>
                <table class="border">
                    <thead>
                        <tr>
                            <th class="border p-1">Cód.</th>
                            <th class="border p-1">Prod.</th>
                            <th class="border p-1">Cant.</th>
                            <th class="border p-1">Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${trTbody}
                    </tbody>
                </table>`;
            },
        },

        series: [
            {
                name: "Solicitud de productos",
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
    <Head title="Solicitud de compra de productos"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">
            Gráficas > Solicitud de compra de productos
        </li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Gráficas > Solicitud de compra de productos</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
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
                            <div class="col-md-12 mt-2">
                                <label>Estado</label>
                                <select
                                    name=""
                                    id=""
                                    class="form-select"
                                    v-model="form.estado"
                                >
                                    <option
                                        v-for="item in listEstados"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
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
    <div class="row mt-3" id="contenedor">
        <div class="col-12 mt-3" id="container"></div>
    </div>
</template>
