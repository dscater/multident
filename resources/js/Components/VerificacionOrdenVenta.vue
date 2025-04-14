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
    ordenVenta: {
        type: Object,
        default: {},
    },
});

const oOrdenVenta = ref(props.ordenVenta);
const form = ref({
    estado_orden: "PENDIENTE",
    observacion: "",
});
const enviando = ref(false);
const listEstados = ref(["PENDIENTE", "RECHAZADO", "CONFIRMADO"]);
const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(
    () => props.ordenVenta,
    (newVal) => {
        oOrdenVenta.value = newVal;
        form.value.estado_orden = oOrdenVenta.value?.estado_orden;
        form.value.observacion = oOrdenVenta.value?.observacion;
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

const txtBtnGuardar = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spinner fa-spin"></i> Guardando...`;
    }
    return `<i class="fa fa-save"></i> Guardar cambios`;
});

const enviarFormulario = async () => {
    enviando.value = true;
    try {
        const resp = await axiosPost(
            route("orden_ventas.update_estado", oOrdenVenta.value.id),
            {
                _method: "PATCH",
                estado_orden: form.value.estado_orden,
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
                            <div
                                class="row"
                                :class="[
                                    oOrdenVenta.estado_orden == 'PENDIENTE'
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
                                        oOrdenVenta.estado_orden == 'CONFIRMADO'
                                    "
                                >
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
                                <div class="col-8 text-left" v-else>
                                    <select
                                        class="form-select text-sm rounded-0"
                                        v-model="form.estado_orden"
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
                            <div class="row pb-3">
                                <div class="col-4 text-right">
                                    <strong>Fecha:</strong>
                                </div>
                                <div class="col-8">
                                    {{ oOrdenVenta.fecha_orden_t }}
                                </div>
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
                        <div
                            class="col-12"
                            v-if="oOrdenVenta.estado_orden == 'CONFIRMADO'"
                        >
                            <div class="row">
                                <div class="col-4 text-right">
                                    Observaciones:
                                </div>
                                <div
                                    class="col-8"
                                    v-html="oOrdenVenta.observacion"
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
                <div class="modal-footer" v-if="oOrdenVenta">
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
                            oOrdenVenta.estado_orden != 'CONFIRMADO' &&
                            (user?.permisos == '*' ||
                                user?.permisos.includes(
                                    'orden_ventas.confirmar'
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
