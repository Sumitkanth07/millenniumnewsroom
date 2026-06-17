const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

navToggle?.addEventListener('click', () => siteNav?.classList.toggle('open'), { passive: true });

document.querySelectorAll('a[href^="/#"],a[href^="#"]').forEach(link => {
    link.addEventListener('click', event => {
        const selector = link.getAttribute('href').replace('/#', '#');
        const target = document.querySelector(selector);
        if (!target) return;
        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        siteNav?.classList.remove('open');
    });
});

const sections = [...document.querySelectorAll('section[id]')];
const links = [...document.querySelectorAll('.nav-link')];
let navTicking = false;
if (sections.length && links.length) {
    window.addEventListener('scroll', () => {
        if (navTicking) return;
        navTicking = true;
        requestAnimationFrame(() => {
            const current = sections.filter(section => section.offsetTop <= window.scrollY + 120).pop();
            links.forEach(link => link.classList.toggle('active', link.getAttribute('href')?.endsWith(`#${current?.id}`)));
            navTicking = false;
        });
    }, { passive: true });
}

const calculator = document.querySelector('.calculator');
if (calculator) {
    const fields = ['monthlyBill', 'city', 'roofSize', 'usage'].reduce((all, id) => {
        all[id] = document.getElementById(id);
        return all;
    }, {});
    const currency = value => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(value);
    const update = () => {
        const monthlyBill = Number(fields.monthlyBill.value || 0);
        const city = Number(fields.city.value || 1);
        const roofSize = Number(fields.roofSize.value || 0);
        const usage = Number(fields.usage.value || 1);
        const rate = Number(calculator.dataset.rate || 8);
        const sunHours = Number(calculator.dataset.sunHours || 4.5);
        const co2Factor = Number(calculator.dataset.co2Factor || 1.35);
        const consumption = monthlyBill / rate;
        const estimatedSize = consumption / (sunHours * 30 * city);
        const roofLimit = roofSize / 100;
        const systemSize = Math.max(1, Math.min(estimatedSize * usage, roofLimit || estimatedSize));
        const monthlySavings = Math.min(.9 * monthlyBill, systemSize * sunHours * 30 * rate * city);
        const yearlySavings = 12 * monthlySavings;
        document.getElementById('systemSize').textContent = `${systemSize.toFixed(1)} kW`;
        document.getElementById('monthlySavings').textContent = currency(monthlySavings);
        document.getElementById('yearlySavings').textContent = currency(yearlySavings);
        document.getElementById('longSavings').textContent = currency(25 * yearlySavings);
        document.getElementById('co2Reduction').textContent = `${(25 * systemSize * co2Factor).toFixed(1)} tons`;
    };
    Object.values(fields).forEach(field => field?.addEventListener('input', update, { passive: true }));
    update();
}
