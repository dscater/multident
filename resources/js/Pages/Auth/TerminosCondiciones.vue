<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { watch, ref, computed, defineEmits, onMounted } from "vue";
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
});

const dialog = ref(props.open_dialog);
const oPublicacion = ref(props.publicacion);
const aDetalles = ref(props.detalles);
const terminos_condiciones = ref("");
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        getTerminosCondiciones();
        if (dialog.value) {
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
        }
    }
);

const { flash } = usePage().props;

const tituloDialog = computed(() => {
    return `<i class="fa fa-list"></i> Términos y condiciones`;
});

const emits = defineEmits(["cerrar-dialog"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const cerrarDialog = () => {
    dialog.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const getTerminosCondiciones = () => {
    axios
        .get(route("getTerminosCondiciones"), {
            params: {
                q: Math.floor(Date.now() / 1000),
            },
        })
        .then((response) => {
            terminos_condiciones.value = response.data;
        });
};

onMounted(() => {});
</script>

<template>
    <div
        class="modal fade"
        :class="{
            show: dialog,
        }"
        id="modal-dialog-form"
        :style="{
            display: dialog ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <!-- <h4 class="modal-title" v-html="tituloDialog"></h4> -->
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p v-html="terminos_condiciones"></p>
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
#modal-dialog-form {
    background-color: rgba(0, 0, 0, 0.719);
}
</style>
