// log_surat.js

// Optional: Tambahkan konfirmasi hapus yang lebih interaktif (bisa pakai SweetAlert atau native confirm)

document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('form[onsubmit]');

  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      const confirmed = confirm('Yakin ingin menghapus status ini?');
      if (!confirmed) {
        e.preventDefault();
      }
    });
  });
});
