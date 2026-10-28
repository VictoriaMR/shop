<?php $this->load('common/header_all'); ?>

<main class="notfound-shell">
    <div class="notfound-code">404</div>
    <h1 class="notfound-title">Page Not Found</h1>
    <p class="notfound-desc">Sorry, the page you are looking for might have been moved, renamed, or is temporarily unavailable.</p>
    <div class="notfound-actions">
        <a href="<?php echo url('index'); ?>" class="btn-nf-primary">Back to Homepage</a>
        <a href="<?php echo url('newIn'); ?>" class="btn-nf-secondary">Explore New Arrivals</a>
    </div>
</main>
