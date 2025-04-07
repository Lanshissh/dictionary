<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">  
    <title>Kapampangan Dictionary</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
    <div class="wrapper">
        <header>Kapampangan Dictionary</header>
        <div class="search">
            <input type="text" id="searchInput" placeholder="Search a word" required spellcheck="false">
            <i class="fas fa-search" id="searchIcon"></i>
            <span class="material-icons" id="removeIcon">close</span>
        </div>
        <p class="info-text">Type any existing word and press enter to get meaning, example, synonyms, etc.</p>
        <ul>
            <li class="word">
                <div class="details">
                    <p id="wordText">__</p>
                    <span id="phoneticText">_ _</span>
                </div>
                <i class="fas fa-volume-up" id="volumeIcon" style="display: none;"></i>
            </li>
            <div class="content">
                <li class="meaning">
                    <div class="details">
                        <p>Meaning</p>
                        <span id="meaningText"><i>No meaning loaded</i></span>
                    </div>
                </li>
                <li class="example">
                    <div class="details">
                        <p>Example</p>
                        <span id="exampleText"><i>No examples yet</i></span>
                    </div>
                </li>
                <li class="synonyms">
                    <div class="details">
                        <p>Synonyms</p>
                        <div class="list" id="synonymsList"><i>None</i></div>
                    </div>
                </li>
                <li class="antonyms">
                    <div class="details">
                        <p>Antonyms</p>
                        <div class="list" id="antonymsList"><i>None</i></div>
                    </div>
                </li>
            </div>
        </ul>
    </div>

    <script>
        const wrapper = document.querySelector(".wrapper"),
              searchInput = document.getElementById("searchInput"),
              volumeIcon = document.getElementById("volumeIcon"),
              infoText = document.querySelector(".info-text"),
              synonymsList = document.getElementById("synonymsList"),
              antonymsList = document.getElementById("antonymsList"),
              removeIcon = document.getElementById("removeIcon"),
              wordText = document.getElementById("wordText"),
              phoneticText = document.getElementById("phoneticText"),
              meaningText = document.getElementById("meaningText"),
              exampleText = document.getElementById("exampleText");

        let audio;

        function data(result, word) {
            if (result.length === 0) {
                infoText.innerHTML = `Can't find the meaning of <span>"${word}"</span>. Please, try to search for another word.`;
            } else {
                wrapper.classList.add("active");
                const wordData = result[0];
                wordText.innerText = wordData.word;
                phoneticText.innerText = wordData.phonetic || ""; 

                meaningText.innerText = wordData.definition || "No definition available.";

                // Clear previous
                synonymsList.innerHTML = "";
                antonymsList.innerHTML = "";
                exampleText.innerHTML = "";

                // Synonyms
                if (wordData.synonyms) {
                    const synonyms = wordData.synonyms.split(',');
                    synonyms.forEach((synonym, index) => {
                        const span = document.createElement("span");
                        span.innerText = synonym.trim();
                        span.onclick = () => search(synonym.trim());
                        synonymsList.appendChild(span);
                        if (index < synonyms.length - 1) {
                            synonymsList.appendChild(document.createTextNode(", "));
                        }
                    });
                    synonymsList.parentElement.style.display = "block";
                } else {
                    synonymsList.innerHTML = "<i>None</i>";
                    synonymsList.parentElement.style.display = "block";
                }

                // Antonyms
                if (wordData.antonyms) {
                    const antonyms = wordData.antonyms.split(',');
                    antonyms.forEach((antonym, index) => {
                        const span = document.createElement("span");
                        span.innerText = antonym.trim();
                        span.onclick = () => search(antonym.trim());
                        antonymsList.appendChild(span);
                        if (index < antonyms.length - 1) {
                            antonymsList.appendChild(document.createTextNode(", "));
                        }
                    });
                    antonymsList.parentElement.style.display = "block";
                } else {
                    antonymsList.innerHTML = "<i>None</i>";
                    antonymsList.parentElement.style.display = "block";
                }

                // Examples
                if (wordData.examples) {
                    const examples = wordData.examples.split('|');
                    examples.forEach(example => {
                        const p = document.createElement("p");
                        p.innerText = example.trim();
                        exampleText.appendChild(p);
                    });
                    exampleText.parentElement.style.display = "block";
                } else {
                    exampleText.innerHTML = "<i>No examples</i>";
                    exampleText.parentElement.style.display = "block";
                }

                // Audio
                if (wordData.audio) {
                    audio = new Audio("audio/" + wordData.audio);
                    volumeIcon.style.display = "inline-block";
                } else {
                    volumeIcon.style.display = "none";
                }
            }
        }

        function search(word) {
            fetchApi(word);
            searchInput.value = word;
        }

        function fetchApi(word) {
            wrapper.classList.remove("active");
            infoText.style.color = "#000";
            infoText.innerHTML = `Searching the meaning of <span>"${word}"</span>`;
            fetch(`search.php?word=${encodeURIComponent(word)}`)
                .then(response => response.json())
                .then(result => {
                    console.log(result); // Debugging
                    data(result, word);
                })
                .catch(() => {
                    infoText.innerHTML = `Can't find the meaning of <span>"${word}"</span>. Please, try to search for another word.`;
                });
        }

        searchInput.addEventListener("keyup", e => {
            let word = e.target.value.trim();
            if (e.key === "Enter" && word) {
                fetchApi(word);
            }
        });

        removeIcon.addEventListener("click", () => {
            searchInput.value = "";
            searchInput.focus();
            wrapper.classList.remove("active");
            infoText.style.color = "#9A9A9A";
            infoText.innerHTML = "Type any existing word and press enter to get meaning, example, synonyms, etc.";
        });

        volumeIcon.addEventListener("click", () => {
            if (audio) audio.play();
        });
    </script>
</body>
</html>
