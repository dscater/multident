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
    solicitudProducto: {
        type: Object,
        default: {},
    },
});

const oSolicitudProducto = ref(props.solicitudProducto);
const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog"]);

watch(
    () => props.solicitudProducto,
    (newVal) => {
        oSolicitudProducto.value = newVal;
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
    return `<i class="fa fa-info-circle"></i> Solicitud de Producto: ${oSolicitudProducto.value?.codigo_solicitud}`;
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
                <div
                    class="modal-body"
                    v-if="oSolicitudProducto && oSolicitudProducto.cliente"
                >
                    <div class="row">
                        <div class="col-12 border-bottom">
                            <div class="row pt-3">
                                <div class="col-4 text-right">
                                    <strong>Cliente:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente.full_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Correo:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente.correo }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Celular:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente.cel }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Dpto.:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.sede.nombre }}
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 text-right">
                                    <strong>Solicitud:</strong>
                                </div>
                                <div class="col-8 text-left">
                                    <span
                                        class="badge"
                                        :class="{
                                            'bg-secondary':
                                                oSolicitudProducto.estado_solicitud ==
                                                'PENDIENTE',
                                            'bg-success':
                                                oSolicitudProducto.estado_solicitud ==
                                                'APROBADO',
                                            'bg-danger':
                                                oSolicitudProducto.estado_solicitud ==
                                                'RECHAZADO',
                                        }"
                                        >{{
                                            oSolicitudProducto.estado_solicitud
                                        }}</span
                                    >
                                </div>
                            </div>
                            <div
                                class="row my-2"
                                v-if="
                                    oSolicitudProducto.estado_solicitud ==
                                    'APROBADO'
                                "
                            >
                                <div class="col-12">
                                    <div class="row my-2">
                                        <div class="col-4 text-right">
                                            <strong class="text-md"
                                                >Total
                                                {{
                                                    oConfiguracion?.conf_moneda
                                                        .abrev
                                                }}:</strong
                                            >
                                        </div>
                                        <div class="col-8 text-md">
                                            {{
                                                getFormatoMoneda(oSolicitudProducto.total_precio)
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 text-right">
                                    <strong>Estado Entrega:</strong>
                                </div>
                                <div class="col-8 text-left">
                                    <span
                                        class="badge"
                                        :class="{
                                            'bg-secondary':
                                                oSolicitudProducto.estado_seguimiento ==
                                                    'PENDIENTE' ||
                                                !oSolicitudProducto.estado_seguimiento,
                                            'bg-primary':
                                                oSolicitudProducto.estado_seguimiento ==
                                                'EN PROCESO',
                                            'bg-info':
                                                oSolicitudProducto.estado_seguimiento ==
                                                'EN ALMACÉN',
                                            'bg-success':
                                                oSolicitudProducto.estado_seguimiento ==
                                                'ENTREGADO',
                                        }"
                                        >{{
                                            oSolicitudProducto.estado_seguimiento
                                        }}</span
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Fecha:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.fecha_solicitud_t }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div
                                    class="col-4 text-right"
                                    v-if="oSolicitudProducto.observacion"
                                >
                                    <strong>Observaciones:</strong>
                                </div>
                                <div
                                    class="col-4"
                                    v-if="oSolicitudProducto.observacion"
                                    v-html="oSolicitudProducto.observacion"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <h5 class="w-100 text-center">Producto Solicitado</h5>
                        <div class="col-12 overflow-auto">
                            <table class="table table-striped">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="text-white text-sm">
                                            Nombre Producto
                                        </th>
                                        <th class="text-white text-sm">
                                            Detalle del producto
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template
                                        v-for="(
                                            item, index
                                        ) in oSolicitudProducto.solicitud_detalles"
                                    >
                                        <tr>
                                            <td>{{ item.nombre_producto }}</td>
                                            <td>{{ item.detalle_producto }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                colspan="3"
                                                v-html="item.links_referencia"
                                            ></td>
                                        </tr>
                                    </template>
                                </tbody>
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
