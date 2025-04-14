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
    estado_seguimiento: "PENDIENTE",
    observacion: oSolicitudProducto.value?.observacion,
    precio_compra: oSolicitudProducto.value?.precio_compra,
    margen_ganancia: oSolicitudProducto.value?.margen_ganancia,
});
const enviando = ref(false);
const listEstados = ref([
    { value: "PENDIENTE", label: "Pendiente" },
    { value: "EN PROCESO", label: "En proceso" },
    { value: "EN ALMACÉN", label: "En almacén" },
    { value: "ENTREGADO", label: "Entregado" },
]);
const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(
    () => props.solicitudProducto,
    (newVal) => {
        oSolicitudProducto.value = newVal;
        form.value.estado_seguimiento =
            oSolicitudProducto.value?.estado_seguimiento;
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

const tituloDialog = computed(() => {
    return `<i class="fa fa-clipboard-check"></i> Seguimiento Solicitud de producto: ${oSolicitudProducto.value?.codigo_solicitud}`;
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
                "solicitud_productos.update_estado_seguimiento",
                oSolicitudProducto.value.id
            ),
            {
                _method: "PATCH",
                estado_seguimiento: form.value.estado_seguimiento,
                observacion: form.value.observacion,
            }
        );
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
                            <div class="row">
                                <div class="col-12">
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
                                            {{
                                                getFormatoMoneda(
                                                    oSolicitudProducto.precio_compra
                                                )
                                            }}
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
                                            {{
                                                getFormatoMoneda(
                                                    oSolicitudProducto.margen_ganancia
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row my-2">
                                <div class="col-4 text-right">
                                    <strong class="text-md"
                                        >Total
                                        {{
                                            oConfiguracion?.conf_moneda.abrev
                                        }}:</strong
                                    >
                                </div>
                                <div class="col-8">
                                    <span class="text-md font-weight-bold">
                                        {{ getFormatoMoneda(oSolicitudProducto.total_precio) }}
                                    </span>
                                </div>
                            </div>

                            <div class="row my-2">
                                <div class="col-4 text-right">
                                    <strong>Seguimiento:</strong>
                                </div>
                                <div class="col-8 text-left">
                                    <select
                                        v-model="form.estado_seguimiento"
                                        class="form-select"
                                    >
                                        <option
                                            v-for="item in listEstados"
                                            :value="item.value"
                                        >
                                            {{ item.label }}
                                        </option>
                                    </select>
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
                                        <th
                                            class="text-white text-sm text-center"
                                        >
                                            Detalle
                                        </th>
                                        <th
                                            class="text-white text-sm text-right"
                                        >
                                            Links de referencia
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            item, index
                                        ) in oSolicitudProducto.solicitud_detalles"
                                    >
                                        <td>{{ item.nombre_producto }}</td>
                                        <td>{{ item.detalle_producto }}</td>
                                        <td v-html="item.links_referencia"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <label>Observaciones:</label>
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
                        v-if="
                            user?.permisos == '*' ||
                            user?.permisos.includes(
                                'solicitud_productos.seguimiento'
                            )
                        "
                        type="button"
                        class="btn btn-primary"
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
