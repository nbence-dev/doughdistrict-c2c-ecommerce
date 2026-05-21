document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');
    if (!form) return;

    const pk = form.dataset.pk;
    const secrets = JSON.parse(form.dataset.secrets);
    const errorDiv = document.getElementById('card-errors');
    const payBtn = document.getElementById('pay-btn');
    const originalBtnHtml = payBtn.innerHTML;

    const stripe = Stripe(pk);
    const elements = stripe.elements();
    const card = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                fontFamily: '"Be Vietnam Pro", sans-serif',
                fontSize: '16px',
                color: '#1b1c17',
                '::placeholder': { color: '#83746b' },
            },
            invalid: { color: '#ba1a1a' },
        },
    });
    card.mount('#card-element');

    card.on('change', function (event) {
        errorDiv.textContent = event.error ? event.error.message : '';
    });

    // Address section toggle
    const newToggle = document.getElementById('new-address-toggle');
    const newFields = document.getElementById('new-address-fields');
    if (newToggle && newFields) {
        document.querySelectorAll('input[name="address_id"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.value === '0') {
                    newFields.classList.remove('d-none');
                } else {
                    newFields.classList.add('d-none');
                }
            });
        });
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorDiv.textContent = '';

        // Validate a freshly entered shipping address before charging the card
        const addressRadio = form.querySelector('input[name="address_id"]:checked')
            || form.querySelector('input[name="address_id"]');
        const usingNewAddress = addressRadio && addressRadio.value === '0';
        if (usingNewAddress && window.DDValidate) {
            let addressOk = true;
            ['street', 'local_area', 'city', 'province', 'postal_code'].forEach(function (id) {
                const field = document.getElementById(id);
                if (field && !window.DDValidate.validateField(field, form)) addressOk = false;
            });
            if (!addressOk) {
                const firstBad = form.querySelector('.dd-invalid');
                if (firstBad) firstBad.focus();
                return;
            }
        }

        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing…';

        let paymentMethodId = null;

        for (const secret of secrets) {
            // Reuse the payment method after the first confirmation to avoid re-prompting 3DS
            const confirmData = paymentMethodId
                ? { payment_method: paymentMethodId }
                : { payment_method: { card: card } };

            const { error, paymentIntent } = await stripe.confirmCardPayment(secret, confirmData);

            if (error) {
                errorDiv.textContent = error.message;
                payBtn.disabled = false;
                payBtn.innerHTML = originalBtnHtml;
                return;
            }

            if (!paymentMethodId) {
                paymentMethodId = paymentIntent.payment_method;
            }
        }

        // All payment intents confirmed — submit to server to record orders
        form.submit();
    });
});
