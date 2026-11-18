/**
 * Baseline IT Development - TaskFlow Dashboard Script
 * Pure jQuery Implementation
 */

$(document).ready(function () {
    'use strict';

    const $sidebar = $('#dashSidebar');
    const $backdrop = $('#sidebarBackdrop');
    const $toggleBtn = $('#sidebarToggleBtn');

    // ── Sidebar Toggle Functionality ──
    $toggleBtn.on('click', function (e) {
        e.preventDefault();
        $sidebar.toggleClass('show');
        $backdrop.toggleClass('show');
    });

    // Close on backdrop click (mobile)
    $backdrop.on('click', function () {
        $sidebar.removeClass('show');
        $backdrop.removeClass('show');
    });

    // Close on window resize if expanded on mobile
    $(window).on('resize', function () {
        if ($(window).width() > 991) {
            $sidebar.removeClass('show');
            $backdrop.removeClass('show');
        }
    });

    // ── Live Clock (Topnav) ──
    function updateClock() {
        const $clock = $('#liveClock');
        if ($clock.length) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            $clock.text(timeStr);
        }
    }
    updateClock();
    setInterval(updateClock, 30000);

    // ── Auto Dismiss Alerts ──
    setTimeout(function () {
        $('.alert.alert-dismissible').fadeOut(400, function () {
            $(this).remove();
        });
    }, 5000);
});
