<?php
// Shared White-Label Branding Head Component
?>
<script>
    (function () {
        const branding = JSON.parse(localStorage.getItem('branding_settings') || '{}');
        const primary = branding.primary_color || '#6366F1';
        const secondary = branding.secondary_color || '#1E293B';
        const accent = branding.accent_color || '#10B981';
        const name = branding.platform_name || 'Affitrace';

        // Inject CSS variables overrides
        const style = document.createElement('style');
        style.id = 'dynamic-branding-styles';
        style.innerHTML = `
        :root {
            --primary-main: ${primary} !important;
            --primary-light: ${primary}ee !important;
            --primary-dark: ${primary}dd !important;
            --text-navbar-active: ${primary} !important;
            --secondary-main: ${secondary} !important;
            --secondary-light: ${secondary}ee !important;
            --secondary-dark: ${secondary}dd !important;
            --success-main: ${accent} !important;
        }
        /* Custom overrides for components that do not inherit correctly */
        .sidebar-brand-text, .text-primary, .btn-primary {
            --primary-main: ${primary} !important;
        }
    `;
        document.head.appendChild(style);

        // Document Title White-Labeling
        if (name) {
            const defaultTitle = 'Affiliate System';
            const defaultTitleAlt = 'Affitrace';
            document.title = document.title.replace(defaultTitle, name).replace(defaultTitleAlt, name);
        }
    })();
</script>