document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalImagen");
    if (modal) {
        const modalImg = document.getElementById("imgExpandida");
        const cerrar = modal.querySelector(".cerrar");

        document.querySelectorAll(".img-expandable").forEach(img => {
            img.addEventListener("click", function () {
                modal.style.display = "block";
                modalImg.src = this.src;
            });
        });

        /* Cerrar modal */
        cerrar.addEventListener("click", function () {
            modal.style.display = "none";
        });

        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    }
});
