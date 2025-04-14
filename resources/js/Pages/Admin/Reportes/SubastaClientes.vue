<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Clientes por Subasta",
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

const { setLoading } = useApp();

const cargarListas = () => {};

onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes

    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    formato: "pdf",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
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

const listCategorias = ref([
    { value: "todos", label: "TODOS" },
    { value: "VEHÍCULOS", label: "VEHÍCULOS" },
    { value: "OTROS BIENES", label: "OTROS BIENES" },
    { value: "ECOLÓGICO", label: "ECOLÓGICO" },
]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_subasta_clientes", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};
</script>
<template>
    <Head title="Reporte Clientes por Subasta"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Clientes por Subasta</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Clientes por Subasta</h1>
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
                            <div class="col-md-12">
                                <label>Seleccionar categoría*</label>
                                <select
                                    v-model="form.categoria"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listCategorias"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Seleccionar formato*</label>
                                <select
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
