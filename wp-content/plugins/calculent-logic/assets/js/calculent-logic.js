(function () {
    const { restUrl, nonce } = window.calculentLogic || {};

    function serializeForm(form) {
        const data = {};
        const formData = new FormData(form);
        formData.forEach((value, key) => {
            if (data[key]) {
                if (!Array.isArray(data[key])) {
                    data[key] = [data[key]];
                }
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });
        return data;
    }

    function renderResult(container, payload) {
        if (!container) return;
        const { result, summary, error } = payload;
        if (error) {
            container.textContent = error;
            container.classList.add('is-error');
            return;
        }

        container.classList.remove('is-error');
        container.innerHTML = `
            <div class="calculent-result__value">${result !== null ? result : ''}</div>
            <p class="calculent-result__summary">${summary || ''}</p>
        `;
    }

    function attachCalculator(wrapper) {
        const type = wrapper?.dataset?.calculentType;
        if (!type || !restUrl) return;

        const form = wrapper.querySelector('.calculent-tool__form');
        const resultContainer = wrapper.querySelector('.calculent-tool__result');
        const summary = wrapper.querySelector('.calculent-tool__summary');

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = serializeForm(form);

            const response = await fetch(restUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce || '',
                },
                body: JSON.stringify({ type, payload }),
            });

            const json = await response.json();
            summary.textContent = json.summary || '';
            renderResult(resultContainer, json);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.calculent-tool').forEach(attachCalculator);
    });
})();
