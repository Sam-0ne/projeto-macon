document.addEventListener('DOMContentLoaded', function() {
    var searchForm = document.querySelector('form[action="folders.php"]');
    var searchInput = document.querySelector('input[name="search"]');
    var searchResultsContainer = document.querySelector('.search-results tbody');

    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();

            var searchTerm = searchInput.value;

            fetch('folders.php?search=' + encodeURIComponent(searchTerm))
                .then(response => response.text())
                .then(html => {
                    searchResultsContainer.innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        });
    }

    var addClienteForm = document.querySelector('form[name="addClienteForm"]');
    var addPastaForm = document.querySelector('form[name="addPastaForm"]');

    if (addClienteForm) {
        addClienteForm.addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(addClienteForm);

            fetch('folders.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    alert('Cliente adicionado com sucesso');
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        });
    }

    if (addPastaForm) {
        addPastaForm.addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(addPastaForm);

            fetch('folders.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    alert('Pasta adicionada com sucesso');
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        });
    }
});
