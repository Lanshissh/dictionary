<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "dictionary_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch words and related data
$sql = "
    SELECT w.id, w.word, w.audio,
           m.definition,
           GROUP_CONCAT(DISTINCT s.synonym) AS synonyms,
           GROUP_CONCAT(DISTINCT a.antonym) AS antonyms,
           GROUP_CONCAT(DISTINCT e.example SEPARATOR '|') AS examples
    FROM words w
    LEFT JOIN meanings m ON w.id = m.word_id
    LEFT JOIN examples e ON m.id = e.meaning_id
    LEFT JOIN synonyms s ON w.id = s.word_id
    LEFT JOIN antonyms a ON w.id = a.word_id
    GROUP BY w.id
";

// Execute the query and check for errors
$words = $conn->query($sql);

if (!$words) {
    // Query failed, display error
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Kapampangan Dictionary</title>
  
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .container {
      margin-top: 30px;
    }
    .btn-custom {
      margin-top: 10px;
    }
    .table th, .table td {
      text-align: center;
    }
    .dashboard {
      display: flex;
      justify-content: space-between;
      gap: 20px;
    }
    .dashboard .table-container {
      flex: 0 0 65%;
    }
    .dashboard .form-container {
      flex: 0 0 30%;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .modal-dialog {
      max-width: 600px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="text-center">Kapampangan Dictionary Admin</h2>

    <div class="dashboard">
      <!-- Table for displaying words -->
      <div class="table-container">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Word</th>
              <th>Definition</th>
              <th>Examples</th>
              <th>Synonyms</th>
              <th>Antonyms</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Check if there are any words to display
            if ($words->num_rows > 0) {
                while ($row = $words->fetch_assoc()) {
            ?>
              <tr>
                <td><?= htmlspecialchars($row['word']) ?></td>
                <td><?= htmlspecialchars($row['definition']) ?></td>
                <td><?= htmlspecialchars($row['examples']) ?></td>
                <td><?= htmlspecialchars($row['synonyms']) ?></td>
                <td><?= htmlspecialchars($row['antonyms']) ?></td>
                <td>
                  <!-- Edit Button -->
                  <button class="btn btn-warning editBtn"
                    data-id="<?= $row['id'] ?>"
                    data-word="<?= htmlspecialchars($row['word']) ?>"
                    data-definition="<?= htmlspecialchars($row['definition']) ?>"
                    data-examples="<?= htmlspecialchars($row['examples']) ?>"
                    data-synonyms="<?= htmlspecialchars($row['synonyms']) ?>"
                    data-antonyms="<?= htmlspecialchars($row['antonyms']) ?>"
                    data-bs-toggle="modal" data-bs-target="#editModal">
                    Edit
                  </button>
                </td>
              </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6'>No words found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

      <!-- Add New Word Form on the Right -->
      <div class="form-container">
        <h3>Add New Word</h3>
        <form id="addForm">
          <div class="mb-3">
            <label for="word" class="form-label">Word:</label>
            <input type="text" name="word" class="form-control" required><br>
          </div>
          <div class="mb-3">
            <label for="definition" class="form-label">Definition:</label>
            <textarea name="definition" class="form-control" required></textarea><br>
          </div>
          <div class="mb-3">
            <label for="examples" class="form-label">Examples (use | to separate):</label>
            <textarea name="examples" class="form-control"></textarea><br>
          </div>
          <div class="mb-3">
            <label for="synonyms" class="form-label">Synonyms (use comma):</label>
            <input type="text" name="synonyms" class="form-control"><br>
          </div>
          <div class="mb-3">
            <label for="antonyms" class="form-label">Antonyms (use comma):</label>
            <input type="text" name="antonyms" class="form-control"><br>
          </div>
          <button type="submit" class="btn btn-success">Add Word</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Word Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Word</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" name="id" id="editId">
            <div class="mb-3">
              <label for="editWord" class="form-label">Word:</label>
              <input type="text" name="word" id="editWord" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="editDefinition" class="form-label">Definition:</label>
              <textarea name="definition" id="editDefinition" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
              <label for="editExamples" class="form-label">Examples (use | to separate):</label>
              <textarea name="examples" id="editExamples" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label for="editSynonyms" class="form-label">Synonyms (use comma):</label>
              <input type="text" name="synonyms" id="editSynonyms" class="form-control">
            </div>
            <div class="mb-3">
              <label for="editAntonyms" class="form-label">Antonyms (use comma):</label>
              <input type="text" name="antonyms" id="editAntonyms" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Word</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS & Popper.js (for modals) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Edit Button functionality
    document.querySelectorAll('.editBtn').forEach(btn => {
      btn.onclick = () => {
        document.getElementById('editId').value = btn.dataset.id;
        document.getElementById('editWord').value = btn.dataset.word;
        document.getElementById('editDefinition').value = btn.dataset.definition;
        document.getElementById('editExamples').value = btn.dataset.examples;
        document.getElementById('editSynonyms').value = btn.dataset.synonyms;
        document.getElementById('editAntonyms').value = btn.dataset.antonyms;
      }
    });

    // Add New Word Form
    document.getElementById('addForm').onsubmit = function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('admin_api.php?action=create', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        location.reload();
      });
    };

    // Edit Word Form
    document.getElementById('editForm').onsubmit = function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('admin_api.php?action=update', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        location.reload();
      });
    };
  </script>

</body>
</html>
