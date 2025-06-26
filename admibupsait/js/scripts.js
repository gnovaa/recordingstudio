document.addEventListener('DOMContentLoaded', function() {
    // Автоматическое скрытие формы при загрузке страницы без редактирования
    if (!window.location.search.includes('edit=')) {
        document.querySelector('.crud-form-container').style.display = 'none';
    }

    // Кнопка "Добавить запись"
    const addButton = document.createElement('button');
    addButton.className = 'btn';
    addButton.textContent = 'Добавить запись';
    addButton.addEventListener('click', function() {
        const formContainer = document.querySelector('.crud-form-container');
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        document.getElementById('crudForm').reset();
        window.history.pushState({}, '', window.location.pathname + '?table=' + 
            new URLSearchParams(window.location.search).get('table'));
    });

    document.querySelector('.admin-header').appendChild(addButton);
});