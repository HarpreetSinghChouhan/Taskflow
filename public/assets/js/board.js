/**
 * Kanban Board — Drag & Drop Logic
 * TaskFlow | Baseline IT Development
 * Depends on: SortableJS (loaded before this script)
 */

(function () {
    'use strict';

    const csrf = document.getElementById('kbBoard')?.dataset.csrf ?? '';

    /* ── Helpers ──────────────────────────────────────────────── */

    /** Re-sort cards inside a column by priority → due date → id */
    function sortColumn(list) {
        [...list.querySelectorAll('.kb-card')]
            .sort((a, b) =>
                (a.dataset.rank - b.dataset.rank) ||
                (a.dataset.due  - b.dataset.due)  ||
                (a.dataset.id   - b.dataset.id))
            .forEach(card => list.appendChild(card));
    }

    /** Refresh the task-count badge of every column */
    function updateCounts() {
        document.querySelectorAll('.kb-col-body').forEach(list => {
            const count = list.querySelectorAll('.kb-card').length;
            list.closest('.kb-col').querySelector('.kb-col-count').textContent = count;
        });
    }

    /** Show or hide the "No tasks" placeholder for a single column */
    function refreshEmpty(list) {
        const hasCards = list.querySelectorAll('.kb-card').length > 0;
        const empty    = list.querySelector('.kb-empty');
        if (empty) empty.style.display = hasCards ? 'none' : 'flex';
    }

    /* ── Initialise ───────────────────────────────────────────── */

    // Sync placeholder visibility on first load
    document.querySelectorAll('.kb-col-body').forEach(refreshEmpty);

    // Attach SortableJS to every column
    document.querySelectorAll('.kb-col-body').forEach(list => {
        new Sortable(list, {
            group:      'tasks',
            sort:       false,          // ordering within a column is automatic
            animation:  150,
            ghostClass: 'kb-ghost',

            onAdd    (evt) { refreshEmpty(evt.to);   },
            onRemove (evt) { refreshEmpty(evt.from); },

            onEnd(evt) {
                if (evt.from === evt.to) return;    // dropped back in the same column

                const card = evt.item;

                fetch(card.dataset.url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  csrf,
                    },
                    body: JSON.stringify({ status: evt.to.dataset.status }),
                })
                .then(res => { if (!res.ok) throw new Error('Server error'); })
                .then(() => {
                    sortColumn(evt.to);
                    updateCounts();
                    refreshEmpty(evt.to);
                    refreshEmpty(evt.from);
                })
                .catch(() => {
                    // Rollback: put the card back where it came from
                    evt.from.appendChild(card);
                    sortColumn(evt.from);
                    updateCounts();
                    refreshEmpty(evt.to);
                    refreshEmpty(evt.from);
                    alert('Could not move the task. Please try again.');
                });
            },
        });
    });

}());
