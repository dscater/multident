import { onMounted, ref } from "vue";

const oSucursal = ref({
    id: 0,
    codigo: "",
    nombre: "",
    direccion: "",
    fonos: "",
    user_id: "",
    fecha_registro: "",
    _method: "POST",
});

export const useSucursals = () => {
    const setSucursal = (item = null) => {
        if (item) {
            oSucursal.value.id = item.id;
            oSucursal.value.codigo = item.codigo;
            oSucursal.value.nombre = item.nombre;
            oSucursal.value.direccion = item.direccion;
            oSucursal.value.fonos = item.fonos;
            oSucursal.value.user_id = item.user_id;
            oSucursal.value.fecha_registro = item.fecha_registro;
            oSucursal.value._method = "PUT";
            return oSucursal;
        }
        return false;
    };

    const limpiarSucursal = () => {
        oSucursal.value.id = 0;
        oSucursal.value.codigo = "";
        oSucursal.value.nombre = "";
        oSucursal.value.direccion = "";
        oSucursal.value.fonos = "";
        oSucursal.value.user_id = "";
        oSucursal.value.fecha_registro = "";
        oSucursal.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oSucursal,
        setSucursal,
        limpiarSucursal,
    };
};
