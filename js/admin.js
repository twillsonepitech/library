const ConfirmToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: true,
    showCloseButton: true,
    confirmButtonText: `
        <i class="fa fa-thumbs-up"></i> Delete
    `,
});
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.querySelector('.search-bar');
    searchBar.addEventListener('input', function() {
        const filter = searchBar.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.name').innerText.toLowerCase();
            if (name.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('.editable').forEach(element => {
                const input = document.createElement('input');
                input.type = 'text';
                input.value = element.innerText;
                input.className = element.className;
                input.dataset.originalValue = element.innerText;
                input.style.width = '100%';
                input.style.boxSizing = 'border-box';
                element.replaceWith(input);
            });
            row.querySelector('.save-row').style.display = 'inline-block';
            row.querySelector('.cancel-row').style.display = 'inline-block';
            row.querySelector('.edit-row').style.display = 'none';
            row.querySelector('.fa-trash').style.display = 'none';
        });
    });

    document.querySelectorAll('.save-row').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('input[type="text"]').forEach(input => {
                const span = document.createElement('span');
                span.innerText = input.value;
                span.className = input.className;
                input.replaceWith(span);
            });
            row.querySelector('.save-row').style.display = 'none';
            row.querySelector('.cancel-row').style.display = 'none';
            row.querySelector('.edit-row').style.display = 'inline-block';
            row.querySelector('.fa-trash').style.display = 'inline-block';

            document.getElementById('book_id').value = row.querySelector('.book_id').innerText;
            document.getElementById('book_code').value = row.querySelector('.book_code').innerText;
            document.getElementById('name').value = row.querySelector('.name').innerText;
            document.getElementById('genre').value = row.querySelector('.genre').innerText;
            document.getElementById('rental_price').value = row.querySelector('.rental_price').innerText;
            document.getElementById('description').value = row.querySelector('.description').innerText;
            document.getElementById('mainForm').submit();
        });
    });

    document.querySelectorAll('.cancel-row').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('input[type="text"]').forEach(input => {
                const span = document.createElement('span');
                span.innerText = input.dataset.originalValue;
                span.className = input.className;
                input.replaceWith(span);
            });
            row.querySelector('.save-row').style.display = 'none';
            row.querySelector('.cancel-row').style.display = 'none';
            row.querySelector('.edit-row').style.display = 'inline-block';
            row.querySelector('.fa-trash').style.display = 'inline-block';
        });
    });

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const bookCode = this.getAttribute('data-book-code');
            const bookId = this.getAttribute('data-book-id');
            ConfirmToast.fire({
                    icon: 'warning',
                    html: `Confirm deletion of Book Code <b>${bookCode}</b> ?`
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('book_id').value = bookId;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    });
});
