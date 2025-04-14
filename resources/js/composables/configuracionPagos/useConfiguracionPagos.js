import { onMounted, ref } from "vue";

const oConfiguracionPago = ref({
    id: 0,
    nombre_banco: "",
    titular_cuenta: "",
    nro_cuenta: "",
    imagen_qr: "",
    _method: "POST",
});

export const useConfiguracionPagos = () => {
    const setConfiguracionPago = (item = null) => {
        if (item) {
            oConfiguracionPago.value.id = item.id;
            oConfiguracionPago.value.nombre_banco = item.nombre_banco;
            oConfiguracionPago.value.titular_cuenta = item.titular_cuenta;
            oConfiguracionPago.value.nro_cuenta = item.nro_cuenta;
            oConfiguracionPago.value._method = "PUT";
            return oConfiguracionPago;
        }
        return false;
    };

    const limpiarConfiguracionPago = () => {
        oConfiguracionPago.value.id = 0;
        oConfiguracionPago.value.nombre_banco = "";
        oConfiguracionPago.value.titular_cuenta = "";
        oConfiguracionPago.value.nro_cuenta = "";
        oConfiguracionPago.value.imagen_qr = "";
        oConfiguracionPago.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oConfiguracionPago,
        setConfiguracionPago,
        limpiarConfiguracionPago,
    };
};
