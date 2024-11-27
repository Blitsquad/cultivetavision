// Au chargement de la page
$(document).ready(function() {
    // Au clic sur la flèche pour monter la page
    $('.scrollToTop').click(function() {
        // Faites défiler la page vers le haut
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });

    // Au clic sur la flèche pour descendre la page
    $('.scrollToBottom').click(function() {
        // Faites défiler la page vers le bas
        $('html, body').animate({scrollTop: $(document).height()}, 800);
        return false;
    });
});
