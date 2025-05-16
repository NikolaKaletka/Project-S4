 <!-- Footer -->
 <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3><?php echo isset($config['site']['name']) ? $config['site']['name'] : 'TravelDream'; ?></h3>
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
                        <li><i class="fas fa-envelope"></i> <?php echo isset($config['site']['email']) ? $config['site']['email'] : 'contact@traveldream.com'; ?></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo isset($config['site']['name']) ? $config['site']['name'] : 'TravelDream'; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bouton de chat flottant -->
    <div class="chat-button" id="chatButton">
        <i class="fas fa-comments"></i>
    </div>

    <!-- Conteneur de chat flottant -->
    <div class="chat-container" id="chatContainer" style="display: none;">
        <div class="chat-header">
            <span>Assistant de Voyage</span>
            <button id="closeChat"><i class="fas fa-times"></i></button>
        </div>
        <iframe src="chat.php" id="chatFrame" frameborder="0"></iframe>
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

    <style>
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 999;
            transition: all 0.3s ease;
        }

        .chat-button:hover {
            transform: scale(1.1);
            background-color: var(--accent-color);
        }

        .chat-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            height: 500px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header button {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        #chatFrame {
            flex: 1;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatButton = document.getElementById('chatButton');
            const chatContainer = document.getElementById('chatContainer');
            const closeChat = document.getElementById('closeChat');

            chatButton.addEventListener('click', function() {
              chatContainer.style.display === 'flex' ?
                chatContainer.style.display = 'none' :
                chatContainer.style.display = 'flex';
            });

            closeChat.addEventListener('click', function() {
                chatContainer.style.display = 'none';
            });
        });
    </script>
</body>
</html>
