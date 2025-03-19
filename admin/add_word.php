<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$word = $_POST['word'];
$definition = $_POST['definition'];
$examples = explode('|', $_POST['examples']); // Split examples into an array (using '|' as a delimiter)
$synonyms = explode(',', $_POST['synonyms']); // Split synonyms into an array
$antonyms = explode(',', $_POST['antonyms']); // Split antonyms into an array
$audio = $_POST['audio']; // Audio URL (if provided)

// Insert into `words` table
$sql = "INSERT INTO words (word, audio) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $word, $audio);
$stmt->execute();
$word_id = $stmt->insert_id; // Get the ID of the inserted word

// Insert into `meanings` table
$sql = "INSERT INTO meanings (word_id, definition) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $word_id, $definition);
$stmt->execute();
$meaning_id = $stmt->insert_id; // Get the ID of the inserted meaning

// Insert into `examples` table
$sql = "INSERT INTO examples (meaning_id, example) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
foreach ($examples as $example) {
    $example = trim($example); // Remove extra spaces
    if (!empty($example)) {
        $stmt->bind_param("is", $meaning_id, $example);
        $stmt->execute();
    }
}

// Insert into `synonyms` table
$sql = "INSERT INTO synonyms (word_id, synonym) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
foreach ($synonyms as $synonym) {
    $synonym = trim($synonym); // Remove extra spaces
    if (!empty($synonym)) {
        $stmt->bind_param("is", $word_id, $synonym);
        $stmt->execute();
    }
}

// Insert into `antonyms` table
$sql = "INSERT INTO antonyms (word_id, antonym) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
foreach ($antonyms as $antonym) {
    $antonym = trim($antonym); // Remove extra spaces
    if (!empty($antonym)) {
        $stmt->bind_param("is", $word_id, $antonym);
        $stmt->execute();
    }
}

echo "Word added successfully!";
$stmt->close();
$conn->close();
?>