<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Productos",
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

const { setLoading } = useApp();
const { axiosGet } = useAxios();
const cargarListas = () => {};
const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    formato: "pdf",
    categoria: "todos",
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

const listCategorias = ref([]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_productos", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};

const cargarCategorias = async () => {
    const resp = await axiosGet(route("categorias.listado"));
    listCategorias.value = resp.categorias;
    listCategorias.value.unshift({ id: "todos", nombre: "TODOS" });
};

onMounted(() => {
    cargarCategorias();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Reporte Productos"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Productos</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Productos</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Seleccionar categoría*</label>
                                <select
                                    v-model="form.categoria"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listCategorias"
                                        :value="item.id"
                                    >
                                        {{ item.nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
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
