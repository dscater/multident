import { onMounted, ref } from "vue";

const oUsuario = ref({
    id: 0,
    usuario: "",
    nombres: "",
    paterno: "",
    materno: "",
    ci: "",
    ci_exp: "",
    correo: "",
    password: "",
    role_id: "",
    sedes_todo: "",
    sucursal_id: "todos",
    foto: "",
    fecha_registro: "",
    acceso: 0 + "",
    status: "",
    origen: "admin",
    _method: "POST",
});

export const useUsuarios = () => {
    const setUsuario = (item = null, cliente = false, origen = "") => {
        if (item) {
            oUsuario.value.id = item.id;
            oUsuario.value.usuario = item.usuario;
            oUsuario.value.nombres = item.nombres;
            oUsuario.value.paterno = item.paterno;
            oUsuario.value.materno = item.materno;
            oUsuario.value.ci = item.ci;
            oUsuario.value.ci_exp = item.ci_exp;
            oUsuario.value.correo = item.correo;
            oUsuario.value.password = item.password;
            oUsuario.value.role_id = item.role_id;
            oUsuario.value.sedes_todo = item.sedes_todo;
            oUsuario.value.sucursal_id =
                item.sucursals_todo == 1 ? "todos" : item.sucursal_id;
            oUsuario.value.foto = "";
            oUsuario.value.fecha_registro = item.fecha_registro;
            oUsuario.value.acceso = item.acceso;
            oUsuario.value.status = item.status;
            if (cliente) {
                oUsuario.value.cliente = item.cliente;
            }

            if (origen) {
                oUsuario.value.origen = origen;
            }
            oUsuario.value._method = "PUT";
            return oUsuario;
        }
        return false;
    };

    const limpiarUsuario = () => {
        oUsuario.value.id = 0;
        oUsuario.value.usuario = "";
        oUsuario.value.nombres = "";
        oUsuario.value.paterno = "";
        oUsuario.value.materno = "";
        oUsuario.value.ci = "";
        oUsuario.value.ci_exp = "";
        oUsuario.value.correo = "";
        oUsuario.value.password = "";
        oUsuario.value.role_id = "";
        oUsuario.value.sedes_todo = "";
        oUsuario.value.sucursal_id = "todos";
        oUsuario.value.foto = "";
        oUsuario.value.fecha_registro = "";
        oUsuario.value.acceso = 0 + "";
        oUsuario.value.status = "";
        oUsuario.value.origen = "admin";
        oUsuario.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oUsuario,
        setUsuario,
        limpiarUsuario,
    };
};
