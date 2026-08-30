import "./bootstrap";
import Alpine from "alpinejs";
import Swal from "sweetalert2";

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

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
    // Check for success messages
    const successMessage = document.querySelector('[data-swal="success"]');
    if (successMessage) {
        showToast(successMessage.dataset.message, "success");
    }

    // Check for error messages
    const errorMessage = document.querySelector('[data-swal="error"]');
    if (errorMessage) {
        showToast(errorMessage.dataset.message, "error");
    }
});

// Handle Alpine dropdown toggle for mobile
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
