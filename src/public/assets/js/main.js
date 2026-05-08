// Smart Urban Parking – Main JS

document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss flash alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions (for elements with data-confirm attribute)
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // Auto-calculate price preview on booking form
    const startInput = document.querySelector('input[name="start_time"]');
    const endInput   = document.querySelector('input[name="end_time"]');
    const priceEl    = document.getElementById('price-preview');

    if (startInput && endInput && priceEl) {
        function updatePrice() {
            const s = new Date(startInput.value);
            const e = new Date(endInput.value);
            if (s && e && e > s) {
                const hours = (e - s) / 3600000;
                const rate  = parseFloat(priceEl.dataset.rate || 0);
                priceEl.textContent = 'Estimated: $' + (hours * rate).toFixed(2);
            }
        }
        startInput.addEventListener('change', updatePrice);
        endInput.addEventListener('change', updatePrice);
    }

    // Highlight current sidebar link
    const currentPage = new URLSearchParams(window.location.search).get('page');
    if (currentPage) {
        document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
            const linkPage = new URLSearchParams(link.search).get('page');
            if (linkPage === currentPage) {
                link.classList.add('active');
            }
        });
    }
});