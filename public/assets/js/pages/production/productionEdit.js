// resources/js/pages/productions-edit.js
(() => {
  // Read server payload safely from a JSON script tag (see Blade below)
  const payloadEl = document.getElementById('production-payload');
  if (!payloadEl) return;

  const {
    latestCuttings,
    factories,
    lines,
    production,
    orders
  } = JSON.parse(payloadEl.textContent);

  // ===== Helpers =====
  const asArray = (val) => {
    if (!val) return [];
    if (typeof val === 'string') { try { return JSON.parse(val); } catch { return []; } }
    return Array.isArray(val) ? val : [];
  };

  const renderOptions = (list, valueKey, labelKey, selectedValue = null) =>
    list.map(item => {
      const value = item[valueKey];
      const label = item[labelKey];
      const selected = selectedValue != null && String(value) === String(selectedValue) ? 'selected' : '';
      return `<option value="${value}" ${selected}>${label}</option>`;
    }).join('');

  const getOrderOptionEl = (orderId) => {
    const sel = document.getElementById('style-select');
    if (!sel) return null;
    return [...sel.options].find(o => String(o.value) === String(orderId));
  };

  const buildGarmentOptions = (orderId, selectedGarment) => {
    const opt = getOrderOptionEl(orderId);
    const garmentSelect = document.getElementById('garment_type');
    if (!garmentSelect) return;

    garmentSelect.innerHTML = '<option value="">Select...</option>';
    if (!opt) return;

    const garments = asArray(opt.getAttribute('data-garments'));
    garments.forEach(type => {
      const val = type.name;
      garmentSelect.insertAdjacentHTML(
        'beforeend',
        `<option value="${val}" ${String(val)===String(selectedGarment)?'selected':''}>${val}</option>`
      );
    });
  };

  const buildRows = (orderId, productionData) => {
    const fieldsDiv = document.getElementById('add-fields');
    if (!fieldsDiv) return;

    fieldsDiv.innerHTML = `
      <label class="form-label fw-semibold">Production Report</label>
      <div class="error text-danger small mt-1"></div>
    `;

    const selected = getOrderOptionEl(orderId);
    if (!selected) return;

    const colors = asArray(selected.getAttribute('data-colors'));
    const rows = Array.isArray(productionData) && productionData.length
      ? productionData
      : colors.map(c => ({
          color: c.color,
          order_qty: c.qty ?? null,
          cutting_qty: null,
          factory: '',
          line: '',
          input: null, total_input: null, output: null, total_output: null,
        }));

    const cutData = latestCuttings[String(orderId)] ? latestCuttings[String(orderId)].cutting : [];

    rows.forEach((row, idx) => {
      const found = cutData.find(c => String(c.color).toLowerCase() === String(row.color).toLowerCase());
      const latestCut = found ? (found.cutting_qty ?? null) : null;
      const displayCuttingQty = row.cutting_qty != null && row.cutting_qty !== '' ? row.cutting_qty : (latestCut ?? 'N/A');

      const html = `
        <div class="row g-2 align-items-center border-primary px-2 pb-2 border mb-2 rounded">
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Color</span>
            <input type="text" readonly class="form-control bg-soft-secondary"
              name="production_data[${idx}][color]" value="${row.color ?? ''}">
          </div>
          <div class="col-6 col-md-3 d-none">
            <span class="fw-semibold">Order Qty</span>
            <input type="number" readonly class="form-control bg-soft-secondary"
              name="production_data[${idx}][order_qty]" value="${row.order_qty ?? ''}">
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Cutting Qty</span>
            <input type="number" readonly class="form-control bg-soft-secondary"
              name="production_data[${idx}][cutting_qty]" value="${displayCuttingQty}">
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Factory</span>
            <select name="production_data[${idx}][factory]" class="form-select">
              <option value="">Select...</option>
              ${renderOptions(factories, 'name', 'name', row.factory ?? '')}
            </select>
            <div class="error text-danger small mt-1"></div>
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Line</span>
            <select name="production_data[${idx}][line]" class="form-select">
              <option value="">Select...</option>
              ${renderOptions(lines, 'name', 'name', row.line ?? '')}
            </select>
            <div class="error text-danger small mt-1"></div>
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Input</span>
            <input type="number" class="form-control"
              name="production_data[${idx}][input]" value="${row.input ?? ''}">
            <div class="error text-danger small mt-1"></div>
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Total Input</span>
            <input type="number" class="form-control"
              name="production_data[${idx}][total_input]" value="${row.total_input ?? ''}">
            <div class="error text-danger small mt-1"></div>
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Output</span>
            <input type="number" class="form-control"
              name="production_data[${idx}][output]" value="${row.output ?? ''}">
            <div class="error text-danger small mt-1"></div>
          </div>
          <div class="col-6 col-md-3">
            <span class="fw-semibold">Total Output</span>
            <input type="number" class="form-control"
              name="production_data[${idx}][total_output]" value="${row.total_output ?? ''}">
            <div class="error text-danger small mt-1"></div>
          </div>
        </div>
      `;
      fieldsDiv.insertAdjacentHTML('beforeend', html);
    });
  };

  // Prefill (edit)
  document.addEventListener('DOMContentLoaded', () => {
    // Preselect garment types based on current style
    const styleSelect = document.getElementById('style-select');
    if (!styleSelect) return;

    // Ensure the correct option is selected server-side; then build garment options + rows
    const initialOrderId = styleSelect.value || '{{ old('order_id', $production->order_id) }}';
    const initialGarment = '{{ $production->garment_type }}';
    buildGarmentOptions(initialOrderId, initialGarment);
    buildRows(initialOrderId, production.production_data || []);

    // Style change
    styleSelect.addEventListener('change', function () {
      buildGarmentOptions(this.value, '');
      buildRows(this.value, []);
    });
  });

  // AJAX submit with event delegation (same UX as before)
  $(function () {
    $("#form").on("submit", function (event) {
      event.preventDefault();
      const form = $(this);
      const formData = new FormData(this);
      const $btn = form.find('button[type="submit"]');
      $btn.prop("disabled", true);

      $.ajax({
        url: form.attr("action"),
        type: "POST", // _method=PUT in form
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        success: function (response) {
          $btn.prop("disabled", false);
          if (response.status) {
            window.location.href = "{{ route('productions.index') }}";
          } else {
            if (response.message) {
              Swal.fire({ toast:true, position:'top-right', icon: response.status?'success':'warning',
                title: response.message, showConfirmButton:false, timer:2500, timerProgressBar:true,
                customClass:{ popup:'colored-toast' } });
            }
            displayFieldErrors(response.errors || {});
          }
        },
        error: function (xhr) {
          $btn.prop("disabled", false);
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            displayFieldErrors(xhr.responseJSON.errors);
          } else {
            Swal.fire({ toast:true, position:'top-right', icon:'error',
              title:'Something went wrong. Please try again.', showConfirmButton:false,
              timer:2500, timerProgressBar:true, customClass:{ popup:'colored-toast' } });
          }
        },
      });

      function displayFieldErrors(errors) {
        $(".error").html("");
        $("input, select").removeClass("is-invalid");

        $.each(errors, function (key, value) {
          let name = key.replace(/\./g, "][");
          let selector = `[name='${name}']`;
          let input = $(selector);
          if (!input.length) input = $(`[name='${key}']`);

          let errorField = input.closest(".mb-3").find(".error").first();
          if (!errorField.length && input.next('.error').length) errorField = input.next('.error');

          input.addClass("is-invalid");
          errorField.html(Array.isArray(value) ? value[0] : value);
        });

        $("input, select").off('input change').on("input change", function () {
          $(this).removeClass("is-invalid").closest(".mb-3").find(".error").html("");
        });
      }
    });
  });
})();
