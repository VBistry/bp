<?php if (!empty($_SESSION['flash'])): ?>
    <div class="notification__container" id="toast-container">
        
        <?php foreach ($_SESSION['flash'] as $flash): ?>
            <div class="notification notification--<?php echo htmlspecialchars($flash['type']); ?>">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endforeach; ?>
        
    </div>
    
    <?php
        unset($_SESSION['flash']);
    ?>
<?php endif; ?>