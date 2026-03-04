import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

export const showSuccess = (message, title = 'Success') => {
    Toast.fire({
        icon: 'success',
        title: message,
        text: title,
    });
};

export const showError = (message, title = 'Error') => {
    Toast.fire({
        icon: 'error',
        title: message,
        text: title,
    });
};

export const showWarning = (message, title = 'Warning') => {
    Toast.fire({
        icon: 'warning',
        title: message,
        text: title,
    });
};

export const showInfo = (message, title = 'Info') => {
    Toast.fire({
        icon: 'info',
        title: message,
        text: title,
    });
};

export const showConfirm = (title, text, confirmText = 'Yes, delete it!', cancelText = 'Cancel') => {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
    });
};

export const showDeleteConfirm = (itemName) => {
    return showConfirm(
        'Are you sure?',
        `You want to delete "${itemName}". This action cannot be undone!`,
        'Yes, delete it!'
    );
};

// Export as default for backward compatibility
export default {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirm,
    showDeleteConfirm,
    fire: Swal.fire,
};
