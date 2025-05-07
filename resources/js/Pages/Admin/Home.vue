<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});
import { onMounted, ref, nextTick, computed } from "vue";
import { useApp } from "@/composables/useApp";
// componentes
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import { usePage, Head, Link } from "@inertiajs/vue3";
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

const props_page = defineProps({
    array_infos: {
        type: Array,
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

// GRAFICO 1
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

//GRAFICO 2
const form2 = ref({
    producto_id: "todos",
    sucursal_id:
        auth?.user.sucursals_todo == 0 ? user.value.sucursal_id : "todos",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
    factura: "todos",
});

const generarGrafico2 = async () => {
    generando.value = true;
    axios
        .get(route("reportes.r_g_ingresos_orden_ventas"), {
            params: form2.value,
        })
        .then((response) => {
            nextTick(() => {
                const containerId = `container2`;
                const container = document.getElementById(containerId);
                // Verificar que el contenedor exista y tenga un tamaño válido
                if (container) {
                    renderChart2(
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

const renderChart2 = (containerId, categories, data) => {
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
            text: `INGRESOS POR ORDENES DE VENTAS`,
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
                text: "TOTAL",
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
                <h5><strong>Total: </strong>${this.point.y}</h5>`;
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

const { oConfiguracion } = useConfiguracion();

const { props } = usePage();

onMounted(() => {
    cargarListas();
    generarGrafico();
    generarGrafico2();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Inicio"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <!-- <h1 class="page-header">Panel</h1> -->
    <!-- END page-header -->
    <div class="row">
        <div class="col-12">
            <h4 class="text-center text-h4">
                Bienvenid@ {{ props.auth.user.full_name }}
            </h4>
        </div>
    </div>
    <div class="row">
        <!-- BEGIN col-3 -->
        <div class="col-xl-3 col-md-6" v-for="item in array_infos">
            <div class="widget widget-stats" :class="[item.color]">
                <div class="stats-icon">
                    <i class="fa" :class="[item.icon]"></i>
                </div>
                <div class="stats-info text-white">
                    <h4>{{ item.label }}</h4>
                    <p>{{ item.cantidad }}</p>
                </div>
                <div class="stats-link">
                    <Link :href="route(item.url)"
                        >Ver más <i class="fa fa-arrow-alt-circle-right"></i
                    ></Link>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div
                            class="col-md-4 mb-2"
                            v-if="user.sucursals_todo == 1"
                        >
                            <label>Seleccionar Sucursal*</label>
                            <el-select
                                :class="{
                                    'parsley-error': form.errors?.sucursal_id,
                                }"
                                v-model="form.sucursal_id"
                                filterable
                                @change="generarGrafico"
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
                        <div class="col-md-4 mb-2">
                            <label>Seleccionar producto*</label>
                            <el-select
                                :hide-details="
                                    form.errors?.producto_id ? false : true
                                "
                                :error="form.errors?.producto_id ? true : false"
                                :error-messages="
                                    form.errors?.producto_id
                                        ? form.errors?.producto_id
                                        : ''
                                "
                                v-model="form.producto_id"
                                class="w-100"
                                filterable
                                @change="generarGrafico"
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
                        <div class="col-md-4 mb-2">
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
                                @change="generarGrafico"
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
                        <div class="col-md-6">
                            <label>Rango de fechas</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input
                                        type="date"
                                        class="form-control"
                                        v-model="form.fecha_ini"
                                        @keyup="generarGrafico"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <input
                                        type="date"
                                        class="form-control"
                                        v-model="form.fecha_fin"
                                        @keyup="generarGrafico"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row overflow-auto" style="max-height: 600px">
                        <div class="col-12 mt-3" id="container"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div
                            class="col-md-4 mb-2"
                            v-if="user.sucursals_todo == 1"
                        >
                            <label>Seleccionar Sucursal*</label>
                            <el-select
                                :class="{
                                    'parsley-error': form2.errors?.sucursal_id,
                                }"
                                v-model="form2.sucursal_id"
                                filterable
                                @change="generarGrafico2"
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
                        <div class="col-md-4 mb-2">
                            <label>Seleccionar producto*</label>
                            <el-select
                                :hide-details="
                                    form2.errors?.producto_id ? false : true
                                "
                                :error="
                                    form2.errors?.producto_id ? true : false
                                "
                                :error-messages="
                                    form2.errors?.producto_id
                                        ? form2.errors?.producto_id
                                        : ''
                                "
                                v-model="form2.producto_id"
                                class="w-100"
                                filterable
                                @change="generarGrafico2"
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
                        <div class="col-md-4 mb-2">
                            <label>Con Factura</label>
                            <el-select
                                :hide-details="
                                    form2.errors?.factura ? false : true
                                "
                                :error="form2.errors?.factura ? true : false"
                                :error-messages="
                                    form2.errors?.factura
                                        ? form2.errors?.factura
                                        : ''
                                "
                                v-model="form2.factura"
                                class="w-100"
                                filterable
                                @change="generarGrafico2"
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
                        <div class="col-md-6">
                            <label>Rango de fechas</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input
                                        type="date"
                                        class="form-control"
                                        v-model="form2.fecha_ini"
                                        @keyup="generarGrafico2"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <input
                                        type="date"
                                        class="form-control"
                                        v-model="form2.fecha_fin"
                                        @keyup="generarGrafico2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row overflow-auto" style="max-height: 600px">
                        <div class="col-12 mt-3" id="container2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.item_btn {
    margin: 10px;
}

.contenido_item i {
    color: black;
}

.contenido_item {
    transition: all 0.8s;
    color: black;
    padding: 10px;
    cursor: pointer;
    background-color: rgb(248, 229, 229);
    border: solid 2px rgb(243, 211, 211);
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    font-size: 1.3em;
    flex-direction: column;
}
</style>
