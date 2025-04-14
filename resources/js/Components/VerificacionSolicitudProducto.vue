<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted } from "vue";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import { useAxios } from "@/composables/axios/useAxios";
import { useFormater } from "@/composables/useFormater";
const { getFormatoMoneda } = useFormater();
const { oConfiguracion } = useConfiguracion();

const { url_assets, auth } = usePage().props;
const user = auth.user;
const { axiosPost } = useAxios();
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
const form = ref({
    estado_solicitud: "PENDIENTE",
    observacion: oSolicitudProducto.value?.observacion,
    precio_compra: oSolicitudProducto.value?.precio_compra,
    margen_ganancia: oSolicitudProducto.value?.margen_ganancia,
    totalSolicitud: 0,
});
const enviando = ref(false);
const listEstados = ref(["PENDIENTE", "RECHAZADO", "APROBADO"]);
const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(
    () => props.solicitudProducto,
    (newVal) => {
        oSolicitudProducto.value = newVal;
        form.value.estado_solicitud =
            oSolicitudProducto.value?.estado_solicitud;
        form.value.observacion = oSolicitudProducto.value?.observacion;
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

const totalSolicitud = computed(() => {
    if (
        oSolicitudProducto.value.margen_ganancia &&
        oSolicitudProducto.value.precio_compra
    ) {
        const total =
            parseFloat(
                isNaN(oSolicitudProducto.value.margen_ganancia)
                    ? 0
                    : oSolicitudProducto.value.margen_ganancia
            ) +
            parseFloat(
                isNaN(oSolicitudProducto.value.precio_compra)
                    ? 0
                    : oSolicitudProducto.value.precio_compra
            );
        return total;
    }
    return "";
});
const calculaTotal = () => {
    const total =
        parseFloat(
            isNaN(form.value.margen_ganancia) ? 0 : form.value.margen_ganancia
        ) +
        parseFloat(
            isNaN(form.value.precio_compra) ? 0 : form.value.precio_compra
        );
    form.value.totalSolicitud = total;
};

const tituloDialog = computed(() => {
    return `<i class="fa fa-info-circle"></i> Solicitud de producto: ${oSolicitudProducto.value?.codigo_solicitud}`;
});

const txtBtnGuardar = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spinner fa-spin"></i> Guardando...`;
    }
    return `<i class="fa fa-save"></i> Guardar cambios`;
});

const verificaPrecios = () => {
    if (form.value.estado_solicitud != "APROBADO") {
        form.value.precio_compra = null;
        form.value.margen_ganancia = null;
    }
};

