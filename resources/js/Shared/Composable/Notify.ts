import Swal, { SweetAlertIcon } from "sweetalert2";

class Notifier {
    private static instance: Notifier | null = null;
    private swal = Swal;

    // Fondo oscuro consistente con tu Dashboard
    private readonly DEFAULT_BG = "#0a1f1a";

    private constructor() {
        // El constructor es privado para forzar el uso del Singleton
    }

    /**
     * Obtiene la instancia única del notificador
     */
    public static getInstance(): Notifier {
        if (!this.instance) {
            this.instance = new Notifier();
        }
        return this.instance;
    }

    private fire(title: string, message: string, icon: SweetAlertIcon, color: string) {
        this.swal.fire({
            title: title,
            text: message,
            icon: icon,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            color: color,
            iconColor: color,
            background: this.DEFAULT_BG,
        });
    }

    private toast(message: string, icon: SweetAlertIcon, background: string, title: string) {
        this.swal.fire({
            title: title,
            text: message,
            icon: icon,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            color: "#ecf0f1",
            iconColor: "#ecf0f1",
            background: background,
            toast: true,
            position: "top",
        });
    }

    // --- Notificaciones Normales ---

    public success(title: string, message: string) {
        this.fire(title, message, "success", "#1abc9c");
    }

    public error(title: string, message: string) {
        this.fire(title, message, "error", "#e74c3c");
    }

    public warning(title: string, message: string) {
        this.fire(title, message, "warning", "#ffc107");
    }

    public info(title: string, message: string) {
        this.fire(title, message, "info", "#007bff");
    }

    public question(title: string, message: string) {
        this.fire(title, message, "question", "#2c3e50");
    }

    public loading(title: string, message: string) {
        this.swal.fire({
            title: title,
            text: message,
            color: "#fff",
            showConfirmButton: false,
            allowOutsideClick: false,
            background: this.DEFAULT_BG,
            didOpen: () => {
                this.swal.showLoading();
            },
        });
    }

    // --- Toasts ---

    public toastSuccess(message: string) {
        this.toast(message, "success", "#1abc9c", "Éxito");
    }

    public toastError(message: string) {
        this.toast(message, "error", "#e74c3c", "Error");
    }

    public toastWarning(message: string) {
        this.toast(message, "warning", "#ffc107", "Advertencia");
    }

    public toastInfo(message: string) {
        this.toast(message, "info", "#007bff", "Info");
    }

    public closeLoading() {
        this.swal.close();
    }
}

// Hook para usar en tus componentes Vue
export const useNotifier = (): Notifier => {
    return Notifier.getInstance();
};
