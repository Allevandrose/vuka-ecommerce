import "./bootstrap";
import Alpine from "alpinejs";
import Swal from "sweetalert2";

window.Swal = Swal;

// Make Alpine available globally
window.Alpine = Alpine;

// Toast notification helper
window.showToast = function (message, type = "success") {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: type,
        title: message,
    });
};

// Handle flash messages
document.addEventListener("DOMContentLoaded", function () {
    const successMessage = document.querySelector('[data-swal="success"]');
    if (successMessage) {
        showToast(successMessage.dataset.message, "success");
    }

    const errorMessage = document.querySelector('[data-swal="error"]');
    if (errorMessage) {
        showToast(errorMessage.dataset.message, "error");
    }
});

// Register all Alpine stores/data BEFORE Alpine.start() fires `alpine:init`.
// Anything registered after Alpine.start() will never fire for the first boot.
document.addEventListener("alpine:init", () => {
    Alpine.store("navigation", {
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    });
});

// Start Alpine LAST, after every init listener is registered
Alpine.start();