const enviarFormulario = async () => {
    enviando.value = true;
    try {
        const resp = await axiosPost(
            route(
                "solicitud_productos.update_estado_verificacion",
                oSolicitudProducto.value.id
            ),
            {
                _method: "PATCH",
                estado_solicitud: form.value.estado_solicitud,
                observacion: form.value.observacion,
                precio_compra: form.value.precio_compra,
                margen_ganancia: form.value.margen_ganancia,
            }
        );
        form.value.precio_compra = "";
        form.value.margen_ganancia = "";
        emits("envio-formulario");
    } catch (e) {
        console.log(e);
    } finally {
        enviando.value = false;
    }
};

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
                <div class="modal-body" v-if="oSolicitudProducto">
                    <div class="row">
                        <div class="col-12 border-bottom">
                            <div class="row pt-3">
                                <div class="col-4 text-right">
                                    <strong>Cliente:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente?.full_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Correo:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente?.correo }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-right">
                                    <strong>Celular:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.cliente?.cel }}
                                </div>
                            </div>
                            <div
                                class="row"
                                :class="[
                                    oSolicitudProducto.estado_solicitud ==
                                    'PENDIENTE'
                                        ? 'my-2'
                                        : '',
                                ]"
                            >
                                <div class="col-4 text-right">
                                    <strong>Estado:</strong>
                                </div>
                                <div
                                    class="col-8 text-left"
                                    v-if="
                                        oSolicitudProducto.estado_solicitud ==
                                        'APROBADO'
                                    "
                                >
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
                                <div class="col-8 text-left" v-else>
                                    <select
                                        class="form-select text-sm rounded-0"
                                        v-model="form.estado_solicitud"
                                        @change="verificaPrecios"
                                    >
                                        <option
                                            v-for="item in listEstados"
                                            :value="item"
                                        >
                                            {{ item }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="col-12"
                                    v-if="form.estado_solicitud == 'APROBADO'"
                                >
                                    <div class="row my-1">
                                        <div class="col-4 text-right">
                                            <strong
                                                >Precio de compra
                                                {{
                                                    oConfiguracion?.conf_moneda
                                                        .abrev
                                                }}:</strong
                                            >
                                        </div>
                                        <div class="col-8">
                                            <template
                                                v-if="
                                                    oSolicitudProducto.estado_solicitud ==
                                                    'APROBADO'
                                                "
                                                >{{
                                                    oSolicitudProducto.precio_compra
                                                }}</template
                                            >
                                            <input
                                                class="form-control"
                                                v-model="form.precio_compra"
                                                type="number"
                                                @keyup="calculaTotal"
                                                v-else
                                            />
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <div class="col-4 text-right">
                                            <strong
                                                >Margen de ganancia
                                                {{
                                                    oConfiguracion?.conf_moneda
                                                        .abrev
                                                }}:</strong
                                            >
                                        </div>
                                        <div class="col-8">
                                            <template
                                                v-if="
                                                    oSolicitudProducto.estado_solicitud ==
                                                    'APROBADO'
                                                "
                                                >{{
                                                    oSolicitudProducto.margen_ganancia
                                                }}</template
                                            >
                                            <input
                                                class="form-control"
                                                v-model="form.margen_ganancia"
                                                type="number"
                                                @keyup="calculaTotal"
                                                v-else
                                            />
                                        </div>
                                    </div>
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
                                        <div class="col-8">
                                            <template
                                                v-if="
                                                    oSolicitudProducto.estado_solicitud ==
                                                    'APROBADO'
                                                "
                                            >
                                                <span
                                                    class="text-md font-weight-bold"
                                                >
                                                    {{
                                                        getFormatoMoneda(
                                                            totalSolicitud
                                                        )
                                                    }}
                                                </span>
                                            </template>
                                            <span v-else class="text-md">{{
                                                getFormatoMoneda(
                                                    form.totalSolicitud
                                                )
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row pb-3">
                                <div class="col-4 text-right">
                                    <strong>Seguimiento:</strong>
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
                                            oSolicitudProducto.estado_seguimiento ??
                                            "PENDIENTE"
                                        }}</span
                                    >
                                </div>
                            </div>
                            <div class="row pb-3">
                                <div class="col-4 text-right">
                                    <strong>Fecha:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oSolicitudProducto.fecha_solicitud_t }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 overflow-auto">
                        <h5 class="w-100 text-center">Producto Solicitado</h5>
                        <div class="col-12 overflow-auto">
                            <table class="table table-striped">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="text-white text-sm">
                                            Producto
                                        </th>
                                        <th class="text-white text-sm">
                                            Detalle
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
                                                colspan="2"
                                                v-html="item.links_referencia"
                                            ></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="col-12"
                            v-if="
                                oSolicitudProducto.estado_solicitud ==
                                'APROBADO'
                            "
                        >
                            <div class="row">
                                <div class="col-4 text-right">
                                    Observaciones:
                                </div>
                                <div
                                    class="col-8"
                                    v-html="oSolicitudProducto.observacion"
                                ></div>
                            </div>
                        </div>
                        <div class="col-12" v-else>
                            <el-input
                                type="textarea"
                                v-model="form.observacion"
                                autosize
                                placeholder="Observaciones"
                            ></el-input>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" v-if="oSolicitudProducto">
                    <a
                        href="javascript:;"
                        class="btn btn-default"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                    <button
                        type="button"
                        class="btn btn-primary"
                        v-if="
                            oSolicitudProducto.estado_solicitud != 'APROBADO' &&
                            (user?.permisos == '*' ||
                                user?.permisos.includes(
                                    'solicitud_productos.confirmar'
                                ))
                        "
                        v-html="txtBtnGuardar"
                        @click="enviarFormulario"
                        :disabled="enviando"
                    ></button>
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
