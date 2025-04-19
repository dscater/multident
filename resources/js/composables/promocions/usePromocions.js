import { onMounted, ref } from "vue";

const oPromocion = ref({
    id: 0,
    producto_id: "",
    porcentaje: "",
    fecha_ini: "",
    fecha_fin: "",
    descripcion: "",
    _method: "POST",
});

export const usePromocions = () => {
    const setPromocion = (item = null) => {
        if (item) {
            oPromocion.value.id = item.id;
            oPromocion.value.producto_id = item.producto_id;
            oPromocion.value.porcentaje = item.porcentaje;
            oPromocion.value.fecha_ini = item.fecha_ini;
            oPromocion.value.fecha_fin = item.fecha_fin;
            oPromocion.value.descripcion = item.descripcion;
            oPromocion.value._method = "PUT";
            return oPromocion;
        }
        return false;
    };

    const limpiarPromocion = () => {
        oPromocion.value.id = 0;
        oPromocion.value.producto_id = "";
        oPromocion.value.porcentaje = "";
        oPromocion.value.fecha_ini = "";
        oPromocion.value.fecha_fin = "";
        oPromocion.value.descripcion = "";
        oPromocion.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oPromocion,
        setPromocion,
        limpiarPromocion,
    };
};
