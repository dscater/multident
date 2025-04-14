import { onMounted, ref } from "vue";

const oRole = ref({
    id: 0,
    nombre: "",
    permisos: "",
    usuarios: "",
    _method: "POST",
});

export const useRoles = () => {
    const setRole = (item = null) => {
        if (item) {
            oRole.value.id = item.id;
            oRole.value.nombre = item.nombre;
            oRole.value.permisos = item.permisos;
            oRole.value.usuarios = item.usuarios;
            oRole.value._method = "PUT";
            return oRole;
        }
        return false;
    };

    const limpiarRole = () => {
        oRole.value.id = 0;
        oRole.value.nombre = "";
        oRole.value.permisos = "";
        oRole.value.usuarios = "";
        oRole.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oRole,
        setRole,
        limpiarRole,
    };
};
