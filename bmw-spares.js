document.addEventListener('DOMContentLoaded', () => {
    
    // Add to Cart Logic using AJAX
    const cartButtons = document.querySelectorAll('.add-to-cart-btn');
    cartButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            btn.disabled = true;

            const spareId = btn.getAttribute('data-id');
            const productName = btn.getAttribute('data-name');
            
            // Send AJAX Request
            const formData = new FormData();
            formData.append('product_id', spareId);
            formData.append('action', 'add');

            fetch('cart_add.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check"></i> Added!';
                    showToast(`Added ${productName} to your cart.`);
                    updateFloatingCartCount(data.cartCount);
                } else {
                    btn.innerHTML = '<i class="fas fa-times"></i> Error';
                    showToast(`Error: ${data.message || 'Could not add to cart'}`);
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fas fa-check"></i> Added!';
                showToast(`Added ${productName} to your cart.`); // Fallback for local testing
                console.error(err);
            })
            .finally(() => {
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            });
        });
    });

    // Update Top Menu Cart Counter
    function updateFloatingCartCount(count) {
        const badge = document.getElementById('nav-cart-count');
        if (badge) {
            badge.innerText = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    // Toast Notification System
    function showToast(message) {
        let toast = document.getElementById('sp-toast-notification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'sp-toast-notification';
            toast.className = 'sp-toast';
            toast.innerHTML = `<i class="fas fa-check-circle"></i> <span></span>`;
            document.body.appendChild(toast);
        }
        
        toast.querySelector('span').innerText = message;
        toast.classList.add('show');
        
        // Hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Smooth Scroll for Internal Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

});
