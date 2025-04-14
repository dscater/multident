<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted } from "vue";
import axios from "axios";
const { url_assets } = usePage().props;
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    hideBg: {
        type: Boolean,
        default: true,
    },
});

const dialog = ref(props.open_dialog);
const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

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
            getDatosPago();
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
    return `<i class="fa fa-info-circle"></i> Datos para el pago de garantía`;
});

const htmlDatosPago = ref("");
const getDatosPago = () => {
    axios.get(route("inicio.getParcialDatosPago")).then((response) => {
        htmlDatosPago.value = response.data;
    });
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body" v-html="htmlDatosPago"></div>
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
