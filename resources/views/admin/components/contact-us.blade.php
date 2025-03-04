  <!-- Contact Form Section -->
  <div class="feature-section">
    <div class="container">
        <h2><i class="fas fa-envelope icon"></i> Get in Touch with Us</h2>
        <p>Have questions, suggestions, or need support? We're here to help! Fill out the form below, and we'll get back to you as soon as possible.</p>

        <form id="msform" id="contact-form" method="post">
            <div class="form-group">
                <input type="text" id="contact-name" name="contact_name" placeholder="Your Name" style="background-color: #c6e99a;">
            </div>

            <div class="form-group">
                <input type="email" id="contact-email" name="contact_email" placeholder="Your Email" style="background-color: #c6e99a;">
            </div>

            <div class="form-group">
                <textarea id="contact-message" name="contact_message" rows="5" required placeholder="Your Message" style="background-color: #c6e99a;padding-left:15px !important;"></textarea>
            </div>

            <button type="submit" class="action-button" name="send_contact_message" style="width:auto;"><i class="fas fa-paper-plane icon"></i> Send Message</button>
        </form>

        @php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_contact_message'])) {
            $name = sanitize_text_field($_POST['contact_name']);
            $email = sanitize_email($_POST['contact_email']);
            $message = sanitize_textarea_field($_POST['contact_message']);

            $subject = "New Contact Message from $name";
            $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
            $headers = "From: $name <$email>";

            if (wp_mail('acutclub@gmail.com', $subject, $body, $headers)) {
                echo '<p class="success-message" style="color:#21f112 !important;font-size:16px; !important"><i class="fas fa-check-circle icon"></i> Your message has been successfully sent!</p>';
            } else {
                echo '<p class="error-message" style="color:#f23232 !important;font-size:16px; !important"><i class="fas fa-times-circle icon"></i> Something went wrong. Please try again.</p>';
            }
        }
        @endphp
    </div>
</div>
