<?php $this->load('common/header_all'); ?>

<main class="contact-shell container">
    <header class="contact-header">
        <h1>Contact Customer Care</h1>
        <p>Have a question about an order, sizing, or returns? Our dedicated support specialists are here to help you.</p>
    </header>

    <div class="contact-grid">
        <div class="contact-info-stack">
            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div class="contact-channel-body">
                    <h2>Email Support</h2>
                    <p>Send us an inquiry anytime and receive a detailed response within 24 business hours.</p>
                    <a href="mailto:service@<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'test.cn', ENT_QUOTES, 'UTF-8'); ?>">service@<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'test.cn', ENT_QUOTES, 'UTF-8'); ?></a>
                </div>
            </div>

            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="contact-channel-body">
                    <h2>Service Hours</h2>
                    <p>Monday &ndash; Friday: 9:00 AM &ndash; 6:00 PM (EST)<br>Weekend inquiries are prioritized every Monday morning.</p>
                </div>
            </div>

            <div class="contact-channel-card">
                <div class="contact-channel-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="contact-channel-body">
                    <h2>Help Center &amp; FAQs</h2>
                    <p>Find instant answers regarding shipping times, return eligibility, and order tracking.</p>
                    <a href="<?php echo url('faq'); ?>">Visit Help Center &rarr;</a>
                </div>
            </div>
        </div>

        <section class="contact-form-card">
            <h2>Send Us a Message</h2>
            <p>Fill out the form below and our team will get back to you promptly.</p>

            <div id="js-contact-feedback" class="contact-feedback" role="status"></div>

            <form id="js-contact-form">
                <div class="contact-field-grid">
                    <div class="contact-field">
                        <label for="ct-name">Your Name *</label>
                        <input type="text" id="ct-name" name="name" placeholder="Jane Doe" required>
                    </div>
                    <div class="contact-field">
                        <label for="ct-email">Email Address *</label>
                        <input type="email" id="ct-email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="contact-field">
                    <label for="ct-subject">Inquiry Topic</label>
                    <select id="ct-subject" name="subject">
                        <option value="Order Status &amp; Tracking">Order Status &amp; Tracking</option>
                        <option value="Returns &amp; Exchanges">Returns &amp; Exchanges</option>
                        <option value="Product &amp; Sizing Advice">Product &amp; Sizing Advice</option>
                        <option value="Payment &amp; Billing">Payment &amp; Billing</option>
                        <option value="Other Inquiry">Other Inquiry</option>
                    </select>
                </div>

                <div class="contact-field">
                    <label for="ct-content">Message *</label>
                    <textarea id="ct-content" name="content" placeholder="Please include your Order # if applicable..." required></textarea>
                </div>

                <button type="submit" class="btn-contact-submit" id="js-contact-submit">Send Message</button>
            </form>
        </section>
    </div>
</main>
