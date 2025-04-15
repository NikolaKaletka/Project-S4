 <!-- Footer -->
 <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3><?php echo $config['site']['name']; ?></h3>
                    <p>Votre compagnon de voyage idéal pour planifier des aventures inoubliables.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <h4>Assistance</h4>
                    <ul class="footer-links">
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h4>Contact</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Rue du Voyage, Paris</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-envelope"></i> <?php echo $config['site']['email']; ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $config['site']['name']; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bouton de chat flottant -->
    <div class="chat-button" onclick="window.location.href='chat.php'">
        <i class="fas fa-comments"></i>
    </div>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($custom_script)): ?>
    <script>
        <?php echo $custom_script; ?>
    </script>
    <?php endif; ?>
</body>
</html>
