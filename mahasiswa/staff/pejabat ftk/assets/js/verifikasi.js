document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const fileInput = document.querySelector('input[type="file"]');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                alert('File dipilih: ' + fileName);
            }
        });
    }

    form.addEventListener('submit', function (e) {
        const nomor = document.querySelector('input[name="nomor_surat"]').value.trim();
        const cap = document.querySelector('input[name="cap_surat"]').value.trim();

        if (nomor === '' || cap === '') {
            alert('Harap isi semua field yang diperlukan.');
            e.preventDefault();
            return;
        }

        const confirmSubmit = confirm('Yakin ingin menyimpan dan mendisposisi surat ini?');
        if (!confirmSubmit) {
            e.preventDefault();
        }
    });
});
