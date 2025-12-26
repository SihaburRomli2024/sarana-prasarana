// FILE: assets/js/script.js
// JavaScript untuk Aplikasi Sarana Prasarana

// ============================================
// MENU TOGGLE (Mobile)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            if (!event.target.closest('.sidebar') && !event.target.closest('.menu-toggle')) {
                sidebar.classList.remove('active');
            }
        }
    });
});

// ============================================
// CHART INITIALIZATION (Dashboard)
// ============================================

function initKondisiChart(data) {
    const ctx = document.getElementById('kondisiChart');
    if (!ctx) return;
    
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
            datasets: [{
                data: data,
                backgroundColor: [
                    '#27ae60',
                    '#f39c12',
                    '#e74c3c'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// ============================================
// FORM VALIDATION
// ============================================

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// ============================================
// CONFIRM DELETE
// ============================================

function confirmDelete(id, name = '') {
    const message = name ? `Apakah Anda yakin ingin menghapus "${name}"?` : 'Apakah Anda yakin ingin menghapus data ini?';
    return confirm(message);
}

// ============================================
// SEARCH & FILTER
// ============================================

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// ============================================
// FORMAT CURRENCY
// ============================================

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
}

// ============================================
// ALERT NOTIFICATION
// ============================================

function showAlert(message, type = 'info') {
    const alertClass = `alert-${type}`;
    const alertHtml = `<div class="alert ${alertClass}" role="alert">${message}</div>`;
    
    const pageContent = document.querySelector('.page-content');
    if (pageContent) {
        pageContent.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = pageContent.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// ============================================
// EXCEL EXPORT
// ============================================

function exportTableToExcel(tableId, filename = 'export.xlsx') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let html = "<table border='1'>";
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        html += "<tr>";
        const cells = row.querySelectorAll('th, td');
        cells.forEach(cell => {
            html += "<td>" + cell.textContent + "</td>";
        });
        html += "</tr>";
    });
    
    html += "</table>";
    
    const blob = new Blob([html], { type: 'application/ms-excel' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
}

// ============================================
// PRINT
// ============================================

function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const win = window.open();
    win.document.write(table.outerHTML);
    win.print();
    win.close();
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Get URL parameter
function getUrlParameter(name) {
    const url = new URL(window.location.href);
    return url.searchParams.get(name);
}

// Debounce function for search
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

// Show/Hide loading spinner
function showLoading(show = true) {
    const spinner = document.querySelector('.spinner');
    if (spinner) {
        spinner.style.display = show ? 'block' : 'none';
    }
}

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Highlight active menu item
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.sidebar-menu .menu-item');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            item.classList.add('active');
        }
    });
});