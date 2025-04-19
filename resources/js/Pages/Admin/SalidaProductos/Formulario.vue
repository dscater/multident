<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useSalidaProductos } from "@/composables/salida_productos/useSalidaProductos";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
const { props: props_page } = usePage();
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const { oSalidaProducto, limpiarSalidaProducto } = useSalidaProductos();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oSalidaProducto.value);
const agregarProducto = ref({
    producto_id: "",
    producto: null,
    cantidad: 1,
    ubicacion_producto_id: "",
    fecha_vencimiento: "",
    descripcion: "",
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("salida_productos.store")
            : route("salida_productos.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            dialog.value = false;
            const flash = usePage().props.flash;

            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            emits("envio-formulario");
        },
        onError: (err) => {
            console.log("ERROR");
            const flash = usePage().props.flash;
            console.log(err);
            if (err.error) {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${err.error}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${
                        flash.bien ? flash.bien : "Hay errores en el formulario"
                    }`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            }
        },
    });
};

const emits = defineEmits(["envio-formulario"]);

const agregarProductoTabla = () => {
    if (validaAgregarProducto() && !verificaExistente()) {
        const producto = getProductoList(agregarProducto.value.producto_id);
        if (producto) {
            form.salida_detalles.push({
                id: 0,
                producto_id: agregarProducto.value.producto_id,
                producto: producto,
                cantidad: agregarProducto.value.cantidad,
                ubicacion_producto_id:
                    agregarProducto.value.ubicacion_producto_id,
                fecha_vencimiento:
                    agregarProducto.value.fecha_vencimiento ?? "",
                descripcion: agregarProducto.value.descripcion ?? "",
            });
            limpiarAgregarDetalle();
        } else {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `Ocurrió un error intente nuevamente por favor`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        }
    }
};

const limpiarAgregarDetalle = () => {
    agregarProducto.value.producto_id = "";
    agregarProducto.value.producto = null;
    agregarProducto.value.cantidad = 1;
    agregarProducto.value.ubicacion_producto_id = "";
    agregarProducto.value.fecha_vencimiento = "";
    agregarProducto.value.descripcion = "";
};

const eliminarDetalle = (index) => {
    const id = form.salida_detalles[index].id;
    if (id != 0) {
        form.eliminados.push(id);
    }
    form.salida_detalles.splice(index, 1);
};

const getProductoList = (producto_id) => {
    const producto = listProductos.value.filter(
        (elem) => elem.id === producto_id
    )[0];
    return producto;
};

const validaAgregarProducto = () => {
    if (
        agregarProducto.value.producto_id &&
        agregarProducto.value.cantidad &&
        parseFloat(agregarProducto.value.cantidad) > 0 &&
        agregarProducto.value.ubicacion_producto_id
    ) {
        return true;
    }

    Swal.fire({
        icon: "info",
        title: "Error",
        text: `Debes completar todos los campos con *`,
        confirmButtonColor: "#3085d6",
        confirmButtonText: `Aceptar`,
    });
    return false;
};

const verificaExistente = () => {
    const existe = form.salida_detalles.filter(
        (elem) => elem.producto_id === agregarProducto.value.producto_id
    );
    if (existe.length > 0) {
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `El producto ya fue agregado`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
        return true;
    }
    return false;
};

const listSucursals = ref([]);
const listProductos = ref([]);

const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
    });
};

const cargarListas = () => {
    cargarSucursals();
    cargarProductos();
};

const cargarProductos = async () => {
    const data = await axiosGet(route("productos.listado"));
    listProductos.value = data.productos;
};

onMounted(() => {
    form.sucursal_id =
        props_page.auth?.user.sucursals_todo == 0
            ? props_page.auth?.user.sucursal_id
            : form.sucursal_id;
    cargarListas();
});
</script>

<template>
    <form>
        <div class="row">
            <div
                class="col-md-12 form-group"
                v-if="props_page.auth?.user.sucursals_todo == 1"
            >
                <label>Seleccionar Sucursal*</label>
                <el-select
                    class="w-100"
                    :class="{
                        'border border-red rounded': form.errors?.sucursal_id,
                    }"
                    clearable
                    placeholder="- Seleccione -"
                    popper-class="custom-header"
                    no-data-text="Sin datos"
                    filterable
                    v-model="form.sucursal_id"
                >
                    <el-option
                        v-for="item in listSucursals"
                        :key="item.id"
                        :value="item.id"
                        :label="item.nombre"
                    />
                </el-select>
                <ul
                    v-if="form.errors?.sucursal_id"
                    class="parsley-errors-list filled"
                >
                    <li class="parsley-required">
                        {{ form.errors?.sucursal_id }}
                    </li>
                </ul>
            </div>
            <div class="col-12">
                <h5 class="w-100 text-center">
                    {{ props_page.auth?.user.sucursal?.nombre }}
                </h5>
            </div>
            <div class="col-12">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar producto*</label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="Producto*"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="form.producto_id"
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.producto_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.producto_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Cantidad*</label>
                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    class="form-control"
                                    placeholder="Cantidad*"
                                    v-model="form.cantidad"
                                />
                                <ul
                                    v-if="form.errors?.cantidad"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.cantidad }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Descripción</label>
                                <el-input
                                    type="textarea"
                                    v-model="form.descripcion"
                                    placeholder="Descripción"
                                    autosize
                                ></el-input>
                                <ul
                                    v-if="form.errors?.descripcion"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.descripcion }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100"
                                    @click="enviarFormulario"
                                >
                                    <template v-if="oSalidaProducto.id == 0">
                                        <i class="fa fa-save"></i>
                                        Guardar</template
                                    >
                                    <template v-else>
                                        <i class="fa fa-edit"></i>
                                        Actualizar</template
                                    >
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
