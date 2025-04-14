import { onMounted, ref } from "vue";

const oProducto = ref({
    id: 0,
    nombre: "",
    descripcion: "",
    precio_pred: "",
    precio_min: "",
    precio_fac: "",
    precio_sf: "",
    stock_maximo: "",
    foto: null,
    _method: "POST",
});

export const useProductos = () => {
    const setProducto = (item = null) => {
        if (item) {
            oProducto.value.id = item.id;
            oProducto.value.nombre = item.nombre;
            oProducto.value.descripcion = item.descripcion;
            oProducto.value.precio_pred = item.precio_pred;
            oProducto.value.precio_min = item.precio_min;
            oProducto.value.precio_fac = item.precio_fac;
            oProducto.value.precio_sf = item.precio_sf;
            oProducto.value.stock_maximo = item.stock_maximo;
            oProducto.value._method = "PUT";
            return oProducto;
        }
        return false;
    };

    const limpiarProducto = () => {
        oProducto.value.id = 0;
        oProducto.value.nombre = "";
        oProducto.value.descripcion = "";
        oProducto.value.precio_pred = "";
        oProducto.value.precio_min = "";
        oProducto.value.precio_fac = "";
        oProducto.value.precio_sf = "";
        oProducto.value.stock_maximo = "";
        oProducto.value.foto = null;
        oProducto.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oProducto,
        setProducto,
        limpiarProducto,
    };
};
