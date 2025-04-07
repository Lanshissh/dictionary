<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "dictionary_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit;
}

// Check if the word already exists in the database (for create action)
if ($_GET['action'] === 'create') {
    $word = $_POST['word'];
    
    // Check if the word already exists in the 'words' table
    $stmt = $conn->prepare("SELECT id FROM words WHERE word = ?");
    $stmt->bind_param("s", $word);
    $stmt->execute();
    $result = $stmt->get_result();

    // If word exists, don't create it again but allow adding meanings
    if ($result->num_rows > 0) {
        $word_id = $result->fetch_assoc()['id'];
    } else {
        // Insert new word into the 'words' table
        $stmt = $conn->prepare("INSERT INTO words (word) VALUES (?)");
        $stmt->bind_param("s", $word);
        $stmt->execute();
        $word_id = $conn->insert_id;
    }

    // Proceed to insert meaning, examples, synonyms, and antonyms
    $definition = $_POST['definition'];
    $examples = $_POST['examples'];
    $synonyms = $_POST['synonyms'];
    $antonyms = $_POST['antonyms'];

    // Check if the same word already has the same meaning
    $stmt = $conn->prepare("SELECT id FROM meanings WHERE word_id = ? AND definition = ?");
    $stmt->bind_param("is", $word_id, $definition);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the meaning already exists for the same word, show error
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "This word already has this meaning."]);
        exit;
    }

    // Insert new meaning for the word
    $stmt = $conn->prepare("INSERT INTO meanings (word_id, definition) VALUES (?, ?)");
    $stmt->bind_param("is", $word_id, $definition);
    $stmt->execute();
    $meaning_id = $conn->insert_id;

    // Insert examples
    $examplesArr = explode('|', $examples);
    foreach ($examplesArr as $example) {
        if (trim($example) !== "") {
            $stmt = $conn->prepare("INSERT INTO examples (meaning_id, example) VALUES (?, ?)");
            $stmt->bind_param("is", $meaning_id, $example);
            $stmt->execute();
        }
    }

    // Insert synonyms
    $synArr = explode(',', $synonyms);
    foreach ($synArr as $syn) {
        if (trim($syn) !== "") {
            $stmt = $conn->prepare("INSERT INTO synonyms (word_id, synonym) VALUES (?, ?)");
            $stmt->bind_param("is", $word_id, trim($syn));
            $stmt->execute();
        }
    }

    // Insert antonyms
    $antArr = explode(',', $antonyms);
    foreach ($antArr as $ant) {
        if (trim($ant) !== "") {
            $stmt = $conn->prepare("INSERT INTO antonyms (word_id, antonym) VALUES (?, ?)");
            $stmt->bind_param("is", $word_id, trim($ant));
            $stmt->execute();
        }
    }

    echo json_encode(["success" => true, "message" => "Word added successfully with meaning."]);
}


// Check if the word already exists in the database (for update action)
if ($_GET['action'] === 'update') {
    $id = $_POST['id'];
    $word = $_POST['word'];

    // Check if the word already exists (except for the current word being updated)
    $stmt = $conn->prepare("SELECT id FROM words WHERE word = ? AND id != ?");
    $stmt->bind_param("si", $word, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "The word already exists in the dictionary."]);
        exit;
    }

    $definition = $_POST['definition'];
    $examples = $_POST['examples'];
    $synonyms = $_POST['synonyms'];
    $antonyms = $_POST['antonyms'];

    // Update word
    $stmt = $conn->prepare("UPDATE words SET word = ? WHERE id = ?");
    $stmt->bind_param("si", $word, $id);
    $stmt->execute();

    // Update meaning
    $stmt = $conn->prepare("UPDATE meanings SET definition = ? WHERE word_id = ?");
    $stmt->bind_param("si", $definition, $id);
    $stmt->execute();

    // Update examples
    // (You should update examples if necessary, but here we assume you can handle the updates on the front-end)

    // Update synonyms
    $stmt = $conn->prepare("DELETE FROM synonyms WHERE word_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $synArr = explode(',', $synonyms);
    foreach ($synArr as $syn) {
        if (trim($syn) !== "") {
            $stmt = $conn->prepare("INSERT INTO synonyms (word_id, synonym) VALUES (?, ?)");
            $stmt->bind_param("is", $id, trim($syn));
            $stmt->execute();
        }
    }

    // Update antonyms
    $stmt = $conn->prepare("DELETE FROM antonyms WHERE word_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $antArr = explode(',', $antonyms);
    foreach ($antArr as $ant) {
        if (trim($ant) !== "") {
            $stmt = $conn->prepare("INSERT INTO antonyms (word_id, antonym) VALUES (?, ?)");
            $stmt->bind_param("is", $id, trim($ant));
            $stmt->execute();
        }
    }

    echo json_encode(["success" => true, "message" => "Word updated successfully."]);
}
?>
