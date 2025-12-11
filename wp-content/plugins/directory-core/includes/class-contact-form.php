<?php
/**
 * Class: Contact Form Manager
 * 
 * Handles the display and processing of the contact form.
 */
class Contact_Form_Manager
{
    public static function init()
    {
        add_shortcode('directory_contact_form', array(__CLASS__, 'render_contact_form'));
        add_action('wp_ajax_directory_submit_contact', array(__CLASS__, 'handle_contact_submission'));
        add_action('wp_ajax_nopriv_directory_submit_contact', array(__CLASS__, 'handle_contact_submission'));
    }

    /**
     * Render the contact form shortcode
     */
    public static function render_contact_form($atts)
    {
        ob_start();
        ?>
        <div class="directory-contact-form-container glass-panel p-6">
            <form id="directory-contact-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('directory_contact_nonce', 'security'); ?>
                <div class="directory-form-group">
                    <label for="contact_name"><?php _e('Name', 'directory-core'); ?></label>
                    <input type="text" name="contact_name" id="contact_name" required>
                </div>

                <div class="directory-form-group">
                    <label for="contact_email"><?php _e('Email', 'directory-core'); ?></label>
                    <input type="email" name="contact_email" id="contact_email" required>
                </div>

                <div class="directory-form-group">
                    <label for="contact_reason"><?php _e('Reason for Contact', 'directory-core'); ?></label>
                    <select name="contact_reason" id="contact_reason">
                        <option value="General Inquiry"><?php _e('General Inquiry', 'directory-core'); ?></option>
                        <option value="Support"><?php _e('Support', 'directory-core'); ?></option>
                        <option value="Advertising"><?php _e('Advertising', 'directory-core'); ?></option>
                    </select>
                </div>

                <div class="directory-form-group">
                    <label for="contact_message"><?php _e('Message', 'directory-core'); ?></label>
                    <textarea name="contact_message" id="contact_message" rows="5" required></textarea>
                </div>

                <div class="directory-form-group">
                    <label for="contact_file"><?php _e('Attachment (Optional)', 'directory-core'); ?></label>
                    <input type="file" name="contact_file" id="contact_file">
                </div>

                <div class="form-response"></div>

                <button type="submit" class="btn btn-primary">
                    <?php _e('Send Message', 'directory-core'); ?>
                </button>
            </form>

            <script>
                jQuery(document).ready(function ($) {
                    $('#directory-contact-form').on('submit', function (e) {
                        e.preventDefault();
                        var formData = new FormData(this);
                        formData.append('action', 'directory_submit_contact');

                        var $form = $(this);
                        var $response = $form.find('.form-response');
                        $form.find('button').prop('disabled', true).text('Sending...');

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response.success) {
                                    $response.html('<div class="success-message" style="color:green; margin-bottom:10px;">' + response.data + '</div>');
                                    $form[0].reset();
                                } else {
                                    $response.html('<div class="error-message" style="color:red; margin-bottom:10px;">' + response.data + '</div>');
                                }
                            },
                            error: function () {
                                $response.html('<div class="error-message" style="color:red; margin-bottom:10px;">An error occurred. Please try again.</div>');
                            },
                            complete: function () {
                                $form.find('button').prop('disabled', false).text('<?php _e('Send Message', 'directory-core'); ?>');
                            }
                        });
                    });
                });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle AJAX Submission
     */
    public static function handle_contact_submission()
    {
        check_ajax_referer('directory_contact_nonce', 'security');

        $name = sanitize_text_field($_POST['contact_name']);
        $email = sanitize_email($_POST['contact_email']);
        $reason = sanitize_text_field($_POST['contact_reason']);
        $message = sanitize_textarea_field($_POST['contact_message']);

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(__('All fields are required.', 'directory-core'));
        }

        // Handle File Upload
        $attachment = '';
        if (!empty($_FILES['contact_file']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('contact_file', 0);
            if (is_wp_error($attachment_id)) {
                // Log error but proceed
            } else {
                $attachment = get_attached_file($attachment_id);
            }
        }

        // Send Email
        $to = get_option('admin_email');
        $subject = "New Contact Form Submission: $reason";
        $body = "Name: $name\nEmail: $email\nReason: $reason\n\nMessage:\n$message";
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        $headers[] = 'From: ' . $name . ' <' . $email . '>';

        $sent = wp_mail($to, $subject, $body, $headers, $attachment);

        if ($sent) {
            wp_send_json_success(__('Message sent successfully!', 'directory-core'));
        } else {
            wp_send_json_error(__('Failed to send message. Please try again.', 'directory-core'));
        }
    }
}
