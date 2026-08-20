document.querySelectorAll('.drop-zone').forEach((dropZone) => {

    const inputId = dropZone.dataset.input;
    const input = document.getElementById(inputId);

    const button = dropZone.querySelector('.drop-zone-button');
    const fileInfo = dropZone.querySelector('.drop-zone-files');

    button.addEventListener('click', () => {
        input.click();
    });

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();

        dropZone.classList.remove('drag-over');

        input.files = event.dataTransfer.files;

        updateFileInfo();
    });

    input.addEventListener('change', () => {
        updateFileInfo();
    });

    function updateFileInfo() {
        const count = input.files.length;

        if (count === 0) {
            fileInfo.textContent = 'Keine Dateien ausgewählt.';
        } else if (count === 1) {
            fileInfo.textContent = input.files[0].name;
        } else {
            fileInfo.textContent = `${count} Dateien ausgewählt.`;
        }
    }

});