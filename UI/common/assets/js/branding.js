document.addEventListener('DOMContentLoaded', function () {
    const branding = JSON.parse(localStorage.getItem('branding_settings') || '{}');
    const name = branding.platform_name || 'Affitrace';
    const logo = branding.platform_logo;
    const favicon = branding.platform_favicon;

    // Apply logo and brand name in top navbar
    const navbarBrand = document.querySelector('.navbar-brand');
    if (navbarBrand) {
        if (logo) {
            const apiBase = typeof API !== 'undefined' ? API.BASE_URL : '';
            const logoUrl = apiBase ? `${apiBase}/../storage/branding/${logo}` : `../storage/branding/${logo}`;
            navbarBrand.innerHTML = `<img src="${logoUrl}" alt="${name}" style="max-height: 40px; width: auto; max-width: 180px;">`;
        } else {
            navbarBrand.innerHTML = `<i class="fab fa-hive"></i> <span>${name}</span>`;
        }
    }

    // Apply favicon dynamically
    if (favicon) {
        const apiBase = typeof API !== 'undefined' ? API.BASE_URL : '';
        const faviconUrl = apiBase ? `${apiBase}/../storage/branding/${favicon}` : `../storage/branding/${favicon}`;

        let link = document.querySelector("link[rel~='icon']");
        if (!link) {
            link = document.createElement('link');
            link.rel = 'icon';
            document.head.appendChild(link);
        }
        link.href = faviconUrl;
    }

    // Apply dynamic copyright in footers
    const footerCopyrights = document.querySelectorAll('footer span, .footer span, .copyright span');
    footerCopyrights.forEach(function (el) {
        if (el && name) {
            el.innerHTML = el.innerHTML.replace(/Affitrace/g, name);
        }
    });
});
