import { onMounted, ref } from "vue";

const oCliente = ref({
    id: 0,
    nombres: "",
    apellidos: "",
    ci: "",
    cel: "",
    descripcion: "",
    _method: "POST",
});

export const useClientes = () => {
    const setCliente = (item = null) => {
        if (item) {
            oCliente.value.id = item.id;
            oCliente.value.nombres = item.nombres;
            oCliente.value.apellidos = item.apellidos;
            oCliente.value.ci = item.ci;
            oCliente.value.cel = item.cel;
            oCliente.value.descripcion = item.descripcion;
            oCliente.value._method = "PUT";
            return oCliente;
        }
        return false;
    };

    const limpiarCliente = () => {
        oCliente.value.id = 0;
        oCliente.value.nombres = "";
        oCliente.value.apellidos = "";
        oCliente.value.ci = "";
        oCliente.value.cel = "";
        oCliente.value.descripcion = "";
        oCliente.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oCliente,
        setCliente,
        limpiarCliente,
    };
};
