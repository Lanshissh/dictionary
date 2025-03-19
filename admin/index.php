<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dictionary App</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Admin Dashboard</h1>
        <div class="row">
            <div class="col-md-8">
                <h2>Word List</h2>
                <!-- Search Bar -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search words...">
                </div>
                <!-- Word Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Word</th>
                            <th>Definition</th>
                            <th>Examples</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="wordList">
                        <!-- Word rows will be dynamically inserted here -->
                    </tbody>
                </table>
                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination links will be dynamically inserted here -->
                    </ul>
                </nav>
            </div>
            <div class="col-md-4">
                <h2>Add New Word</h2>
                <form id="addWordForm">
                    <div class="mb-3">
                        <label for="word" class="form-label">Word</label>
                        <input type="text" id="word" name="word" class="form-control" placeholder="Enter word" required>
                    </div>
                    <div class="mb-3">
                        <label for="definition" class="form-label">Definition</label>
                        <textarea id="definition" name="definition" class="form-control" placeholder="Enter definition" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="examples" class="form-label">Examples (separated by '|')</label>
                        <textarea id="examples" name="examples" class="form-control" placeholder="Enter examples (separated by '|')" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="synonyms" class="form-label">Synonyms (comma-separated)</label>
                        <input type="text" id="synonyms" name="synonyms" class="form-control" placeholder="Enter synonyms (comma-separated)" required>
                    </div>
                    <div class="mb-3">
                        <label for="antonyms" class="form-label">Antonyms (comma-separated)</label>
                        <input type="text" id="antonyms" name="antonyms" class="form-control" placeholder="Enter antonyms (comma-separated)" required>
                    </div>
                    <div class="mb-3">
                        <label for="audio" class="form-label">Audio URL</label>
                        <input type="text" id="audio" name="audio" class="form-control" placeholder="Enter audio URL (optional)">
                    </div>
                    <button type="submit" class="btn btn-orange w-100">Add Word</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Admin JS -->
    <script src="admin.js"></script>
</body>
</html>