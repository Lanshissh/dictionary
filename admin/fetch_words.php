<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$itemsPerPage = 10; // Number of items per page
$offset = ($page - 1) * $itemsPerPage;

// Query to fetch words with pagination and search
$sql = "
    SELECT SQL_CALC_FOUND_ROWS w.id, w.word, m.definition, 
           GROUP_CONCAT(DISTINCT e.example SEPARATOR '|') AS examples
    FROM words w
    LEFT JOIN meanings m ON w.id = m.word_id
    LEFT JOIN examples e ON m.id = e.meaning_id
    WHERE w.word LIKE ?
    GROUP BY w.id, m.id
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$searchParam = "%$searchQuery%";
$stmt->bind_param("sii", $searchParam, $itemsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Get total number of rows
$totalRows = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
$totalPages = ceil($totalRows / $itemsPerPage);

echo json_encode([
    'words' => $data,
    'totalPages' => $totalPages
]);

$stmt->close();
$conn->close();
?>