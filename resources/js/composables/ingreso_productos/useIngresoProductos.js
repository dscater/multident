import { onMounted, ref } from "vue";

const oIngresoProducto = ref({
    id: 0,
    sucursal_id: "",
    fecha_registro: "",
    descripcion: "",
    ingreso_detalles: [],
    eliminados: [],
    _method: "POST",
});

export const useIngresoProductos = () => {
    const setIngresoProducto = (item = null) => {
        if (item) {
            oIngresoProducto.value.id = item.id;
            oIngresoProducto.value.sucursal_id = item.sucursal_id;
            oIngresoProducto.value.fecha_registro = item.fecha_registro;
            oIngresoProducto.value.descripcion = item.descripcion;
            oIngresoProducto.value.ingreso_detalles = item.ingreso_detalles;
            oIngresoProducto.value.eliminados = [];
            oIngresoProducto.value._method = "PUT";
            return oIngresoProducto;
        }
        return false;
    };

    const limpiarIngresoProducto = () => {
        oIngresoProducto.value.id = 0;
        oIngresoProducto.value.sucursal_id = "";
        oIngresoProducto.value.fecha_registro = "";
        oIngresoProducto.value.descripcion = "";
        oIngresoProducto.value.ingreso_detalles = [];
        oIngresoProducto.value.eliminados = [];
        oIngresoProducto.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oIngresoProducto,
        setIngresoProducto,
        limpiarIngresoProducto,
    };
};
