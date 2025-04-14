import { defineStore } from "pinia";

export const useOrdenVentaStore = defineStore("ordenVenta", {
    state: () => ({
        ordenVenta: {},
        carrito: [],
        producto: null,
    }),
    actions: {
        initCarrito() {
            const detalleVenta = localStorage.getItem("detalleVenta");
            if (detalleVenta) {
                this.carrito = JSON.parse(detalleVenta);
            }
        },
        getTotalProductos() {
            return this.carrito.length;
        },
        getSumaTotalCarrito() {
            let sumaTotal = this.carrito.reduce(
                (acum, current) => acum + current.subtotal,
                0
            );

            return parseFloat(sumaTotal).toFixed(2);
        },
        getCarrito() {
            return this.carrito;
        },
        setOrdenVenta(value) {
            this.ordenVenta = value;
        },
        getProductoCarrito(producto_id) {
            this.produto = null;
            const indice = this.carrito.findIndex(
                (item) => item.producto_id === producto_id
            );
            if (indice > -1) {
                this.producto = this.carrito[indice];
            }
            return this.producto;
        },
        addProducto(item) {
            const indice = this.carrito.findIndex(
                (elem) => elem.producto_id === item.producto_id
            );
            if (indice > -1) {
                this.carrito[indice].cantidad += parseInt(item.cantidad);
                this.carrito[indice].subtotal =
                    parseInt(this.carrito[indice].cantidad) *
                    parseFloat(this.carrito[indice].precio);
            } else {
                item.subtotal =
                    parseInt(item.cantidad) * parseFloat(item.precio);
                this.carrito.push(item);
            }
            localStorage.setItem("detalleVenta", JSON.stringify(this.carrito));
        },
        deleteProducto(index) {
            this.carrito.splice(index, 1);
            localStorage.setItem("detalleVenta", JSON.stringify(this.carrito));
            return this.carrito;
        },
        editQuantity(index, value) {
            if (value > 0) {
                this.carrito[index].cantidad = parseInt(value);
            } else {
                this.carrito[index].cantidad = 1;
            }
            this.carrito[index].subtotal =
                parseInt(this.carrito[index].cantidad) *
                parseFloat(this.carrito[index].precio);

            localStorage.setItem("detalleVenta", JSON.stringify(this.carrito));
            return this.carrito;
        },
        limpiarCarrito() {
            this.carrito = [];
            localStorage.removeItem("detalleVenta");
        },
    },
});
