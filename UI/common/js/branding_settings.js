// --- White Label & Branding Settings Logic ---

let logoFilename = null;
let removeLogoFlag = 0;

// Link pickers to input text fields
function syncColorPicker(pickerId, inputId) {
    const $picker = $(pickerId);
    const $input = $(inputId);

    $picker.on('input', function () {
        $input.val($(this).val().toUpperCase());
        updateLivePreview();
    });

    $input.on('input', function () {
        const val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $picker.val(val);
            updateLivePreview();
        }
    });
}

syncColorPicker('#primary_picker', '#primary_color');
syncColorPicker('#secondary_picker', '#secondary_color');
syncColorPicker('#accent_picker', '#accent_color');

function updateLivePreview() {
    const primary = $('#primary_color').val() || '#6366F1';
    const accent = $('#accent_color').val() || '#10B981';

    $('#preview-btn').css({
        'background': primary,
        'box-shadow': `0 1px 2px 0 ${primary}66`
    });
    $('#preview-badge').css({
        'background': `${accent}22`,
        'color': accent
    });
}

// Fetch and display branding settings on load
async function loadBrandingSettings() {
    try {
        const data = await API.fetchWithAuth('admin/settings');
        const settings = data.settings || {};

        $('#platform_name').val(settings.platform_name || 'AffiTrace Agency');
        $('#support_email').val(settings.support_email || '');
        $('#custom_copyright').val(settings.custom_copyright || '');

        // Colors
        const pColor = settings.primary_color || '#2563EB';
        const sColor = settings.secondary_color || '#1E293B';
        const aColor = settings.accent_color || '#10B981';

        $('#primary_color').val(pColor.toUpperCase());
        $('#primary_picker').val(pColor);

        $('#secondary_color').val(sColor.toUpperCase());
        $('#secondary_picker').val(sColor);

        $('#accent_color').val(aColor.toUpperCase());
        $('#accent_picker').val(aColor);

        updateLivePreview();

        // Logo preview
        logoFilename = settings.platform_logo;
        if (logoFilename) {
            const logoUrl = `${API.BASE_URL}/../storage/branding/${logoFilename}`;
            $('#logo-preview-container').html(`<img src="${logoUrl}" style="max-height: 50px; max-width: 100%;">`);
            $('#btnRemoveLogo').removeClass('d-none');
        } else {
            $('#logo-preview-container').html(`<span class="text-muted small">No Logo</span>`);
            $('#btnRemoveLogo').addClass('d-none');
        }

        // Favicon preview
        if (settings.platform_favicon) {
            const faviconUrl = `${API.BASE_URL}/../storage/branding/${settings.platform_favicon}`;
            $('#favicon-preview-container').html(`<img src="${faviconUrl}" style="max-height: 40px; max-width: 40px;">`);
        } else {
            $('#favicon-preview-container').html(`<span class="text-muted small">No Icon</span>`);
        }

    } catch (e) {
        console.error('Failed to load branding configurations:', e);
    }
}

// Initial load
loadBrandingSettings();

// Logo Removal Handler
$('#btnRemoveLogo').on('click', function () {
    if (confirm('Are you sure you want to remove the logo?')) {
        removeLogoFlag = 1;
        $('#logo-preview-container').html(`<span class="text-muted small">Removed</span>`);
        $(this).addClass('d-none');
        $('#platform_logo').val('');
    }
});

// Branding settings form submission
$('#brandingForm').on('submit', async function (e) {
    e.preventDefault();
    $('#btnSaveBranding').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
    $('#brand-alert-container').empty();

    const formData = new FormData();
    formData.append('platform_name', $('#platform_name').val());
    formData.append('support_email', $('#support_email').val());
    formData.append('custom_copyright', $('#custom_copyright').val());
    formData.append('primary_color', $('#primary_color').val());
    formData.append('secondary_color', $('#secondary_color').val());
    formData.append('accent_color', $('#accent_color').val());
    formData.append('remove_logo', removeLogoFlag);

    const logoFile = $('#platform_logo')[0].files[0];
    if (logoFile) {
        formData.append('platform_logo', logoFile);
    }

    const faviconFile = $('#platform_favicon')[0].files[0];
    if (faviconFile) {
        formData.append('platform_favicon', faviconFile);
    }

    try {
        const result = await API.fetchWithAuth('admin/settings', {
            method: 'POST',
            body: formData // Fetch API automatically determines multi-part boundary when body is FormData
        });

        if (result.status === 1) {
            removeLogoFlag = 0;
            $('#brand-alert-container').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Branding configurations updated successfully!</div>');

            // Update cached settings in localStorage and apply branding styles dynamically
            if (result.settings) {
                localStorage.setItem('branding_settings', JSON.stringify(result.settings));

                // Re-apply dynamically on the current screen
                const event = new Event('DOMContentLoaded');
                document.dispatchEvent(event);

                // Re-render preview
                await loadBrandingSettings();
            }
        } else {
            throw new Error(result.msg || 'Save failed');
        }
    } catch (err) {
        console.error('[Branding Settings] Save failed:', err);
        $('#brand-alert-container').html(`<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> ${err.message || 'Failed to save settings.'}</div>`);
    } finally {
        $('#btnSaveBranding').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Branding Settings');
    }
});
