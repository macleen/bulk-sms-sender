document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const mobileField = form.querySelector('input[name="mobile_number"]');
            if (mobileField) {
                const mobileNumber = mobileField.value.trim();
                const regex        = /^\+?[1-9]\d{1,14}$/; // E.164 format

                if (!mobileNumber) {
                    e.preventDefault();
                    show_alert('SMS-Bulk-Sender: Mobile number is required.');
                } else if (!regex.test(mobileNumber)) {
                    e.preventDefault();
                    show_alert('SMS-Bulk-Sender: Invalid mobile number format.');
                }
            }
        });
    });
});
