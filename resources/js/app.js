import './bootstrap';

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

if (navToggle && siteNav) {
    navToggle.addEventListener('click', () => siteNav.classList.toggle('open'));
}

document.querySelectorAll('a[href^="/#"], a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
        const id = link.getAttribute('href').replace('/#', '#');
        const target = document.querySelector(id);
        if (target) {
            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            siteNav?.classList.remove('open');
        }
    });
});

const sections = [...document.querySelectorAll('section[id]')];
const links = [...document.querySelectorAll('.nav-link')];

if (sections.length && links.length) {
    window.addEventListener('scroll', () => {
        const active = sections.findLast((section) => section.offsetTop <= window.scrollY + 120);
        links.forEach((link) => link.classList.toggle('active', link.getAttribute('href')?.endsWith(`#${active?.id}`)));
    });
}

const calculator = document.querySelector('.calculator');

if (calculator) {
    const fields = ['monthlyBill', 'city', 'roofSize', 'usage'].reduce((items, id) => {
        items[id] = document.getElementById(id);
        return items;
    }, {});

    const money = (value) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(value);

    const update = () => {
        const bill = Number(fields.monthlyBill.value || 0);
        const city = Number(fields.city.value || 1);
        const roof = Number(fields.roofSize.value || 0);
        const usage = Number(fields.usage.value || 1);
        const rate = Number(calculator.dataset.rate || 8);
        const sunHours = Number(calculator.dataset.sunHours || 4.5);
        const co2Factor = Number(calculator.dataset.co2Factor || 1.35);
        const monthlyUnits = bill / rate;
        const requiredKw = monthlyUnits / (sunHours * 30 * city);
        const roofLimitedKw = roof / 100;
        const systemKw = Math.max(1, Math.min(requiredKw * usage, roofLimitedKw || requiredKw));
        const monthlySavings = Math.min(bill * .9, systemKw * sunHours * 30 * rate * city);
        const yearlySavings = monthlySavings * 12;
        const co2 = systemKw * co2Factor * 25;

        document.getElementById('systemSize').textContent = `${systemKw.toFixed(1)} kW`;
        document.getElementById('monthlySavings').textContent = money(monthlySavings);
        document.getElementById('yearlySavings').textContent = money(yearlySavings);
        document.getElementById('longSavings').textContent = money(yearlySavings * 25);
        document.getElementById('co2Reduction').textContent = `${co2.toFixed(1)} tons`;
    };

    Object.values(fields).forEach((field) => field?.addEventListener('input', update));
    update();
}
