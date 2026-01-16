@extends('layouts.dashboard')

@section('title', 'Crear Promoción - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Crear Nueva Promoción</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Ofrece descuentos especiales a tus clientes</p>
        </div>
        <div>
            <a href="/dashboard/promociones" class="btn btn-outline">← Volver</a>
        </div>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div id="error-message"
            style="display: none; background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        </div>

        <form id="promocion-form">
            <div class="grid grid-2" style="gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Título de la Promoción
                            *</label>
                        <input type="text" id="title" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción *</label>
                        <textarea id="description" required rows="3"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Local Asociado *</label>
                        <select id="locale_id" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Selecciona un local</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tipo de Descuento *</label>
                        <select id="discount_type" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Selecciona el tipo</option>
                            <option value="2x1">🎁 2x1 (Paga uno, lleva dos)</option>
                            <option value="percentage">💯 Porcentaje de Descuento</option>
                            <option value="fixed">💵 Descuento Fijo (Monto)</option>
                        </select>
                    </div>

                    <!-- Discount Fields (shown conditionally) -->
                    <div id="percentage-field" style="display: none; margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Porcentaje de Descuento
                            (%)</label>
                        <input type="number" id="discount_percentage" min="1" max="100"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div id="fixed-field" style="display: none; margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Monto de Descuento
                            (S/)</label>
                        <input type="number" id="discount_amount" step="0.01" min="0"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div class="grid grid-2">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio Original
                                (S/)</label>
                            <input type="number" id="original_price" step="0.01" min="0"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio Final
                                (S/)</label>
                            <input type="number" id="final_price" step="0.01" min="0"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="grid grid-2">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha de Inicio
                                *</label>
                            <input type="date" id="start_date" required
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha de Fin *</label>
                            <input type="date" id="end_date" required
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Código de Redención</label>
                        <input type="text" id="redemption_code" maxlength="50"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; text-transform: uppercase;">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Ej: VERANO2025,
                            DESCUENTO20</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Términos y
                            Condiciones</label>
                        <textarea id="terms_conditions" rows="6"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Restricciones,
                            horarios, etc.</p>
                    </div>

                    <!-- Preview Card -->
                    <div
                        style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
                        <p style="margin: 0; font-size: 0.875rem; opacity: 0.9;">Vista Previa</p>
                        <div id="preview-display" style="font-size: 2rem; font-weight: 800; margin: 0.5rem 0;">--</div>
                        <p id="preview-text" style="margin: 0; font-size: 0.875rem;"></p>
                    </div>
                </div>
            </div>

            <div
                style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--bg-light); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="/dashboard/promociones" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear Promoción</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyLocales() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/locales');
                const allLocales = response.data.data.data;
                const myLocales = allLocales.filter(l => l.user_id === user.id);

                const select = document.getElementById('locale_id');
                myLocales.forEach(locale => {
                    const option = document.createElement('option');
                    option.value = locale.id;
                    option.textContent = locale.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading locales:', error);
            }
        }

        // Show/hide discount fields based on type
        document.getElementById('discount_type').addEventListener('change', function() {
            const type = this.value;
            const percentageField = document.getElementById('percentage-field');
            const fixedField = document.getElementById('fixed-field');

            percentageField.style.display = 'none';
            fixedField.style.display = 'none';

            if (type === 'percentage') {
                percentageField.style.display = 'block';
                document.getElementById('discount_percentage').required = true;
                document.getElementById('discount_amount').required = false;
            } else if (type === 'fixed') {
                fixedField.style.display = 'block';
                document.getElementById('discount_amount').required = true;
                document.getElementById('discount_percentage').required = false;
            } else if (type === '2x1') {
                document.getElementById('discount_percentage').required = false;
                document.getElementById('discount_amount').required = false;
            }

            updatePreview();
        });

        // Update preview
        function updatePreview() {
            const type = document.getElementById('discount_type').value;
            const percentage = document.getElementById('discount_percentage').value;
            const amount = document.getElementById('discount_amount').value;
            const display = document.getElementById('preview-display');
            const text = document.getElementById('preview-text');

            if (type === '2x1') {
                display.textContent = '2×1';
                text.textContent = 'PAGA UNO, LLEVA DOS';
            } else if (type === 'percentage' && percentage) {
                display.textContent = percentage + '%';
                text.textContent = 'DE DESCUENTO';
            } else if (type === 'fixed' && amount) {
                display.textContent = 'S/ ' + parseFloat(amount).toFixed(2);
                text.textContent = 'DE DESCUENTO';
            } else {
                display.textContent = '--';
                text.textContent = '';
            }
        }

        document.getElementById('discount_percentage').addEventListener('input', updatePreview);
        document.getElementById('discount_amount').addEventListener('input', updatePreview);

        document.getElementById('promocion-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';

            const data = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                locale_id: document.getElementById('locale_id').value,
                discount_type: document.getElementById('discount_type').value,
                discount_percentage: document.getElementById('discount_percentage').value || null,
                discount_amount: document.getElementById('discount_amount').value || null,
                original_price: document.getElementById('original_price').value || null,
                final_price: document.getElementById('final_price').value || null,
                start_date: document.getElementById('start_date').value,
                end_date: document.getElementById('end_date').value,
                redemption_code: document.getElementById('redemption_code').value.toUpperCase() || null,
                terms_conditions: document.getElementById('terms_conditions').value || null,
            };

            try {
                const response = await axios.post('/api/promociones', data);

                if (response.data.success) {
                    showNotification('¡Promoción creada exitosamente!', 'success');
                    window.location.href = '/dashboard/promociones';
                }
            } catch (error) {
                errorDiv.style.display = 'block';

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else if (error.response?.data?.message) {
                    errorDiv.textContent = error.response.data.message;
                } else {
                    errorDiv.textContent = 'Error al crear la promoción. Por favor, intenta nuevamente.';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadMyLocales();

            // Set minimum dates to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').min = today;
            document.getElementById('end_date').min = today;
        });
    </script>
@endpush
