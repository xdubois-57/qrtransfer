document.addEventListener('DOMContentLoaded', function() {
    // Check if Web Share API is supported
    if (navigator.share) {
        document.querySelectorAll('[data-share]').forEach(button => {
            button.classList.add('share-supported');
            button.addEventListener('click', async function() {
                try {
                    const image = this.dataset.image || document.querySelector('#qr-image').src;
                    const title = this.dataset.title || 'QR Transfer';
                    
                    // Fetch the image and convert it to a blob
                    const response = await fetch(image);
                    const blob = await response.blob();
                    const file = new File([blob], 'qr-code.png', { type: 'image/png' });

                    await navigator.share({
                        title: title,
                        text: 'Scan with your banking app!',
                        files: [file]
                    });
                } catch (error) {
                    console.error('Error sharing:', error);
                }
            });
        });
    }

    // Update share button when new QR code is generated
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                const shareButton = document.querySelector('#share-qr');
                if (shareButton) {
                    const newSrc = mutation.target.getAttribute('src');
                    shareButton.dataset.image = newSrc;
                }
            }
        });
    });

    const qrImage = document.querySelector('#qr-image');
    if (qrImage) {
        observer.observe(qrImage, {
            attributes: true,
            attributeFilter: ['src']
        });
    }
});
