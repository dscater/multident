import { onMounted, ref } from "vue";

const oUbicacionProducto = ref({
    id: 0,
    lugar: "",
    numero_filas: 1,
    _method: "POST",
});

export const useUbicacionProductos = () => {
    const setUbicacionProducto = (item = null) => {
        if (item) {
            oUbicacionProducto.value.id = item.id;
            oUbicacionProducto.value.lugar = item.lugar;
            oUbicacionProducto.value.numero_filas = item.numero_filas;
            oUbicacionProducto.value._method = "PUT";
            return oUbicacionProducto;
        }
        return false;
    };

    const limpiarUbicacionProducto = () => {
        oUbicacionProducto.value.id = 0;
        oUbicacionProducto.value.lugar = "";
        oUbicacionProducto.value.numero_filas = 1;
        oUbicacionProducto.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oUbicacionProducto,
        setUbicacionProducto,
        limpiarUbicacionProducto,
    };
};
