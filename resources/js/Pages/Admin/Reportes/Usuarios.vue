<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Usuarios",
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

const listRoles = ref([]);
const listFormatos = ref([
    { value: "pdf", label: "PDF" },
    { value: "excel", label: "EXCEL" },
]);
const cargarRoles = async () => {
    const resp = await axiosGet(route("roles.listado"));
    listRoles.value = resp.roles;
    listRoles.value.unshift({ id: "todos", nombre: "TODOS" });
    listRoles.value.push({ id: "externo", nombre: "EXTERNOS" });
};
const form = ref({
    role_id: "todos",
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
    const url = route("reportes.r_usuarios", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};

onMounted(() => {
    cargarRoles();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Reporte Usuarios"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Usuarios</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Usuarios</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Seleccionar role*</label>
                                <select
                                    :hide-details="
                                        form.errors?.role_id ? false : true
                                    "
                                    :error="form.errors?.role_id ? true : false"
                                    :error-messages="
                                        form.errors?.role_id
                                            ? form.errors?.role_id
                                            : ''
                                    "
                                    v-model="form.role_id"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listRoles"
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
