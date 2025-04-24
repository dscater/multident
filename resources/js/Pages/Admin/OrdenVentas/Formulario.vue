<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useOrdenVentas } from "@/composables/orden_ventas/useOrdenVentas";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import Relacion from "./Relacion.vue";
import ProductoSucursal from "./ProductoSucursal.vue";
const { props: props_page } = usePage();
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const { oOrdenVenta, limpiarOrdenVenta } = useOrdenVentas();
const { axiosGet } = useAxios();
let form = useForm(oOrdenVenta.value);
const infoProductoSucursal = ref(null);
const infoPromocion = ref(null);
const agregarProducto = ref({
    producto_id: "",
    producto: null,
    promocion_id: "",
    promocion_descuento: "",
    promocion: null,
    cantidad: 1,
    precio_reg: "",
    precio: "",
    subtotal: 0,
});

const open_dialog_relacion = ref(false);
const accion_dialog_relacion = ref(0);

const open_dialog_producto_sucursal = ref(false);
const accion_dialog_producto_sucursal = ref(0);

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
            const venta_id = usePage().props.venta_id;
            if (venta_id) {
                window.open(
                    route("orden_ventas.generarPdf", venta_id),
                    "_blank"
                );
            }

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

const getInfoProducto = () => {
    infoProductoSucursal.value = null;
    infoPromocion.value = null;
    if (agregarProducto.value.producto_id && form.sucursal_id) {
        axios
            .get(route("producto_sucursals.getProductoSucursal"), {
                params: {
                    sucursal_id: form.sucursal_id,
                    producto_id: agregarProducto.value.producto_id,
                },
            })
            .then((response) => {
                // console.log(response);
                infoProductoSucursal.value = response.data.producto_sucursal;
                agregarProducto.value.precio =
                    infoProductoSucursal.value.producto.monto_sf;
                if (form.factura == "SI") {
                    agregarProducto.value.precio =
                        infoProductoSucursal.value.producto.monto_cf;
                }
                infoPromocion.value = response.data.promocion;
                if (infoPromocion.value) {
                    console.log("tiene promocion");
                    agregarProducto.value.promocion_id = infoPromocion.value.id;
                    agregarProducto.value.promocion_descuento =
                        infoPromocion.value.porcentaje;
                    agregarProducto.value.promocion = infoPromocion.value;
                    const val_descuento =
                        1 - infoPromocion.value.porcentaje / 100;
                    agregarProducto.value.precio =
                        parseFloat(agregarProducto.value.precio) *
                        val_descuento;
                    agregarProducto.value.precio = Math.round(
                        agregarProducto.value.precio,
                        2
                    );
                }
            })
            .catch((err) => {
                console.log("ERROR");
                console.log(err);
                if (err.response.data.message) {
                    Swal.fire({
                        icon: "info",
                        title: "Error",
                        text: `${err.response.data.message}`,
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: `Aceptar`,
                    });
                } else {
                    Swal.fire({
                        icon: "info",
                        title: "Error",
                        text: `Ocurrió un error inesperado intente nuevamente`,
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: `Aceptar`,
                    });
                }
            });
    }
};

