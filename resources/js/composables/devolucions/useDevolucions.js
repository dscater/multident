import { onMounted, ref } from "vue";

const oDevolucion = ref({
    id: 0,
    sucursal_id: "",
    orden_venta_id: "",
    producto_id: "",
    detalle_orden_id: "",
    razon: "",
    descripcion: "",
    _method: "POST",
});

export const useDevolucions = () => {
    const setDevolucion = (item = null) => {
        if (item) {
            oDevolucion.value.id = item.id;
            oDevolucion.value.sucursal_id = item.sucursal_id;
            oDevolucion.value.orden_venta_id = item.orden_venta_id;
            oDevolucion.value.producto_id = item.producto_id;
            oDevolucion.value.detalle_orden_id = item.detalle_orden_id;
            oDevolucion.value.razon = item.razon;
            oDevolucion.value.descripcion = item.descripcion;
            oDevolucion.value._method = "PUT";
            return oDevolucion;
        }
        return false;
    };

    const limpiarDevolucion = () => {
        oDevolucion.value.id = 0;
        oDevolucion.value.sucursal_id = "";
        oDevolucion.value.orden_venta_id = "";
        oDevolucion.value.producto_id = "";
        oDevolucion.value.detalle_orden_id = "";
        oDevolucion.value.razon = "";
        oDevolucion.value.descripcion = "";
        oDevolucion.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oDevolucion,
        setDevolucion,
        limpiarDevolucion,
    };
};
