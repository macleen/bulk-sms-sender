# Bulk SMS Sender - Effortlessly Send SMS at Scale 🚀

Bulk SMS Sender is specifically designed for large campaigns. While it works for micro campaigns as well, you can reap the true benefits when the campaigns are large. The plugin makes extensive use of the WordPress REST API, so it's essential to have WP REST API available and fully functional.

If you encounter any issues, please do not hesitate to contact us; we will be glad to help.

---

## Table of Contents 📚

- [Overview](#overview)
- [Included Add-ons](#included-add-ons)
- [Sending Your First SMS](#sending-your-first-sms)
- [Account Info](#account-info)
- [Local Settings Panel](#local-settings-panel)
- [Rotational Messages Panel](#rotational-messages-panel)
- [URL Linking Mode](#url-linking-mode)
- [Leads Panel](#leads-panel)
- [Lead Line Format](#lead-line-format)
- [Preview Summary](#preview-summary)
- [Sending Session](#sending-session)
- [Daily Log](#daily-log)
- [Click Analytics](#click-analytics)

---

## Overview ✨

Bulk SMS Sender is designed to streamline sending SMS messages for large-scale campaigns. While it can handle micro campaigns, it truly shines with larger volumes. The plugin leverages the WordPress REST API for smooth operation. Ensure your WP REST API is enabled and functioning properly for optimal performance.

---

## Included Add-ons 🧩

Bulk SMS Sender comes with several powerful add-ons:

- **Free SMS Provider Add-on**: Integrated with InfoBip for sending SMS.
- **Test Child-Plugin**: A handy test plugin that simulates the SMS experience, allowing you to preview how the final message will appear to recipients. This is also a great way to familiarize yourself with the plugin's features.

---

## Sending Your First SMS 📨

1. From the admin menu, select **Start Sending**. You’ll be presented with a list of available child-plugins or SMS Providers add-ons. Choose **Test** to explore the plugin’s features.
2. You’ll be prompted with a dummy authentication screen. (For live SMS providers, you’ll need to enter your API credentials here.)
3. After clicking **Next**, you'll access the **Account Info** panel, providing a quick overview of your account (if supported by the provider's API) without needing to log in to the provider's website.

---

## Account Info ℹ️

The **Account Info** panel offers a high-level view of your account details, including remaining credits and usage, without the need to log in to your provider's website. If your provider supports this feature, this panel will give you all the necessary data in one place.

---

## Local Settings Panel ⚙️

This section includes several configuration options for customizing how your messages are sent:

- **SenderID**: The sender name displayed on the recipient's phone (max 11 characters).
- **Delay Interval**: The delay between messages to help avoid spam detection.
- **Language**: Differentiate recipients based on language, allowing for localized message delivery.
- **Country**: Organize lists of recipients by country for more targeted messaging.

Click **Next** after configuring these options to proceed.

---

## Rotational Messages Panel 🔄

This panel (available in the Pro version) allows you to rotate multiple message templates for added variety and reduced spam risk.

---

## URL Linking Mode 🌐

Configure how URLs are handled in your messages:

- **Indexation Option**: Choose between no link, URL shorteners, or direct URLs.
- **Index Roll Frequency**: Control how often URLs switch in the message sequence.

---

## Leads Panel 📋

This panel manages your lead data:

- **Full Name**: Choose to use the full name or just parts of it (first name, last name, etc.).
- **Input Source**: Import contacts from your WordPress database or paste an external list.

---

## Lead Line Format 📄

The plugin supports multiple formats for importing leads, accommodating variations in how data is presented. Select the appropriate format for your contact list.

---

## Preview Summary 🧐

Review all your choices before sending the message. If you need to make adjustments, you can go back using the sidebar menu. If everything looks good, click **Next** to proceed.

---

## Sending Session ⏳

During the sending session, you can monitor:

- **Successful Messages**: View all successfully sent messages.
- **Failed Messages**: Check any messages that failed to send.
- The session can be paused and resumed at any time.

---

## Daily Log 📅

All sent messages are logged by date. The log details how many messages were sent, how many were successful, and how many failed. Detailed logs are available for each day’s sends.

---

## Click Analytics 📊

When using the "Shortener" option, detailed click analytics are available. Track the performance of links within your messages for a better understanding of user interaction.

---


On the redirection page, additional data is sent via a POST request, which may include information like:

- **Country**: AI (Anguilla)
- **Recipient**: 32465888661
- **Message**: Full SMS message content
- **User Info**: Full Name, Email, Address, etc.
- **Meta Data**: Time, Date, IP, User Agent, Device Info, etc.

---

### Handling the Data 📊

If you want to access this data, you can use a function to parse it. Here’s an example function to convert dot-notated POST data into a nested array:

```php
<?php
// Function to convert dot-notation keys in $_POST into nested arrays
function parse_dot_notation_post($postData) {
    $result = [];
    
    foreach ($postData as $key => $value) {
        $keys = explode('.', $key);
        $current = &$result;
        
        foreach ($keys as $part) {
            if (!isset($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }
        
        $current = $value;
    }
    
    return $result;
}

// Example usage
$nestedArray = parse_dot_notation_post($_POST);
print_r($nestedArray); // For debugging
?>
