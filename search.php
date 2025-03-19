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

$word = $_GET['word'];

// Query to fetch word details
$sql = "
    SELECT w.word, m.definition, 
           GROUP_CONCAT(DISTINCT s.synonym) AS synonyms, 
           GROUP_CONCAT(DISTINCT a.antonym) AS antonyms,
           GROUP_CONCAT(DISTINCT e.example SEPARATOR '|') AS examples,
           w.audio
    FROM words w
    LEFT JOIN meanings m ON w.id = m.word_id
    LEFT JOIN examples e ON m.id = e.meaning_id
    LEFT JOIN synonyms s ON w.id = s.word_id
    LEFT JOIN antonyms a ON w.id = a.word_id
    WHERE w.word = ?
    GROUP BY w.id, m.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $word);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>