<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Ordenes de Venta",
        disabled: false,
        url: "",
        name_url: "",
    },
];
</script>

<script setup>
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { useAxios } from "@/composables/axios/useAxios";

const { auth } = usePage().props;
const user = ref(auth.user);
const { setLoading } = useApp();
const { axiosGet } = useAxios();
const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    formato: "pdf",
    producto_id: "todos",
    sucursal_id:
        auth?.user.sucursals_todo == 0 ? user.value.sucursal_id : "todos",
    factura: "todos",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listFormato = ref([
    { value: "pdf", label: "PDF" },
    { value: "excel", label: "EXCEL" },
]);

const listSucursals = ref([]);
const listProductos = ref([]);
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

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_orden_ventas", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};

onMounted(() => {
    cargarListas();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Reporte Ordenes de Venta"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Ordenes de Venta</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Ordenes de Venta</h1>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Fecha inicio*</label>
                                        <input
                                            type="date"
                                            v-model="form.fecha_ini"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Fecha final*</label>
                                        <input
                                            type="date"
                                            v-model="form.fecha_fin"
                                            class="form-control"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label>Seleccionar formato*</label>
                                <select
                                    :hide-details="
                                        form.errors?.formato ? false : true
                                    "
                                    :error="form.errors?.formato ? true : false"
                                    :error-messages="
                                        form.errors?.formato
                                            ? form.errors?.formato
                                            : ''
                                    "
                                    v-model="form.formato"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listFormato"
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
                                    @click="generarReporte"
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
</template>
