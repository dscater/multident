<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useOrdenVentas } from "@/composables/orden_ventas/useOrdenVentas";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
const { props: props_page } = usePage();
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const { oOrdenVenta, limpiarOrdenVenta } = useOrdenVentas();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oOrdenVenta.value);
const agregarProducto = ref({
    producto_id: "",
    producto: null,
    cantidad: 1,
    precio: "",
});

const enviarFormulario = () => {
    // form.sucursal_id =
    //     props_page.auth?.user.sucursals_todo == 0
    //         ? props_page.auth?.user.sucursal_id
    //         : "";

    let url =
        form["_method"] == "POST"
            ? route("orden_ventas.store")
            : route("orden_ventas.update", form.id);

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

const agregarProductoTabla = async () => {
    if (validaAgregarProducto() && !verificaExistente()) {
        const producto = getProductoList(agregarProducto.value.producto_id);
        if (producto) {
            // todo validar precio minimo

            //to do verificar promocion

            //to do: calcular precio total

            form.detalle_ordens.push({
                id: 0,
                producto_id: agregarProducto.value.producto_id,
                producto: producto,
                cantidad: agregarProducto.value.cantidad,
                precio: agregarProducto.value.precio,
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
    const id = form.detalle_ordens[index].id;
    if (id != 0) {
        form.eliminados.push(id);
    }
    form.detalle_ordens.splice(index, 1);
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
        agregarProducto.value.precio &&
        parseFloat(agregarProducto.value.precio) > 0
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
    const existe = form.detalle_ordens.filter(
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

const asignarNitCi = () => {
    form.nit_ci = "";
    if (form.cliente_id) {
        const registro = listClientes.value.filter(
            (elem) => elem.id === form.cliente_id
        );
        if (registro.length > 0) {
            form.nit_ci = registro[0].ci;
        }
    }
};

const listSucursals = ref([]);
const listClientes = ref([]);
const listProductos = ref([]);

const cargarSucursals = async () => {
    axios.get(route("sucursals.listado")).then((response) => {
        listSucursals.value = response.data.sucursals;
    });
};

const cargarClientes = async () => {
    axios.get(route("clientes.listado")).then((response) => {
        listClientes.value = response.data.clientes;
    });
};

const cargarListas = () => {
    cargarSucursals();
    cargarClientes();
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-2 overflow-auto">
                            <div class="col-12">
                                <h4 class="w-100 text-center">
                                    Productos agregados
                                </h4>
                            </div>
                            <div class="col-12 mt-2 mb-0">
                                <table class="table table-bordered">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                PRODUCTO
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                CANTIDAD
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 200px"
                                            >
                                                P/U
                                            </th>
                                            <th
                                                class="text-white"
                                                style="min-width: 120px"
                                            >
                                                SUBTOTAL
                                            </th>
                                            <th class="text-white"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template
                                            v-if="
                                                form.detalle_ordens.length > 0
                                            "
                                        >
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in form.detalle_ordens"
                                            >
                                                <td>{{ index + 1 }}</td>
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
                                                <td></td>
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
                                    v-if="form.errors?.detalle_ordens"
                                    class="parsley-errors-list filled mt-0"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.detalle_ordens }}
                                    </li>
                                </ul>
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

                            <div class="col-12">
                                <label>Factura*</label>
                                <select
                                    class="form-control"
                                    v-model="form.factura"
                                >
                                    <option value="NO">NO</option>
                                    <option value="SI">SI</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label>Tipo de Pago*</label>
                                <select
                                    class="form-control"
                                    v-model="form.tipo_pago"
                                >
                                    <option value="EFECTIVO">EFECTIVO</option>
                                    <option value="QR">QR</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Seleccionar Cliente*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'border border-red rounded':
                                            form.errors?.cliente_id,
                                    }"
                                    clearable
                                    placeholder="- Seleccione -"
                                    popper-class="custom-header"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="form.cliente_id"
                                    @change="asignarNitCi"
                                >
                                    <el-option
                                        v-for="item in listClientes"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="`${item.full_name} - ${item.ci}`"
                                    />
                                </el-select>
                                <ul
                                    v-if="form.errors?.cliente_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.cliente_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 form-group mt-2">
                                <label>NIT/C.I.:</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="form.nit_ci"
                                />
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <button
                                    type="button"
                                    class="btn btn-primary w-100"
                                    @click="enviarFormulario"
                                >
                                    <template v-if="oOrdenVenta.id == 0">
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar producto* </label>
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
                                <div
                                    class="row mt-1"
                                    v-if="agregarProducto.producto_id != ''"
                                >
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <button
                                                        class="btn btn-primary btn-small"
                                                        title="Relación"
                                                    >
                                                        <i
                                                            class="fa fa-random"
                                                        ></i>
                                                        Prods. relacionados
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                <label>Precio*</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    placeholder="Precio"
                                    v-model="agregarProducto.precio"
                                />
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
                <div class="col-12 mt-2">
                    <button class="btn btn-warning w-100">
                        <i class="fa fa-search"></i> Buscar productos por
                        sucursal
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>
