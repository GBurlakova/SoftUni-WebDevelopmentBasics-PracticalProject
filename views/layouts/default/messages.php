<?php
if (isset($_SESSION['messages'])): ?>
    <?php foreach($_SESSION['messages'] as $msg):?>
    <script>
        var notifier = new notifier();
        var messageType = <?php echo json_encode($msg['type'])?>;
        var messageText = <?php echo json_encode(htmlspecialchars($msg['text']))?>;
        notifier.showMessage(messageText, messageType);
    </script>
    <?php endforeach;
    unset($_SESSION['messages']);
endif; ?>