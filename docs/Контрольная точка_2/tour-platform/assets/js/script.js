// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Автоматическое скрытие уведомлений через 5 секунд
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Подтверждение удаления
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите удалить этот элемент?')) {
                e.preventDefault();
            }
        });
    });

    // Валидация форм
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#dc3545';
                } else {
                    input.style.borderColor = '#ddd';
                }
            });

            // Валидация email
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (input.value && !emailRegex.test(input.value)) {
                    isValid = false;
                    input.style.borderColor = '#dc3545';
                }
            });

            // Валидация пароля
            const passwordInputs = form.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                if (input.value && input.value.length < 6) {
                    isValid = false;
                    input.style.borderColor = '#dc3545';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля корректно');
            }
        });
    });

    // Динамический поиск
    const searchInput = document.querySelector('.live-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.search-item');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Сортировка таблиц
    const sortableTables = document.querySelectorAll('.sortable-table');
    sortableTables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');

        headers.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.dataset.sort;
                const rows = Array.from(table.querySelector('tbody').rows);
                const direction = this.dataset.direction === 'asc' ? 'desc' : 'asc';

                headers.forEach(h => delete h.dataset.direction);
                this.dataset.direction = direction;

                rows.sort((a, b) => {
                    const aValue = a.querySelector(`td:nth-child(${this.cellIndex + 1})`).textContent;
                    const bValue = b.querySelector(`td:nth-child(${this.cellIndex + 1})`).textContent;

                    if (direction === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                });

                const tbody = table.querySelector('tbody');
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // Калькулятор цены при бронировании
    const participantsInput = document.getElementById('participants');
    const pricePerPerson = document.getElementById('price-per-person');
    const totalPriceElement = document.getElementById('total-price');

    if (participantsInput && pricePerPerson && totalPriceElement) {
        const price = parseFloat(pricePerPerson.value);

        const updateTotalPrice = () => {
            const participants = parseInt(participantsInput.value) || 1;
            const total = price * participants;
            totalPriceElement.textContent = total.toLocaleString() + ' ₽';
        };

        participantsInput.addEventListener('input', updateTotalPrice);
        updateTotalPrice();
    }

    // Предпросмотр изображений
    const imageInput = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Автоматическое обновление статусов (для админ-панели)
    const refreshButton = document.getElementById('refresh-stats');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            location.reload();
        });
    }

    // Фильтр дат
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });
    }
});