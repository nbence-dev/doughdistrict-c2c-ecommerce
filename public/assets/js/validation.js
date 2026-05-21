/* DoughDistrict — client-side form validation.
 *
 * Opt in by adding the `data-validate` attribute to a <form>; every form so
 * marked is checked on submit (and each field re-checked on blur/change).
 * Rules are read from standard attributes so no per-form config is needed:
 *   required            -> must not be empty
 *   type="email"        -> must look like an email address
 *   type="tel"          -> must be a valid South African phone number
 *   type="number"       -> numeric, respecting min / max
 *   minlength / data-minlength -> minimum character count
 *   data-rule="postal"  -> 4-digit South African postal code
 *   data-match="<id>"   -> must equal the value of another field
 *
 * Framework-agnostic (works on the Bootstrap and Tailwind pages alike) and
 * dependency-free. Exposes window.DDValidate for forms that need to drive
 * validation manually (e.g. the Stripe checkout form).
 */
(function () {
    'use strict';

    var style = document.createElement('style');
    style.textContent =
        '.dd-invalid{outline:2px solid #ba1a1a !important;outline-offset:1px;border-color:#ba1a1a !important;}' +
        '.dd-error-msg{color:#ba1a1a;font-size:.8rem;margin-top:.25rem;display:block;}';
    document.head.appendChild(style);

    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var PHONE_RE = /^(\+?27|0)[0-9]{9}$/;
    var POSTAL_RE = /^[0-9]{4}$/;

    function fieldLabel(field) {
        if (field.dataset.label) return field.dataset.label;
        if (field.id) {
            var l = document.querySelector('label[for="' + field.id + '"]');
            if (l) {
                return l.textContent
                    .replace(/\([^)]*\)/g, '')
                    .replace(/[:*]/g, '')
                    .trim() || 'This field';
            }
        }
        if (field.getAttribute('aria-label')) return field.getAttribute('aria-label');
        return 'This field';
    }

    // Password fields and number fields are often wrapped for layout; place the
    // error message after the wrapper so it lands below the whole control.
    function anchorFor(field) {
        var p = field.parentElement;
        if (p && (p.classList.contains('pw-wrapper') || p.classList.contains('relative'))) {
            return p;
        }
        return field;
    }

    function clearError(field) {
        field.classList.remove('dd-invalid');
        var next = anchorFor(field).nextElementSibling;
        if (next && next.classList.contains('dd-error-msg')) next.remove();
    }

    function showError(field, message) {
        clearError(field);
        field.classList.add('dd-invalid');
        var msg = document.createElement('span');
        msg.className = 'dd-error-msg';
        msg.textContent = message;
        anchorFor(field).insertAdjacentElement('afterend', msg);
    }

    function fieldError(field, form) {
        var type = (field.type || '').toLowerCase();
        var raw = field.value || '';
        var value = raw.trim();
        var name = fieldLabel(field);
        var required = field.hasAttribute('required');

        if (type === 'radio') {
            var group = form.querySelectorAll(
                'input[type="radio"][name="' + CSS.escape(field.name) + '"]'
            );
            var anyChecked = false;
            group.forEach(function (r) { if (r.checked) anyChecked = true; });
            return (required && !anyChecked) ? 'Please select an option.' : '';
        }
        if (type === 'checkbox') {
            return (required && !field.checked) ? 'Please tick this box to continue.' : '';
        }
        if (type === 'file') {
            return (required && field.files.length === 0) ? 'Please choose a file.' : '';
        }

        if (required && value === '') return name + ' is required.';
        if (value === '') return '';

        if (type === 'email' && !EMAIL_RE.test(value)) {
            return 'Enter a valid email address.';
        }
        if (type === 'tel' && !PHONE_RE.test(value.replace(/[\s-]/g, ''))) {
            return 'Enter a valid South African phone number (e.g. 0821234567).';
        }
        if (field.dataset.rule === 'postal' && !POSTAL_RE.test(value)) {
            return 'Enter a valid 4-digit postal code.';
        }
        if (type === 'number') {
            var num = Number(value);
            if (isNaN(num)) return 'Enter a valid number.';
            if (field.hasAttribute('min') && num < parseFloat(field.min)) {
                return name + ' must be at least ' + field.min + '.';
            }
            if (field.hasAttribute('max') && num > parseFloat(field.max)) {
                return name + ' must be no more than ' + field.max + '.';
            }
        }

        var minLen = field.getAttribute('minlength') || field.dataset.minlength;
        if (minLen && value.length < parseInt(minLen, 10)) {
            return name + ' must be at least ' + minLen + ' characters.';
        }

        if (field.dataset.match) {
            var other = document.getElementById(field.dataset.match);
            if (other && raw !== other.value) {
                return field.dataset.matchMsg || 'The values do not match.';
            }
        }
        return '';
    }

    function validatableFields(form) {
        return Array.prototype.filter.call(
            form.querySelectorAll('input, select, textarea'),
            function (f) {
                var t = (f.type || '').toLowerCase();
                return t !== 'hidden' && t !== 'submit' && t !== 'button' &&
                    t !== 'reset' && !f.disabled;
            }
        );
    }

    function validateField(field, form) {
        form = form || field.form;
        if (!form) return true;
        var error = fieldError(field, form);
        if (error) {
            showError(field, error);
            return false;
        }
        clearError(field);
        return true;
    }

    function validateForm(form) {
        var valid = true;
        var firstInvalid = null;
        var seenRadioGroups = {};
        validatableFields(form).forEach(function (field) {
            if (field.type === 'radio') {
                if (seenRadioGroups[field.name]) return;
                seenRadioGroups[field.name] = true;
            }
            if (!validateField(field, form)) {
                valid = false;
                if (!firstInvalid) firstInvalid = field;
            }
        });
        if (firstInvalid) firstInvalid.focus();
        return valid;
    }

    function attach(form) {
        if (form.dataset.ddBound) return;
        form.dataset.ddBound = '1';
        form.setAttribute('novalidate', 'novalidate');

        form.addEventListener('submit', function (e) {
            if (!validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        validatableFields(form).forEach(function (field) {
            var liveEvent = (field.tagName === 'SELECT' || field.type === 'radio' ||
                field.type === 'checkbox' || field.type === 'file') ? 'change' : 'blur';
            field.addEventListener(liveEvent, function () {
                validateField(field, form);
            });
            field.addEventListener('input', function () {
                if (field.classList.contains('dd-invalid')) validateField(field, form);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-validate]').forEach(attach);
    });

    window.DDValidate = {
        attach: attach,
        validateForm: validateForm,
        validateField: validateField,
        showError: showError,
        clearError: clearError
    };
})();
