let currentPage = 1;
const itemsPerPage = 10; // Number of items per page

// Fetch and display words
function fetchWords(page = 1, searchQuery = '') {
    fetch(`fetch_words.php?page=${page}&search=${searchQuery}`)
        .then(response => response.json())
        .then(data => {
            const wordList = document.getElementById('wordList');
            wordList.innerHTML = ''; // Clear existing rows

            data.words.forEach(word => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${word.word}</td>
                    <td>${word.definition}</td>
                    <td>${word.examples.replace(/\|/g, '<br>')}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editWord(${word.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteWord(${word.id})">Delete</button>
                    </td>
                `;
                wordList.appendChild(row);
            });

            // Update pagination
            updatePagination(data.totalPages, page);
        })
        .catch(error => console.error('Error fetching words:', error));
}

// Update pagination links
function updatePagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
        pagination.appendChild(li);
    }
}

// Change page
function changePage(page) {
    currentPage = page;
    const searchQuery = document.getElementById('searchInput').value;
    fetchWords(page, searchQuery);
}

// Search words
document.getElementById('searchInput').addEventListener('input', function () {
    const searchQuery = this.value;
    fetchWords(1, searchQuery); // Reset to page 1 when searching
});

// Add new word
document.getElementById('addWordForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('add_word.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(message => {
            alert(message);
            fetchWords(currentPage); // Refresh the word list
            this.reset(); // Clear the form
        })
        .catch(error => console.error('Error adding word:', error));
});

// Edit word (to be implemented)
function editWord(id) {
    alert('Edit word with ID: ' + id);
    // Implement edit functionality
}

// Delete word
function deleteWord(id) {
    if (confirm('Are you sure you want to delete this word?')) {
        fetch(`delete_word.php?id=${id}`, {
            method: 'DELETE'
        })
            .then(response => response.text())
            .then(message => {
                alert(message);
                fetchWords(currentPage); // Refresh the word list
            })
            .catch(error => console.error('Error deleting word:', error));
    }
}

// Fetch words on page load
fetchWords(currentPage);