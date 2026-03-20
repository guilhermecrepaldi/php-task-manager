document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('task-grid');

    if (!grid || grid.children.length === 0) return;

    // Check if only empty-state is present
    if (grid.querySelector('.empty-state')) return;

    new Sortable(grid, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function () {
            const order = [];
            document.querySelectorAll('.task-card').forEach(function (card) {
                order.push(card.dataset.id);
            });

            const formData = new FormData();
            order.forEach(function (id, index) {
                formData.append('ordenar[]', id);
            });

            fetch('reordenar.php', {
                method: 'POST',
                body: formData
            }).catch(function (err) {
                console.error('Erro ao reordenar:', err);
            });
        }
    });
});
