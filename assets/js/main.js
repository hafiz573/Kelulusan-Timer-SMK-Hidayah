// Main JavaScript File for Kelulusan App

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Form validation
    initFormValidation();
    
    // Countdown animation
    initCountdownAnimation();
    
    // Print functionality
    initPrintFunctionality();
    
    // Password toggle
    initPasswordToggle();
});

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

// Countdown Animation
function initCountdownAnimation() {
    const countdownNumbers = document.querySelectorAll('.countdown-number');
    
    if (countdownNumbers.length > 0) {
        setInterval(() => {
            countdownNumbers.forEach(number => {
                number.classList.add('animate__animated', 'animate__pulse');
                
                setTimeout(() => {
                    number.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            });
        }, 3000);
    }
}

// Print Functionality
function initPrintFunctionality() {
    const printButtons = document.querySelectorAll('[onclick*="print"]');
    
    printButtons.forEach(button => {
        button.removeAttribute('onclick');
        button.addEventListener('click', function() {
            window.print();
        });
    });
}

// Password Toggle
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('[id^="toggle"]');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.id.replace('toggle', '').toLowerCase();
            const passwordInput = document.getElementById(inputId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

// Confirmation Dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Show Toast Notification
function showToast(type, message) {
    const toastContainer = document.getElementById('toast-container');
    
    if (!toastContainer) {
        createToastContainer();
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${getToastIcon(type)} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.innerHTML += toast;
    
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        case 'info': return 'info-circle';
        default: return 'bell';
    }
}

// Export Data Function
function exportToCSV(data, filename) {
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    csvContent += Object.keys(data[0]).join(',') + '\n';
    
    // Add data
    data.forEach(row => {
        csvContent += Object.values(row).join(',') + '\n';
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-hide alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Responsive table
function makeTableResponsive() {
    const tables = document.querySelectorAll('.table-responsive table');
    
    tables.forEach(table => {
        if (table.offsetWidth > table.parentElement.offsetWidth) {
            table.parentElement.classList.add('table-scroll');
        }
    });
}

// Initialize when window loads
window.addEventListener('load', function() {
    makeTableResponsive();
});

// Window resize handler
window.addEventListener('resize', makeTableResponsive);

// Custom confirmation for delete actions
document.addEventListener('click', function(e) {
    if (e.target.matches('.delete-btn') || e.target.closest('.delete-btn')) {
        e.preventDefault();
        const link = e.target.href || e.target.closest('a').href;
        const message = e.target.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
        
        if (confirm(message)) {
            window.location.href = link;
        }
    }
});

// Form auto-save (for long forms)
function initAutoSave(formId, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    let timeout;
    let isDirty = false;
    
    form.addEventListener('input', function() {
        isDirty = true;
        
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            if (isDirty) {
                saveFormData(form);
                isDirty = false;
            }
        }, interval);
    });
    
    // Save on page unload
    window.addEventListener('beforeunload', function(e) {
        if (isDirty) {
            saveFormData(form);
            e.preventDefault();
            e.returnValue = 'Data belum disimpan. Yakin ingin meninggalkan halaman?';
        }
    });
}

function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    localStorage.setItem('form_autosave_' + form.id, JSON.stringify(data));
    showToast('info', 'Data berhasil disimpan secara otomatis');
}

// Load auto-saved data
function loadAutoSave(formId) {
    const savedData = localStorage.getItem('form_autosave_' + formId);
    
    if (savedData) {
        const data = JSON.parse(savedData);
        const form = document.getElementById(formId);
        
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
            }
        });
        
        if (confirm('Ada data yang tersimpan otomatis. Ingin melanjutkan?')) {
            showToast('info', 'Data berhasil dimuat dari penyimpanan otomatis');
        }
    }
}

// Clear auto-saved data
function clearAutoSave(formId) {
    localStorage.removeItem('form_autosave_' + formId);
}