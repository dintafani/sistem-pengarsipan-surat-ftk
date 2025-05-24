document.addEventListener('DOMContentLoaded', () => {
    const hapusForms = document.querySelectorAll('form');

    hapusForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Surat ini akan dihapus dari daftar tampilan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0D6E63',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
