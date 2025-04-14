<script>
import SliderImagenes from "@/Components/SliderImagenes.vue";
</script>
<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { onMounted, ref, inject, computed, onBeforeUnmount, watch } from "vue";
import { useFormater } from "@/composables/useFormater";
import { useOrdenVentaStore } from "@/stores/ordenVenta/ordenVentaStore";
import axios from "axios";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
const { oConfiguracion } = useConfiguracion();
const ordenVentaStore = useOrdenVentaStore();
const { getFormatoMoneda } = useFormater();
const { props: props_page } = usePage();
const props = defineProps({
    producto: {
        type: Object,
        default: null,
    },
});

const stockIniProducto = ref(props.producto.stock_actual);

const form = ref({
    cantidad: 1,
    errors: [],
});

const open_modal = ref(false);
const abrirModal = () => {
    open_modal.value = true;
};
const cerrarDialog = () => {
    open_modal.value = false;
};
const agregarCantidadProducto = () => {
    if (verificaCantidad() > 0) {
        const cantidad = parseInt(form.value.cantidad);
        ordenVentaStore.addProducto({
            producto_id: props.producto.id,
            url_imagen: props.producto.imagens[0].url_imagen,
            nombre_prod: props.producto.nombre,
            cantidad: cantidad,
            precio: props.producto.precio_venta,
            subtotal: 0,
        });
        stockIniProducto.value--;
        Swal.fire({
            icon: "success",
            title: "Correcto",
            text: `Producto agregado`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
    } else {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: `Stock insuficiente`,
            confirmButtonColor: "#3085d6",
            confirmButtonText: `Aceptar`,
        });
    }
    open_modal.value = false;
};

const verificaCantidad = (cantidad) => {
    stockIniProducto.value = props.producto.stock_actual;
    const existeProducto = ordenVentaStore.getProductoCarrito(
        props.producto.id
    );

    let cantidad_carrito = 0;
    if (existeProducto) {
        cantidad_carrito = existeProducto.cantidad;
    }
    stockIniProducto.value -= parseInt(cantidad_carrito);

    return stockIniProducto.value;
};

watch(
    () => props.producto,
    (newValue) => {
        oPublicacion.value = newValue;
        swRealizarOferta.value =
            oPublicacion.value.esta_vigente == true ? true : false;
    }
);

onMounted(() => {
    ordenVentaStore.initCarrito();
    verificaCantidad();
});

onBeforeUnmount(() => {});
</script>
<template>
    <div class="product-detail row">
        <!-- BEGIN product-image -->
        <div class="product-image col-md-6">
            <SliderImagenes
                :imagenes="producto?.imagens"
                class="slider-imagenes"
            ></SliderImagenes>
        </div>
        <!-- END product-image -->
        <!-- BEGIN product-info -->
        <div class="product-info col-md-6 pt-4 pb-4 px-4">
            <div class="product-info-detail pb-3">
                <h4 class="product-title">{{ producto.nombre }}</h4>
                <span class="text-muted">{{ producto.categoria.nombre }}</span>
                <p class="pull-right mb-0 mt-2">{{ producto.descripcion }}</p>
            </div>
            <div class="product-info-detail pt-3 pb-3">
                <div class="pull-right">Disponible: {{ stockIniProducto }}</div>
            </div>
            <div class="pt-4 pb-3">
                <div class="product-price mb-3">
                    <div class="price">
                        {{ oConfiguracion.conf_moneda?.abrev }}
                        {{ getFormatoMoneda(producto.precio_venta) }}
                    </div>
                </div>
                <button
                    class="btn btn-dark btn-theme btn-lg"
                    @click.prevent="abrirModal()"
                >
                    <i class="fa fa-cart-plus"></i> AGREGAR AL CARRITO
                </button>
            </div>
        </div>
        <!-- END product-info -->
    </div>

    <div
        class="modal fade"
        :class="{
            show: open_modal,
        }"
        id="modal-dialog-form"
        :style="{
            display: open_modal ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title">
                        <i class="fa fa-cart-plus"></i> Agregar
                    </h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="agregarCantidadProducto()">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Ingresa la cantidad*</label>
                                <input
                                    type="number"
                                    step="1"
                                    class="form-control"
                                    :class="{
                                        'parsley-error': form.errors?.cantidad,
                                    }"
                                    v-model="form.cantidad"
                                    min="1"
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
                        @click="agregarCantidadProducto()"
                        class="btn btn-dark"
                    >
                        <i class="fa fa-check"></i>
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.slider-imagenes {
    height: 100%;
}
.product-detail {
    padding: 0;
    background-color: white;
}

.product-detail .product-image {
    padding: 0;
}

.product-info-detail {
    border-bottom: 1px solid var(--bs-component-border-color);
}

.price {
    font-weight: 600;
    font-size: 2rem;
}
</style>
