import { defineStore } from "pinia";

export const useSolicitudProductoStore = defineStore("solicitudProducto", {
    state: () => ({
        solicitudProducto: {},
    }),
    actions: {
        setSolicitudProducto(value) {
            this.solicitudProducto = value;
        },
    },
});
