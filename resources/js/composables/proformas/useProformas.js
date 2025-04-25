import { onMounted, ref } from "vue";

const oProforma = ref({
    id: 0,
    nro: "",
    sucursal_id: "",
    cliente_id: "",
    nit_ci: "",
    factura: "NO",
    fecha_validez: "",
    fecha_registro: "",
    detalle_proformas: [],
    eliminados: [],
    total: "0.00",
    _method: "POST",
});

export const useProformas = () => {
    const setProforma = (item = null) => {
        if (item) {
            oProforma.value.id = item.id;
            oProforma.value.nro = item.nro;
            oProforma.value.sucursal_id = item.sucursal_id;
            oProforma.value.cliente_id = item.cliente_id;
            oProforma.value.nit_ci = item.nit_ci;
            oProforma.value.factura = item.factura;
            oProforma.value.fecha_validez = item.fecha_validez;
            oProforma.value.fecha_registro = item.fecha_registro;
            oProforma.value.detalle_proformas = item.detalle_proformas;
            oProforma.value.eliminados = [];
            oProforma.value.total = item.total;
            oProforma.value._method = "PUT";
            return oProforma;
        }
        return false;
    };

    const limpiarProforma = () => {
        oProforma.value.id = 0;
        oProforma.value.nro = "";
        oProforma.value.sucursal_id = "";
        oProforma.value.cliente_id = "";
        oProforma.value.nit_ci = "";
        oProforma.value.factura = "NO";
        oProforma.value.fecha_validez = "EFECTIVO";
        oProforma.value.fecha_registro = "";
        oProforma.value.detalle_proformas = [];
        oProforma.value.eliminados = [];
        oProforma.value.total = "0.00";
        oProforma.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oProforma,
        setProforma,
        limpiarProforma,
    };
};
