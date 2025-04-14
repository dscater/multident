import { onMounted, ref } from "vue";

const oCategoria = ref({
    id: 0,
    nombre: "",
    permisos: "",
    usuarios: "",
    _method: "POST",
});

export const useCategorias = () => {
    const setCategoria = (item = null) => {
        if (item) {
            oCategoria.value.id = item.id;
            oCategoria.value.nombre = item.nombre;
            oCategoria.value.permisos = item.permisos;
            oCategoria.value.usuarios = item.usuarios;
            oCategoria.value._method = "PUT";
            return oCategoria;
        }
        return false;
    };

    const limpiarCategoria = () => {
        oCategoria.value.id = 0;
        oCategoria.value.nombre = "";
        oCategoria.value.permisos = "";
        oCategoria.value.usuarios = "";
        oCategoria.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oCategoria,
        setCategoria,
        limpiarCategoria,
    };
};
