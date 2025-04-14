<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted } from "vue";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
const { oConfiguracion } = useConfiguracion();
import { useFormater } from "@/composables/useFormater";
const { getFormatoMoneda } = useFormater();

const { url_assets } = usePage().props;
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    ordenVenta: {
        type: Object,
        default: {},
    },
});

const oOrdenVenta = ref(props.ordenVenta);
const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog"]);

watch(
    () => props.ordenVenta,
    (newVal) => {
        oOrdenVenta.value = newVal;
    }
);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
        }
    }
);

const cerrarDialog = () => {
    dialog.value = false;
    if (props.hideBg) {
        document.getElementsByTagName("body")[0].classList.remove("modal-open");
    }
};

const tituloDialog = computed(() => {
    return `<i class="fa fa-info-circle"></i> Orden de venta: ${oOrdenVenta.value?.codigo}`;
});

onMounted(() => {});
</script>

<template>
    <div
        class="modal fade z-1000"
        :class="{
            show: dialog,
        }"
        id="modal-dialog-datos-pago"
        :style="{
            display: dialog ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body" v-if="oOrdenVenta">
                    <div class="row">
                        <div class="col-12 border-bottom">
                            <div class="row pt-3">
                                <div class="col-4 text-right">
                                    <strong>Cliente:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oOrdenVenta.cliente.full_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Correo:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oOrdenVenta.cliente.correo }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Celular:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oOrdenVenta.cliente.cel }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Estado:</strong>
                                </div>
                                <div class="col-8 text-left">
                                    <span
                                        class="badge"
                                        :class="{
                                            'bg-secondary':
                                                oOrdenVenta.estado_orden ==
                                                'PENDIENTE',
                                            'bg-success':
                                                oOrdenVenta.estado_orden ==
                                                'CONFIRMADO',
                                            'bg-danger':
                                                oOrdenVenta.estado_orden ==
                                                'RECHAZADO',
                                        }"
                                        >{{ oOrdenVenta.estado_orden }}</span
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Fecha:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oOrdenVenta.fecha_orden_t }}
                                </div>
                            </div>
                            <div class="row pb-3">
                                <div class="col-4 text-right">
                                    <strong>Comprobante:</strong>
                                </div>
                                <div class="col-8">
                                    <a
                                        :href="oOrdenVenta.url_comprobante"
                                        target="_blank"
                                        ><i
                                            class="fa fa-external-link text-black"
                                        ></i
                                    ></a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div
                                    class="col-4 text-right"
                                    v-if="oOrdenVenta.observacion"
                                >
                                    <strong>Observaciones:</strong>
                                </div>
                                <div
                                    class="col-4"
                                    v-if="oOrdenVenta.observacion"
                                    v-html="oOrdenVenta.observacion"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <h5 class="w-100 text-center">Detalle</h5>
                        <div class="col-12 overflow-auto">
                            <table class="table table-striped">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="text-white text-sm">#</th>
                                        <th
                                            class="text-white text-sm"
                                            colspan="2"
                                        >
                                            Producto
                                        </th>
                                        <th
                                            class="text-white text-sm text-center"
                                        >
                                            Cantidad
                                        </th>
                                        <th
                                            class="text-white text-sm text-right"
                                        >
                                            Precio
                                            {{
                                                oConfiguracion?.conf_moneda
                                                    ?.abrev
                                            }}
                                        </th>
                                        <th
                                            class="text-white text-sm text-right"
                                        >
                                            Subtotal
                                            {{
                                                oConfiguracion?.conf_moneda
                                                    ?.abrev
                                            }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            item, index
                                        ) in oOrdenVenta.detalle_venta"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <img
                                                :src="
                                                    item.producto.imagens[0]
                                                        .url_imagen
                                                "
                                                width="90px"
                                            />
                                        </td>
                                        <td>{{ item.producto.nombre }}</td>
                                        <td class="text-center">
                                            {{ item.cantidad }}
                                        </td>
                                        <td class="text-right">{{ getFormatoMoneda(item.precio) }}</td>
                                        <td class="text-right">{{ getFormatoMoneda(item.subtotal) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <th
                                            colspan="5"
                                            class="text-right text-white"
                                        >
                                            TOTAL
                                        </th>
                                        <th class="text-white text-right">
                                            {{ getFormatoMoneda(oOrdenVenta.total) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-default"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
#modal-dialog-datos-pago {
    z-index: 1300;
}
</style>
