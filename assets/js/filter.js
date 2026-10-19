document.addEventListener('DOMContentLoaded', function() {
    const applyBtn = document.getElementById('apply-filters');
    const productContainer = document.getElementById('product-container');

    if (!applyBtn || !productContainer) return;

    applyBtn.addEventListener('click', async function() {
        // Show loading state
        applyBtn.innerText = 'Filtering...';
        applyBtn.disabled = true;
        productContainer.style.opacity = '0.5';

        const category = document.getElementById('filter-category').value;
        const size = document.getElementById('filter-size').value;
        const condition = document.getElementById('filter-condition').value;
        const minPrice = document.getElementById('filter-min-price').value;
        const maxPrice = document.getElementById('filter-max-price').value;

        const params = new URLSearchParams({
            category,
            size,
            condition,
            min_price: minPrice,
            max_price: maxPrice
        });

        try {
            const response = await fetch(`actions/filter_products.php?${params.toString()}`);
            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            productContainer.innerHTML = html;
        } catch (error) {
            console.error('Error filtering products:', error);
            alert('Something went wrong while filtering products. Please try again.');
        } finally {
            applyBtn.innerText = 'Apply Filters';
            applyBtn.disabled = false;
            productContainer.style.opacity = '1';
        }
    });
});
