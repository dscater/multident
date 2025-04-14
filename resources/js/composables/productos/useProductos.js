import { onMounted, ref } from "vue";

const oProducto = ref({
    id: 0,
    categoria_id: "",
    nombre: "",
    descripcion: "",
    stock_actual: "",
    precio_compra: "",
    precio_venta: "",
    observaciones: "",
    publico: "",
    imagens: [],
    eliminados_imagens: [],
    _method: "POST",
});

export const useProductos = () => {
    const setProducto = (item = null) => {
        if (item) {
            oProducto.value.id = item.id;
            oProducto.value.categoria_id = item.categoria_id;
            oProducto.value.nombre = item.nombre;
            oProducto.value.descripcion = item.descripcion;
            oProducto.value.stock_actual = item.stock_actual;
            oProducto.value.precio_compra = item.precio_compra;
            oProducto.value.precio_venta = item.precio_venta;
            oProducto.value.observaciones = item.observaciones;
            oProducto.value.publico = item.publico;
            oProducto.value.imagens = item.imagens;
            oProducto.value.eliminados_imagens = [];
            oProducto.value._method = "PUT";
            return oProducto;
        }
        return false;
    };

    const limpiarProducto = () => {
        oProducto.value.id = 0;
        oProducto.value.categoria_id = "";
        oProducto.value.nombre = "";
        oProducto.value.descripcion = "";
        oProducto.value.stock_actual = "";
        oProducto.value.precio_compra = "";
        oProducto.value.precio_venta = "";
        oProducto.value.observaciones = "";
        oProducto.value.publico = "";
        oProducto.value.imagens = [];
        oProducto.value.eliminados_imagens = [];
        oProducto.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oProducto,
        setProducto,
        limpiarProducto,
    };
};
