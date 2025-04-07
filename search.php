<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary_db";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode([]));
}

// Get searched word
$word = $_GET['word'] ?? '';

if (empty($word)) {
    echo json_encode([]);
    exit;
}

// Main query: Fetch word info
$sql = "
    SELECT 
        w.word,
        w.audio,
        GROUP_CONCAT(DISTINCT m.definition SEPARATOR ' | ') AS definition,
        GROUP_CONCAT(DISTINCT e.example SEPARATOR '|') AS examples,
        GROUP_CONCAT(DISTINCT s.synonym) AS synonyms,
        GROUP_CONCAT(DISTINCT a.antonym) AS antonyms
    FROM words w
    LEFT JOIN meanings m ON w.id = m.word_id
    LEFT JOIN examples e ON m.id = e.meaning_id
    LEFT JOIN synonyms s ON w.id = s.word_id
    LEFT JOIN antonyms a ON w.id = a.word_id
    WHERE w.word = ?
    GROUP BY w.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $word);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($row = $result->fetch_assoc()) {
    $data[] = [
        "word" => $row["word"],
        "phonetic" => "", // Optional: add phonetic if available
        "definition" => $row["definition"],
        "examples" => $row["examples"],
        "synonyms" => $row["synonyms"],
        "antonyms" => $row["antonyms"],
        "audio" => $row["audio"]
    ];
}

echo json_encode($data);

$stmt->close();
$conn->close();
