<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useProductos } from "@/composables/productos/useProductos";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import MiDropZone from "@/Components/MiDropZone.vue";
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    accion_dialog: {
        type: Number,
        default: 0,
    },
});

const { oProducto, limpiarProducto } = useProductos();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oProducto.value);
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            cargarListas();
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oProducto.value);
        }
    }
);
watch(
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const { flash } = usePage().props;

const listPublico = ["HABILITADO", "DESHABILITADO"];
const listCategorias = ref([]);
const listSucursals = ref([]);

const detectaArchivos = (files) => {
    form.imagens = files;
};

const detectaEliminados = (eliminados) => {
    form.eliminados_imagens = eliminados;
};

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nueva Categoría`
        : `<i class="fa fa-edit"></i> Editar Categoría`;
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("productos.store")
            : route("productos.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            dialog.value = false;
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            limpiarProducto();
            emits("envio-formulario");
        },
        onError: (err) => {
            console.log("ERROR");
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const cerrarDialog = () => {
    dialog.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const cargarListas = () => {
    cargarCategorias();
};

const cargarCategorias = async () => {
    const data = await axiosGet(route("categorias.listado"));
    listCategorias.value = data.categorias;
};

onMounted(() => {
    cargarListas();
});
</script>

<template>
    <div
        class="modal fade"
        :class="{
            show: dialog,
        }"
        id="modal-dialog-form"
        :style="{
            display: dialog ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="enviarFormulario()">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nombre del producto*</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.nombre,
                                    }"
                                    v-model="form.nombre"
                                />
                                <ul
                                    v-if="form.errors?.nombre"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.nombre }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <label>Descripción del producto*</label>
                                <el-input
                                    type="textarea"
                                    :class="{
                                        'is-invalid': form.errors?.descripcion,
                                    }"
                                    v-model="form.descripcion"
                                    autosize
                                >
                                </el-input>
                                <ul
                                    v-if="form.errors?.descripcion"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.descripcion }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Stock actual*</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.stock_actual,
                                    }"
                                    v-model="form.stock_actual"
                                />
                                <ul
                                    v-if="form.errors?.stock_actual"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.stock_actual }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Precio Compra*</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.precio_compra,
                                    }"
                                    v-model="form.precio_compra"
                                />
                                <ul
                                    v-if="form.errors?.precio_compra"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.precio_compra }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Precio Venta*</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.precio_venta,
                                    }"
                                    v-model="form.precio_venta"
                                />
                                <ul
                                    v-if="form.errors?.precio_venta"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.precio_venta }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Observaciones</label>
                                <el-input
                                    type="textarea"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.observaciones,
                                    }"
                                    v-model="form.observaciones"
                                    autosize
                                >
                                </el-input>
                                <ul
                                    v-if="form.errors?.observaciones"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.observaciones }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Público*</label>
                                <el-select
                                    :class="{
                                        'parsley-error': form.errors?.publico,
                                    }"
                                    v-model="form.publico"
                                    placeholder="Público"
                                    no-data-text="Sin datos"
                                >
                                    <el-option
                                        v-for="item in listPublico"
                                        :value="item"
                                        :label="item"
                                    ></el-option>
                                </el-select>
                                <ul
                                    v-if="form.errors?.publico"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.publico }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <label>Seleccionar categoría*</label>
                                <el-select
                                    :class="{
                                        'parsley-error':
                                            form.errors?.categoria_id,
                                    }"
                                    v-model="form.categoria_id"
                                    placeholder="Categoría"
                                    no-data-text="Sin datos"
                                >
                                    <el-option
                                        v-for="item in listCategorias"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                                <ul
                                    v-if="form.errors?.categoria_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.categoria_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="label">Cargar imagenes</label>
                                <div class="text-muted">
                                    Selecciona al menos una imagen
                                </div>
                                <MiDropZone
                                    :files="form.imagens"
                                    @UpdateFiles="detectaArchivos"
                                    @addEliminados="detectaEliminados"
                                ></MiDropZone>
                                <ul
                                    v-if="form.errors?.imagens"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.imagens }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-white"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                    <button
                        type="button"
                        @click="enviarFormulario()"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
