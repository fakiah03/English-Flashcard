// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            let currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            document.cookie = "theme=" + newTheme + ";path=/;max-age=31536000";
            
            // update icon
            if(newTheme === 'dark') {
                themeToggle.classList.remove('bi-moon');
                themeToggle.classList.add('bi-sun');
            } else {
                themeToggle.classList.remove('bi-sun');
                themeToggle.classList.add('bi-moon');
            }
        });
    }

    // Free Dictionary API integration for "Add Word" page
    const wordInput = document.getElementById('word');
    const generateBtn = document.getElementById('btn-generate-ai');
    
    if (wordInput && generateBtn) {
        generateBtn.addEventListener('click', async function() {
            const word = wordInput.value.trim();
            if (!word) {
                Swal.fire('Error', 'Please enter a word first.', 'error');
                return;
            }
            
            generateBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
            generateBtn.disabled = true;
            
            try {
                const response = await fetch(`https://api.dictionaryapi.dev/api/v2/entries/en/${word}`);
                if (!response.ok) throw new Error('Word not found in dictionary');
                
                const data = await response.json();
                const entry = data[0];
                
                // Extract Pronunciation (Phonetics)
                let pronunciation = '';
                if (entry.phonetics && entry.phonetics.length > 0) {
                    pronunciation = entry.phonetics.find(p => p.text)?.text || entry.phonetics[0].text || '';
                }
                
                // Extract Meaning and Example
                let meaning = '';
                let example = '';
                if (entry.meanings && entry.meanings.length > 0) {
                    const firstMeaning = entry.meanings[0].definitions[0];
                    meaning = firstMeaning.definition;
                    if (firstMeaning.example) {
                        example = firstMeaning.example;
                    }
                }
                
                // Fill the inputs
                document.getElementById('meaning').value = meaning;
                document.getElementById('example').value = example;
                document.getElementById('pronunciation').value = pronunciation;
                
                Swal.fire('Success', 'Word details generated successfully!', 'success');
                
            } catch (error) {
                Swal.fire('Error', error.message || 'Could not fetch word details.', 'error');
            } finally {
                generateBtn.innerHTML = '<i class="bi bi-magic"></i> Auto Generate details (AI)';
                generateBtn.disabled = false;
            }
        });
    }

});
