import { onMounted, ref } from "vue";

const oOrdenVenta = ref({
    id: 0,
    nro: "",
    sucursal_id: "",
    cliente_id: "",
    nit_ci: "",
    factura: "NO",
    tipo_pago: "EFECTIVO",
    fecha_registro: "",
    detalle_ordens: [],
    eliminados: [],
    descripcion: "",
    total: "0.00",
    _method: "POST",
});

export const useOrdenVentas = () => {
    const setOrdenVenta = (item = null) => {
        if (item) {
            oOrdenVenta.value.id = item.id;
            oOrdenVenta.value.nro = item.nro;
            oOrdenVenta.value.sucursal_id = item.sucursal_id;
            oOrdenVenta.value.cliente_id = item.cliente_id;
            oOrdenVenta.value.nit_ci = item.nit_ci;
            oOrdenVenta.value.factura = item.factura;
            oOrdenVenta.value.tipo_pago = item.tipo_pago;
            oOrdenVenta.value.fecha_registro = item.fecha_registro;
            oOrdenVenta.value.detalle_ordens = item.detalle_ordens;
            oOrdenVenta.value.descripcion = item.descripcion;
            oOrdenVenta.value.eliminados = [];
            oOrdenVenta.value.total = item.total;
            oOrdenVenta.value._method = "PUT";
            return oOrdenVenta;
        }
        return false;
    };

    const limpiarOrdenVenta = () => {
        oOrdenVenta.value.id = 0;
        oOrdenVenta.value.nro = "";
        oOrdenVenta.value.sucursal_id = "";
        oOrdenVenta.value.cliente_id = "";
        oOrdenVenta.value.nit_ci = "";
        oOrdenVenta.value.factura = "NO";
        oOrdenVenta.value.tipo_pago = "EFECTIVO";
        oOrdenVenta.value.fecha_registro = "";
        oOrdenVenta.value.detalle_ordens = [];
        oOrdenVenta.value.descripcion = [];
        oOrdenVenta.value.eliminados = [];
        oOrdenVenta.value.total = "0.00";
        oOrdenVenta.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oOrdenVenta,
        setOrdenVenta,
        limpiarOrdenVenta,
    };
};
