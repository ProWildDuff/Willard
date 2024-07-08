document.addEventListener('DOMContentLoaded', function() {
    // Create the back-to-top button dynamically
    var backButton = document.createElement('a');
    backButton.href = '#';
    backButton.className = 'willard-top-of-page';
    var img = document.createElement('img');
    img.src = willard_vars.back_to_top_icon;
    backButton.appendChild(img);
    
    // Initially hide the button
    backButton.style.display = 'none';
    
    // Append the button to the body
    document.body.appendChild(backButton);

    // Function to control visibility of the back-to-top button
    function toggleBackToTopButton() {
        var scrollPosition = window.scrollY;
        var windowHeight = window.innerHeight;

        // Adjust this value according to your requirement (100vh in this case)
        var scrollThreshold = windowHeight;

        if (scrollPosition > scrollThreshold) {
            backButton.style.display = 'block';
        } else {
            backButton.style.display = 'none';
        }
    }

    // Scroll to top when the button is clicked
    backButton.addEventListener('click', function(event) {
        event.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    });

    // Show/hide back-to-top button based on scroll position
    window.addEventListener('scroll', function() {
        // Check if the window width is larger than 768px (not on mobile)
        if (window.innerWidth > 768) {
            toggleBackToTopButton();
        }
    });
});