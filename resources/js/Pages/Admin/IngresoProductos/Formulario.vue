<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useIngresoProductos } from "@/composables/ingreso_productos/useIngresoProductos";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
const { props: props_page } = usePage();
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const { oIngresoProducto, limpiarIngresoProducto } = useIngresoProductos();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oIngresoProducto.value);
const agregarProducto = ref({
    producto_id: "",
    producto: null,
    cantidad: 1,
    ubicacion_producto_id: "",
    fila: "",
    listFilas: [],
    fecha_vencimiento: "",
    descripcion: "",
});

const enviarFormulario = () => {
    // form.sucursal_id =
    //     props_page.auth?.user.sucursals_todo == 0
    //         ? props_page.auth?.user.sucursal_id
    //         : "";

    let url =
        form["_method"] == "POST"
            ? route("ingreso_productos.store")
            : route("ingreso_productos.update", form.id);

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
            limpiarIngresoProducto();
            emits("envio-formulario");
        },
        onError: (err) => {
            console.log("ERROR");
            const flash = usePage().props.flash;
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.bien ? flash.bien : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["envio-formulario"]);

const agregarProductoTabla = () => {
    if (validaAgregarProducto() && !verificaExistente()) {
        const producto = getProductoList(agregarProducto.value.producto_id);
        if (producto) {
            form.ingreso_detalles.push({
                id: 0,
                producto_id: agregarProducto.value.producto_id,
                producto: producto,
                cantidad: agregarProducto.value.cantidad,
                ubicacion_producto_id:
                    agregarProducto.value.ubicacion_producto_id,
                fila: agregarProducto.value.fila,
                listFilas: agregarProducto.value.listFilas,
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
    const id = form.ingreso_detalles[index].id;
    if (id != 0) {
        form.eliminados.push(id);
    }
    form.ingreso_detalles.splice(index, 1);
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
        agregarProducto.value.ubicacion_producto_id &&
        agregarProducto.value.fila
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
    const existe = form.ingreso_detalles.filter(
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
const listUbicacionProductos = ref([]);
const listFilas = ref([]);

const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
    });
};

const cargarUbicacionProductos = async () => {
    axios.get(route("ubicacion_productos.listado")).then((response) => {
        listUbicacionProductos.value = response.data.ubicacion_productos;
    });
};

const cargarListas = () => {
    cargarSucursals();
    cargarProductos();
    cargarUbicacionProductos();
};

const cargarProductos = async () => {
    const data = await axiosGet(route("productos.listado"));
    listProductos.value = data.productos;
};

const generarFilas = () => {
    listFilas.value = [];
    if (agregarProducto.value.ubicacion_producto_id) {
        const ubicacion_producto = listUbicacionProductos.value.filter(
            (elem) => elem.id === agregarProducto.value.ubicacion_producto_id
        )[0];
        for (let i = 1; i <= ubicacion_producto.numero_filas; i++) {
            listFilas.value.push({
                value: i,
                label: i,
            });
        }
        agregarProducto.value.listFilas = listFilas.value;
    }
};

const generarFilasDetalle = (index) => {
    form.ingreso_detalles[index].listFilas = [];
    form.ingreso_detalles[index].fila = "";
    if (form.ingreso_detalles[index].ubicacion_producto_id) {
        const ubicacion_producto = listUbicacionProductos.value.filter(
            (elem) =>
                elem.id === form.ingreso_detalles[index].ubicacion_producto_id
        )[0];
        let nuevasFilas = [];
        for (let i = 1; i <= ubicacion_producto.numero_filas; i++) {
            nuevasFilas.push({
                value: i,
                label: i,
            });
        }
        form.ingreso_detalles[index].listFilas = nuevasFilas;
    }
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
            <div class="col-12">
                <div class="card text-dark bg-light">
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
                                    v-model="agregarProducto.producto_id"
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    />
                                </el-select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Cantidad*</label>
                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    class="form-control"
                                    placeholder="Cantidad*"
                                    v-model="agregarProducto.cantidad"
                                />
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Ubicación*</label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="Ubicación del producto*"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="
                                        agregarProducto.ubicacion_producto_id
                                    "
                                    @change="generarFilas"
                                >
                                    <el-option
                                        v-for="item in listUbicacionProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="`${item.lugar}`"
                                    />
                                </el-select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar Fila*</label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="Seleccionar Fila*"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="agregarProducto.fila"
                                >
                                    <el-option
                                        v-for="item in listFilas"
                                        :key="item.value"
                                        :value="item.value"
                                        :label="`${item.label}`"
                                    />
                                </el-select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Fecha de vencimiento</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    placeholder="Fecha de vencimiento"
                                    v-model="agregarProducto.fecha_vencimiento"
                                />
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Descripción</label>
                                <el-input
                                    type="textarea"
                                    v-model="agregarProducto.descripcion"
                                    placeholder="Descripción"
                                    autosize
                                ></el-input>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 mx-auto">
                                <button
                                    class="btn btn-sm btn-outline-primary w-100"
                                    @click.prevent="agregarProductoTabla"
                                >
                                    <i class="fa fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-2 overflow-auto">
                            <div class="col-12">
                                <h4 class="w-100 text-center">
                                    Productos agregados
                                </h4>
                            </div>
                            <div
                                class="col-md-12 form-group"
                                v-if="props_page.auth?.user.sucursals_todo == 1"
                            >
                                <label>Seleccionar Sucursal*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'border border-red rounded':
                                            form.errors?.sucursal_id,
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
                            <div class="col-12 mt-2 mb-0">
                                <table class="table table-bordered">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                Producto
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                Cantidad
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 200px"
                                            >
                                                Ubicación
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 200px"
                                            >
                                                Fila
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                Fecha Vencimiento
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                Descripción
                                            </th>
                                            <th class="text-white"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template
                                            v-if="
                                                form.ingreso_detalles.length > 0
                                            "
                                        >
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in form.ingreso_detalles"
                                            >
                                                <td>
                                                    {{ item.producto.nombre }}
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        class="form-control"
                                                        placeholder="Cantidad"
                                                        v-model="item.cantidad"
                                                    />
                                                </td>
                                                <td>
                                                    <el-select
                                                        class="w-100"
                                                        clearable
                                                        placeholder="- Seleccionar Ubicación -"
                                                        no-data-text="Sin datos"
                                                        filterable
                                                        v-model="
                                                            item.ubicacion_producto_id
                                                        "
                                                        @change="
                                                            generarFilasDetalle(
                                                                index
                                                            )
                                                        "
                                                    >
                                                        <el-option
                                                            v-for="item in listUbicacionProductos"
                                                            :key="item.id"
                                                            :value="item.id"
                                                            :label="`${item.lugar}`"
                                                        />
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <el-select
                                                        class="w-100"
                                                        clearable
                                                        placeholder="- Seleccionar Fila -"
                                                        no-data-text="Sin datos"
                                                        filterable
                                                        v-model="item.fila"
                                                    >
                                                        <el-option
                                                            v-for="item_fila in item.listFilas"
                                                            :key="
                                                                item_fila.value
                                                            "
                                                            :value="
                                                                item_fila.value
                                                            "
                                                            :label="`${item_fila.label}`"
                                                        />
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <input
                                                        type="date"
                                                        class="form-control"
                                                        v-model="
                                                            item.fecha_vencimiento
                                                        "
                                                    />
                                                </td>
                                                <td>
                                                    <el-input
                                                        type="textarea"
                                                        v-model="
                                                            item.descripcion
                                                        "
                                                        autosize
                                                    ></el-input>
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        @click.prevent="
                                                            eliminarDetalle(
                                                                index
                                                            )
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-trash"
                                                        ></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <td colspan="5" class="text-center">
                                                <strong
                                                    >No se agrego ningún
                                                    producto</strong
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <ul
                                    v-if="form.errors?.ingreso_detalles"
                                    class="parsley-errors-list filled mt-0"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.ingreso_detalles }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100"
                                    @click="enviarFormulario"
                                >
                                    <template v-if="oIngresoProducto.id == 0">
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
