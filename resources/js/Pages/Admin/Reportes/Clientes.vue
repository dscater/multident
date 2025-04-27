<script setup>
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

const { auth } = usePage().props;
const user = ref(auth.user);
const { setLoading } = useApp();

const listFormatos = ref([
    { value: "pdf", label: "PDF" },
    { value: "excel", label: "EXCEL" },
]);

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const form = ref({
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
    formato: "pdf",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_clientes", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};
</script>
<template>
    <Head title="Reporte Clientes"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Clientes</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Clientes</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Fecha Inicio</label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :class="{
                                                'parsley-error':
                                                    form.errors?.fecha_ini,
                                            }"
                                            v-model="form.fecha_ini"
                                        />
                                        <ul
                                            v-if="form.errors?.fecha_ini"
                                            class="parsley-errors-list filled"
                                        >
                                            <li class="parsley-required">
                                                {{ form.errors?.fecha_ini }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Fecha Fin</label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :class="{
                                                'parsley-error':
                                                    form.errors?.fecha_fin,
                                            }"
                                            v-model="form.fecha_fin"
                                        />
                                        <ul
                                            v-if="form.errors?.fecha_fin"
                                            class="parsley-errors-list filled"
                                        >
                                            <li class="parsley-required">
                                                {{ form.errors?.fecha_fin }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
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
                                        v-for="item in listFormatos"
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