const validarCantidad = () => {
    if (agregarProducto.value.cantidad) {
        if (parseFloat(agregarProducto.value.cantidad) > 0) {
            if (
                agregarProducto.value.cantidad >
                infoProductoSucursal.value.stock_actual
            ) {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `La cantidad no puede ser mayor al stock actual de ${infoProductoSucursal.value.stock_actual}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
                return false;
            }
            return true;
        } else {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `La cantidad debe ser mayor a 0`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return false;
        }
    } else {
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `Debes ingresar la cantidad`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
        return false;
    }
};
const validarPrecioProducto = () => {
    if (agregarProducto.value.precio) {
        if (
            parseFloat(agregarProducto.value.precio) >=
            parseFloat(infoProductoSucursal.value.producto.precio_min)
        ) {
            return true;
        } else {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `El precio del producto no puede ser menor a ${infoProductoSucursal.value.producto.precio_min}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return false;
        }
    } else {
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `Debes ingresar precio precio`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
        return false;
    }
};

const agregarProductoTabla = async () => {
    if (
        validaAgregarProducto() &&
        !verificaExistente() &&
        validarCantidad() &&
        validarPrecioProducto()
    ) {
        agregarProducto.value.precio_reg = agregarProducto.value.precio;
        const producto = getProductoList(agregarProducto.value.producto_id);
        if (producto) {
            const subtotal =
                parseFloat(agregarProducto.value.precio) *
                parseFloat(agregarProducto.value.cantidad);
            form.detalle_ordens.push({
                id: 0,
                producto_id: agregarProducto.value.producto_id,
                producto: producto,
                promocion_id: agregarProducto.value.promocion_id,
                promocion_descuento: agregarProducto.value.promocion_descuento,
                promocion: agregarProducto.value.promocion,
                cantidad: agregarProducto.value.cantidad,
                precio: agregarProducto.value.precio,
                precio_reg: agregarProducto.value.precio_reg,
                subtotal: subtotal,
            });
            await sumaTotales();

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

const sumaTotales = () => {
    if (form.detalle_ordens.length > 0) {
        let total_final = 0;
        form.detalle_ordens.forEach((elem, index) => {
            total_final += parseFloat(elem.subtotal);
        });
        form.total = Math.round(total_final, 2);
    } else {
        form.total = "0.00";
    }
};

const limpiarAgregarDetalle = () => {
    agregarProducto.value.producto_id = "";
    agregarProducto.value.producto = null;
    agregarProducto.value.promocion_id = "";
    agregarProducto.value.promocion_descuento = "";
    agregarProducto.value.promocion = null;
    agregarProducto.value.cantidad = 1;
    agregarProducto.value.precio_reg = "";
    agregarProducto.value.precio = "";
    agregarProducto.value.subtotal = 0;

    infoProductoSucursal.value = null;
    infoPromocion.value = null;
};

const eliminarDetalle = async (index) => {
    const id = form.detalle_ordens[index].id;
    if (id != 0) {
        form.eliminados.push(id);
    }
    form.detalle_ordens.splice(index, 1);
    await sumaTotales();
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

const calcularTotales = () => {
    if (form.detalle_ordens.length > 0) {
        let total_final = 0;
        form.detalle_ordens.forEach((elem, index) => {
            const cantidad = elem.cantidad;
            let precio = parseFloat(elem.precio_reg);
            if (form.factura == "SI") {
                const p = 1 + parseFloat(elem.producto.precio_fac) / 100;
                precio = elem.precio_reg * p;
            } else {
                const p = 1 - parseFloat(elem.producto.precio_sf) / 100;
                precio = elem.precio_reg * p;
            }
            if (elem.promocion) {
                const val_descuento = 1 - elem.promocion.porcentaje / 100;
                precio = parseFloat(precio) * val_descuento;
            }
            if (precio < parseFloat(elem.producto.precio_min)) {
                precio = parseFloat(elem.producto.precio_min);
            }
            precio = Math.round(precio, 2);
            const subtotal = parseFloat(cantidad) * parseFloat(precio);
            form.detalle_ordens[index].precio = precio;
            form.detalle_ordens[index].subtotal = subtotal;
            total_final += subtotal;
        });
        form.total = total_final;
    } else {
        form.total = "0.00";
    }
};

const verificaPrecioDetalle = (index, e) => {
    const elem = form.detalle_ordens[index];
    const cantidad = elem.cantidad;
    let precio = parseFloat(e.target.value ?? 0);
    if (precio < parseFloat(elem.producto.precio_min)) {
        precio = elem.precio_reg;
        Swal.fire({
            icon: "info",
            title: "Error",
            text: `El monto no puede ser menor a ${elem.producto.precio_min}`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
    }
    precio = Math.round(precio, 2);
    const subtotal = parseFloat(cantidad) * parseFloat(precio);
    form.detalle_ordens[index].precio = precio;
    form.detalle_ordens[index].precio_reg = precio;
    form.detalle_ordens[index].subtotal = subtotal;
    sumaTotales();
};

const verificaCantidades = (index, e) => {
    const elem = form.detalle_ordens[index];
    const precio = parseFloat(elem.precio);
    const cantidad = e.target.value;
    const subtotal = parseFloat(cantidad) * parseFloat(precio);
    form.detalle_ordens[index].cantidad = cantidad;
    form.detalle_ordens[index].subtotal = subtotal;
    sumaTotales();
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

//RELACION PRODUCTOS
const abrirRelacionProducto = () => {
    if (infoProductoSucursal.value) {
        open_dialog_relacion.value = true;
    }
};

const seleccionarProductoRelacion = (producto_id) => {
    open_dialog_relacion.value = false;
    accion_dialog_relacion.value = 0;
    agregarProducto.value.producto_id = producto_id;
    getInfoProducto();
};

//PRODUCTOS SUCURSAL
const openProductoSucursal = () => {
    open_dialog_producto_sucursal.value = true;
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
    <Relacion
        :open_dialog="open_dialog_relacion"
        :accion_dialog="accion_dialog_relacion"
        :producto="infoProductoSucursal?.producto"
        :id-sucursal="infoProductoSucursal?.sucursal_id"
        @envio-formulario="seleccionarProductoRelacion"
        @cerrar-dialog="open_dialog_relacion = false"
    ></Relacion>
    <ProductoSucursal
        :open_dialog="open_dialog_producto_sucursal"
        :accion_dialog="accion_dialog_producto_sucursal"
        @envio-formulario="console.log('.')"
        @cerrar-dialog="open_dialog_producto_sucursal = false"
    ></ProductoSucursal>
    <form>
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-2 overflow-auto">
                    <div class="col-12">
                        <h4 class="w-100 text-center">Productos agregados</h4>
                    </div>
                    <div class="col-12 mt-2 mb-0">
                        <table class="table table-bordered bg-white mb-1">
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
                                <template v-if="form.detalle_ordens.length > 0">
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
                                                @keyup="
                                                    verificaCantidades(
                                                        index,
                                                        $event
                                                    )
                                                "
                                                @change="
                                                    verificaCantidades(
                                                        index,
                                                        $event
                                                    )
                                                "
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                step="1"
                                                class="form-control"
                                                placeholder="Cantidad"
                                                v-model="item.precio"
                                                @change="
                                                    verificaPrecioDetalle(
                                                        index,
                                                        $event
                                                    )
                                                "
                                            />
                                        </td>
                                        <td>
                                            {{ item.subtotal }}
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-danger btn-sm"
                                                @click.prevent="
                                                    eliminarDetalle(index)
                                                "
                                            >
                                                <i class="fa fa-trash"></i>
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
                            <tfoot>
                                <tr>
                                    <th class="h5" colspan="4">TOTAL</th>
                                    <th class="h5">{{ form.total }}</th>
                                </tr>
                            </tfoot>
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
                </div>

                <div class="row my-2">
                    <div class="col-12 my-1">
                        <label>Factura*</label>
                        <select
                            class="form-control"
                            v-model="form.factura"
                            @change="calcularTotales"
                        >
                            <option value="NO">NO</option>
                            <option value="SI">SI</option>
                        </select>
                    </div>

                    <div class="col-12 my-1">
                        <label>Tipo de Pago*</label>
                        <select class="form-control" v-model="form.tipo_pago">
                            <option value="EFECTIVO">EFECTIVO</option>
                            <option value="QR">QR</option>
                        </select>
                    </div>
                    <div class="col-md-12 my-1">
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
                    <div class="col-12 form-group my-1">
                        <label>NIT/C.I.:</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="form.nit_ci"
                        />
                        <ul
                            v-if="form.errors?.nit_ci"
                            class="parsley-errors-list filled"
                        >
                            <li class="parsley-required">
                                {{ form.errors?.nit_ci }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            @click="enviarFormulario"
                        >
                            <template v-if="oOrdenVenta.id == 0">
                                <i class="fa fa-save"></i>
                                Guardar Orden de Venta</template
                            >
                            <template v-else>
                                <i class="fa fa-edit"></i>
                                Actualizar Orden de Venta</template
                            >
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div
                                class="col-md-12 my-1"
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
                                    @change="getInfoProducto"
                                    :disabled="form.detalle_ordens.length > 0"
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
                            <div class="col-12 my-1" v-else>
                                <h5 class="w-100 text-center">
                                    {{ props_page.auth?.user.sucursal?.nombre }}
                                </h5>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar producto* </label>
                                <el-select
                                    class="w-100"
                                    clearable
                                    placeholder="Producto*"
                                    no-data-text="Sin datos"
                                    filterable
                                    v-model="agregarProducto.producto_id"
                                    @change="getInfoProducto"
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
                                    v-if="infoProductoSucursal"
                                >
                                    <div class="col-12">
                                        <div class="card rounded-0 bg-light">
                                            <div class="card-body">
                                                <div class="row">
                                                    <p>
                                                        <strong
                                                            >Stock
                                                            actual:</strong
                                                        >
                                                        {{
                                                            infoProductoSucursal.stock_actual
                                                        }}
                                                    </p>
                                                    <p>
                                                        <strong>P/SF:</strong>
                                                        {{
                                                            infoProductoSucursal
                                                                .producto
                                                                .monto_sf
                                                        }}
                                                    </p>
                                                    <p>
                                                        <strong>P/CF:</strong>
                                                        {{
                                                            infoProductoSucursal
                                                                .producto
                                                                .monto_cf
                                                        }}
                                                    </p>
                                                    <p>
                                                        <strong>P/min.:</strong>
                                                        {{
                                                            infoProductoSucursal
                                                                .producto
                                                                .precio_min
                                                        }}
                                                    </p>
                                                    <template
                                                        v-if="infoPromocion"
                                                    >
                                                        <p>
                                                            <strong
                                                                >Promoción:</strong
                                                            >
                                                            SI
                                                        </p>
                                                        <p>
                                                            <strong
                                                                >Descuento:</strong
                                                            >
                                                            {{
                                                                infoPromocion.porcentaje
                                                            }}%
                                                        </p>
                                                    </template>

                                                    <button
                                                        class="btn btn-primary btn-sm"
                                                        title="Relación"
                                                        @click.prevent="
                                                            abrirRelacionProducto
                                                        "
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
                                    type="button"
                                    class="btn btn-sm btn-outline-primary w-100"
                                    @click.prevent="agregarProductoTabla"
                                >
                                    <i class="fa fa-plus"></i> Agregar Producto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button
                        type="button"
                        class="btn btn-warning w-100"
                        @click.prevent="openProductoSucursal"
                    >
                        <i class="fa fa-search"></i> Buscar productos por
                        sucursal
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>
