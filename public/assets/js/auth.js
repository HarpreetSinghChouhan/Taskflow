/**
 * Baseline IT Development - TaskFlow Auth Scripts
 * Enterprise jQuery Implementation
 */

$(document).ready(function () {
    'use strict';

    // ── Cache Selectors ──
    const $loginForm        = $('#loginForm');
    const $emailInput       = $('#email');
    const $passwordInput    = $('#password');
    const $toggleBtn        = $('#togglePasswordBtn');
    const $toggleIcon       = $('#togglePasswordIcon');
    const $submitBtn        = $('#submitBtn');
    const $btnText          = $('#btnText');
    const $btnIcon          = $('#btnIcon');
    const $btnSpinner       = $('#btnSpinner');
    const $autofillToast    = $('#autofillToast');
    const $autofillRoleName = $('#autofillRoleName');

    // ── Password Visibility Toggle ──
    $toggleBtn.on('click', function (e) {
        e.preventDefault();
        const isPassword = $passwordInput.attr('type') === 'password';

        $passwordInput.attr('type', isPassword ? 'text' : 'password');
        $toggleIcon.toggleClass('bi-eye', !isPassword)
                   .toggleClass('bi-eye-slash', isPassword);

        $passwordInput.trigger('focus');
    });

    // ── Quick Demo Autofill (Data Attribute Driven) ──
    $('.demo-role-btn').on('click', function (e) {
        e.preventDefault();

        const email    = $(this).data('email');
        const password = $(this).data('password');
        const role     = $(this).data('role');

        if (!email || !password) return;

        // Populate fields
        $emailInput.val(email);
        $passwordInput.val(password);

        // Visual flash feedback on inputs
        $emailInput.add($passwordInput)
            .css('background-color', '#EFF6FF')
            .delay(350)
            .queue(function (next) {
                $(this).css('background-color', '#FFFFFF');
                next();
            });

        // Show confirmation toast with role info
        if ($autofillToast.length) {
            $autofillRoleName.text(`${role} (${email})`);
            $autofillToast.stop(true, true).hide().removeClass('d-none').addClass('d-flex').fadeIn(250);
        }

        // Focus submit button ready for user
        $submitBtn.trigger('focus');
    });

    // ── Form Submit Loading State ──
    $loginForm.on('submit', function () {
        $submitBtn.prop('disabled', true);
        $btnText.text('Authenticating...');
        $btnIcon.addClass('d-none');
        $btnSpinner.removeClass('d-none');
    });

    // ── Auto-hide status notifications after 5 seconds ──
    setTimeout(function () {
        $('.alert.alert-dismissible').fadeOut(400, function () {
            $(this).remove();
        });
    }, 5000);
});
