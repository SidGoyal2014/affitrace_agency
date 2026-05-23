(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    var PRESET_OPTIONS = [
        { value: 'custom', label: 'Custom Range' },
        { value: 'today', label: 'Today' },
        { value: 'yesterday', label: 'Yesterday' },
        { value: 'last_7_days', label: 'Last 7 Days' },
        { value: 'last_15_days', label: 'Last 15 Days' },
        { value: 'last_30_days', label: 'Last 30 Days' },
        { value: 'this_month', label: 'This Month' },
        { value: 'last_month', label: 'Last Month' }
    ];

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function formatDate(date) {
        return [date.getFullYear(), pad(date.getMonth() + 1), pad(date.getDate())].join('-');
    }

    function cloneDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function addDays(date, days) {
        var next = cloneDate(date);
        next.setDate(next.getDate() + days);
        return next;
    }

    function getRangeForPreset(preset) {
        var today = cloneDate(new Date());
        var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        var firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        var lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        var yesterday = addDays(today, -1);

        switch (preset) {
            case 'today':
                return { from: formatDate(today), to: formatDate(today) };
            case 'yesterday':
                return { from: formatDate(yesterday), to: formatDate(yesterday) };
            case 'last_7_days':
                return { from: formatDate(addDays(today, -6)), to: formatDate(today) };
            case 'last_15_days':
                return { from: formatDate(addDays(today, -14)), to: formatDate(today) };
            case 'last_30_days':
                return { from: formatDate(addDays(today, -29)), to: formatDate(today) };
            case 'this_month':
                return { from: formatDate(firstDayOfMonth), to: formatDate(lastDayOfMonth) };
            case 'last_month':
                return { from: formatDate(firstDayOfLastMonth), to: formatDate(lastDayOfLastMonth) };
            default:
                return null;
        }
    }

    function getMatchingPreset(fromValue, toValue) {
        if (!fromValue || !toValue) {
            return 'custom';
        }

        for (var index = 1; index < PRESET_OPTIONS.length; index += 1) {
            var preset = PRESET_OPTIONS[index].value;
            var range = getRangeForPreset(preset);
            if (range && range.from === fromValue && range.to === toValue) {
                return preset;
            }
        }

        return 'custom';
    }

    function createPresetSelect(selectId) {
        var select = document.createElement('select');
        select.className = 'form-control';
        select.id = selectId;
        select.name = 'date_preset';
        select.setAttribute('data-date-preset-select', 'true');

        PRESET_OPTIONS.forEach(function (optionConfig) {
            var option = document.createElement('option');
            option.value = optionConfig.value;
            option.textContent = optionConfig.label;
            select.appendChild(option);
        });

        return select;
    }

    function buildPresetContainer(className, nestedFormGroup, selectId) {
        var container = document.createElement('div');
        var formGroup = container;

        if (nestedFormGroup) {
            container.className = className;
            formGroup = document.createElement('div');
            formGroup.className = 'form-group';
            container.appendChild(formGroup);
        } else {
            container.className = className;
        }

        container.setAttribute('data-date-preset-container', 'true');

        var label = document.createElement('label');
        label.setAttribute('for', selectId);
        label.textContent = 'Quick Range';

        var select = createPresetSelect(selectId);

        formGroup.appendChild(label);
        formGroup.appendChild(select);

        return {
            container: container,
            select: select
        };
    }

    function triggerInputChange(input) {
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function resolveInsertionTarget(fromInput) {
        var formGroup = fromInput.closest('.form-group') || fromInput.parentElement;
        var columnWrapper = formGroup && formGroup.parentElement;

        if (!formGroup) {
            return null;
        }

        if (columnWrapper && columnWrapper !== formGroup && /(^|\s)col-/.test(columnWrapper.className)) {
            return {
                anchor: columnWrapper,
                parent: columnWrapper.parentElement,
                className: columnWrapper.className,
                nestedFormGroup: true
            };
        }

        return {
            anchor: formGroup,
            parent: formGroup.parentElement,
            className: formGroup.className,
            nestedFormGroup: false
        };
    }

    function bindPresetControl(form, fromInput, toInput) {
        if (form.getAttribute('data-date-preset-bound') === 'true') {
            return;
        }

        var insertionTarget = resolveInsertionTarget(fromInput);
        if (!insertionTarget || !insertionTarget.parent) {
            return;
        }

        var selectId = (form.id || 'report-form') + '-date-preset';
        var presetUi = buildPresetContainer(insertionTarget.className, insertionTarget.nestedFormGroup, selectId);
        insertionTarget.parent.insertBefore(presetUi.container, insertionTarget.anchor);

        function syncPreset() {
            presetUi.select.value = getMatchingPreset(fromInput.value, toInput.value);
        }

        presetUi.select.addEventListener('change', function () {
            var range = getRangeForPreset(presetUi.select.value);
            if (!range) {
                return;
            }

            fromInput.value = range.from;
            toInput.value = range.to;

            triggerInputChange(fromInput);
            triggerInputChange(toInput);
        });

        fromInput.addEventListener('change', syncPreset);
        toInput.addEventListener('change', syncPreset);
        fromInput.addEventListener('input', syncPreset);
        toInput.addEventListener('input', syncPreset);

        syncPreset();
        form.setAttribute('data-date-preset-bound', 'true');
    }

    function initReportDatePresets() {
        if (window.location.pathname.toLowerCase().indexOf('/reports/') === -1) {
            return;
        }

        Array.prototype.forEach.call(document.forms, function (form) {
            var fromInput = form.querySelector('input[name="from"], input[name="date_from"]');
            var toInput = form.querySelector('input[name="to"], input[name="date_to"]');

            if (!fromInput || !toInput) {
                return;
            }

            bindPresetControl(form, fromInput, toInput);
        });
    }

    window.addEventListener('load', initReportDatePresets);
}(window, document, window.jQuery));