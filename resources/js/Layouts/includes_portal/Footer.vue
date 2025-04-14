<script setup>
import { usePage, Head, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import { useOrdenVentaStore } from "@/stores/ordenVenta/ordenVentaStore";
const { oConfiguracion } = useConfiguracion();
const ordenVentaStore = useOrdenVentaStore();
const { props: props_page } = usePage();
const user = ref(props_page.auth?.user);
</script>
<template>
    <!-- BEGIN #footer -->
    <div id="footer" class="footer">
        <!-- BEGIN container -->
        <div class="container">
            <!-- BEGIN row -->
            <div class="row">
                <!-- BEGIN col-4 -->
                <div class="col-lg-4">
                    <p class="font-weight-bold text-md"><i class="fa fa-map-marked-alt"></i> Direcciones</p>
                    <div
                        v-html="oConfiguracion.dir"
                        :style="{ whiteSpace: 'pre-line' }"
                    ></div>
                </div>
                <!-- END col-4 -->
                <!-- BEGIN col-4 -->
                <div class="col-lg-4 text-center">
                    <h2 class="font-weight-bold">
                        {{ oConfiguracion.alias }}
                    </h2>
                    <ul class="fa-ul mb-lg-4 mb-0 p-0 text-left">
                        <li>
                            <i class="fa fa-fw fa-angle-right"></i>
                            <Link
                                class="text-white"
                                :href="route('portal.index')"
                                >Inicio</Link
                            >
                        </li>
                        <li>
                            <i class="fa fa-fw fa-angle-right"></i>
                            <Link
                                class="text-white"
                                :href="route('portal.productos')"
                                >Productos</Link
                            >
                        </li>
                        <li v-if="user && user.role_id == 2">
                            <i class="fa fa-fw fa-angle-right"></i>
                            <Link
                                class="text-white"
                                :href="route('portal.misSolicitudes')"
                                >Mis solicitudes</Link
                            >
                        </li>
                        <li>
                            <i class="fa fa-fw fa-angle-right"></i>
                            <Link
                                :href="route('portal.miCarrito')"
                                class="text-white"
                                >Mi carrito
                            </Link>
                        </li>
                    </ul>
                </div>
                <!-- END col-4 -->
                <!-- BEGIN col-4 -->
                <div class="col-lg-4">
                    <p class="font-weight-bold text-md"><i class="fa fa-phone"></i> Teléfono Principal</p>
                    <div
                        style="font-size: 1.4rem"
                        class="font-weight-bold"
                        :style="{ whiteSpace: 'pre-line' }"
                    >
                        <span v-html="` ${oConfiguracion.fono}`"></span>
                    </div>
                </div>
                <!-- END col-4 -->
            </div>
            <!-- END row -->
        </div>
        <!-- END container -->
    </div>
    <!-- END #footer -->
</template>
<style scoped>
.img_logo {
    margin-left: -130px;
    width: 370px;
}

.footer {
    color: white;
    background-color: var(--principal);
    box-shadow: none;
}

@media (max-width: 900px) {
    .img_logo {
        margin-left: 0px;
        width: 270px;
    }
}
</style>
