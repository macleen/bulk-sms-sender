<!-- Pro Version Content -->
<div id="pro" class="tab-content">
    <div class="feature-section" style="color: #ffffffb3;font-size: 2rem;">
        <div class="container" style="background-color: #181a1ca6;">
            <h1 class="page-title" style="color:#65e114c7;font-size:1.6rem;font-weight:600;">The Pro version</h1>
            <p>On top of the basic version, the pro version includes the following features:</p>
        </div>    
    </div>

    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-anchor icon"></i> Anchored Messages Using Multiple Indexing Hosts</h1>
            <p>Imagine you run an e-commerce store selling t-shirts, and you have a customer list of 5,000 people. You want to inform them about an exclusive <strong>35% discount on your summer collection</strong> starting next week. Your message might look like this:</p>
            
            <div class="highlight">
                <strong>Dear {name}, we have a 35% discount on the summer collection from 15/01/25 to 30/01/25. Click here {index} to check it out!</strong>
            </div>
            
            <h2>*** The Problem: Spam Filters & Message Blocking ***</h2>
            <p>Sending 5,000 nearly identical messages can quickly trigger spam filters, causing your SMS campaign to be <strong>blocked after just 100–200 messages</strong>. Worse still, your SMS provider will continue charging you for every message—even those that never reach your customers.</p>
            
            <p>If you contact your provider to ask why so many messages failed, their response will likely be:</p>
            
            <div class="highlight-dark">
                <em>"It wasn't us; your messages were blocked by the receiving network."</em>
            </div>
            
            <p>That means you've <strong>lost money on undelivered messages</strong>, and your campaign's success has been severely impacted.</p>
            
            <h2>The Solution: Using Multiple Shortened Links</h2>
            <p>To reduce the chances of being flagged as spam, our plugin allows you to <strong>rotate multiple shortened links</strong> in your messages. Here's how it works:</p>
            
            <ul>
                <li><strong>Create several shortened URLs</strong> that all point to your promotional page.</li>
                <li><strong>Enter these links into the "Index Input" area</strong> (one per line).</li>
                <li><strong>The system will automatically replace {index} with a different link</strong> in each message, cycling through them in a round-robin pattern.</li>
            </ul>
            
            <p>For example, if you have <strong>10 different shortened links</strong>, each unique link will only appear once every 11 messages. This means:</p>
            <ul>
                <li>The same link won't appear too frequently.</li>
                <li>Your messages will look slightly different, making them <strong>less likely to trigger spam filters</strong>.</li>
            </ul>
            
            <h2>Combining with Rotational Messaging</h2>
            <p>If you also enable <strong>rotational messaging</strong>, where <strong>four different message templates</strong> are rotated, the final effect is:</p>
            <ul>
                <li>Each identical message appears only <strong>once every 45 messages</strong>.</li>
            </ul>
            
            <p style="color:#34ec12;">
                This <strong>significantly lowers the chances of spam detection</strong>, making it more likely that your messages <strong>actually reach your customers</strong>.
            </p>
            
            <div class="highlight">
                <strong><i class="fas fa-info-circle icon"></i> While no method can 100% guarantee bypassing spam filters, this approach drastically reduces the risk—helping you maximize reach and <em>avoid unnecessary SMS costs</em>.</strong>
            </div>
            
            <p class="footer">For more details on rotational messaging, see the next section.</p>
        </div>
    </div>
    
    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-sync-alt icon"></i> Enhancing Message Delivery: 4-Cycle Round-Robin Messaging</h1>
            <p>Spam filters are highly sensitive to repetitive messages, especially when they detect identical content sent in bulk. To further reduce the chances of being flagged, our plugin introduces <strong>4-cycle round-robin rotational messaging</strong>—a system that dynamically alternates between multiple message templates.</p>
            
            <p>This strategy increases message variation and makes it harder for spam filters to detect patterns.</p>
            
            <h2>How It Works:</h2>
            <p>Instead of sending the same message to all customers, you can define <strong>4 different message templates</strong>, and the system will rotate through them sequentially:</p>
            
            <ul>
                <li><strong>Message 1 ? Message 2 ? Message 3 ? Message 4 ? (Repeat the cycle)</strong></li>
            </ul>
            
            <p>This means that no two consecutive messages will be identical, making your campaign appear more natural and <strong>significantly reducing spam detection risks</strong>.</p>
            
            <h2>Combining with Multi-Link Rotation</h2>
            <p>For <strong>maximum effectiveness</strong>, you can combine <strong>4-cycle messaging</strong> with <strong>multiple shortened links</strong>:</p>
            
            <ul>
                <li>Instead of just rotating links, your <strong>entire message structure changes every 4 messages</strong>.</li>
                <li>This ensures <strong>higher variation</strong> across a campaign of thousands of messages.</li>
                <li>The same exact message will only appear <strong>once every 45 messages</strong> (when also using link rotation).</li>
            </ul>
            
            <h2>Why This Works</h2>
            <ul>
                <li> <strong>Mimics natural conversation patterns</strong> ? Unlike robotic, identical messages.</li>
                <li> <strong>Breaks repetitive patterns</strong> ? Less likely to trigger SMS carrier spam filters.</li>
                <li> <strong>Maximizes deliverability</strong> ? Ensuring <strong>more messages reach customers</strong> instead of being blocked.</li>
                <li> <strong>Reduces wasted costs</strong> ? No more paying for messages that never get delivered.</li>
            </ul>
            
            <div class="highlight">
                <strong><i class="fas fa-lightbulb icon"></i> By using 4-cycle round-robin messaging, you're not just increasing the reach of your campaign—you're also maximizing its impact and cost-efficiency. ??</strong>
            </div>
        </div>
    </div>
    
    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-link icon"></i> Built-in URL Marker, Shortener & Redirector</h1>
            <p>Managing large contact lists and sending personalized SMS campaigns can be challenging. This is especially true when dealing with external lead sources—whether purchased or scraped—where you lack a structured database.</p>
            
            <h2>The Challenge: Handling Large Lead Lists</h2>
            <p>Imagine you have a CSV file with <strong>100,000 contacts</strong>, where each line contains:</p>
            
            <div class="highlight">
                <em>+3312456789; Jhon Doe; jhondoe@example.com; streetName house nbr, cityName, zipCode, country</em>
            </div>
            
            <p>You want to send an SMS inviting them to a promotional page, but you only need the phone number and first name. Your message might look like this:</p>
            
            <div class="highlight">
                <strong>Dear {name}, we have a 35% discount on the summer collection from 15/01/25 to 30/01/25. Click here {index} to check it out!</strong>
            </div>
            
            <p>However, what about the rest of the data?</p>
            
            <h2>Why Traditional Methods Don't Work</h2>
            <ul>
                <li><i class="fas fa-clock icon"></i> <strong>Manually importing all data</strong> into a database before sending messages is <strong>time-consuming and inefficient</strong>.</li>
                <li><i class="fas fa-sms icon"></i> <strong>Including all data in the SMS</strong> makes messages long, unprofessional, and <strong>significantly increases sending costs</strong>.</li>
            </ul>
            
            <h2>The Smart Solution: Built-in Shortener & Redirector</h2>
            <p>Instead of sending unnecessary data via SMS or manually importing it first, our plugin introduces a <strong>built-in URL shortener with data tagging</strong>, now backed by an advanced <strong>hashed storage system</strong>.</p>
            
            <h2>How It Works:</h2>
            <ol>
                <li style="padding-bottom:8px;"><i class="fas fa-cogs icon"></i> When setting up your campaign, enter your <strong>promotional page URL</strong> (e.g., <em>https://mywebsite.com/promotion</em>) in the <strong>redirection URL field</strong> on the settings page.</li>
                <li><i class="fas fa-link icon"></i> Keep the <strong>Index Links area</strong> empty and select "<strong>Shortener</strong>" before proceeding.</li>
                <li><i class="fas fa-magic icon"></i> The system will automatically generate a <strong>unique shortened URL</strong> for each recipient, embedding their details in a secure, high-performance hashed storage.</li>
            </ol>
            
            <p>Your SMS will now look like this:</p>
            
            <div class="highlight">
                <strong>Dear Jhon, we have a 35% discount on the summer collection from 15/01/25 to 30/01/25. Click here <em>https://mywebsite.com/msp/a45m7</em> to check it out!</strong>
            </div>
            
            <h2>What Happens When They Click?</h2>
            <ul>
                <li><i class="fas fa-tag icon"></i> The <strong>"a45m7"</strong> tag dynamically maps to the user's full data, securely stored with lightning-fast indexing.</li>
                <li><i class="fas fa-redo-alt icon"></i> The <strong>"msp"</strong> page (auto-generated) processes the click and <strong>redirects the user</strong> to your main promotion page.</li>
                <li><i class="fas fa-paper-plane icon"></i> A <strong>POST request</strong> sends all stored user data to your promotional page.</li>
                <li><i class="fas fa-user-check icon"></i> You can now <strong>personalize their experience</strong> and save only users who clicked the link into your database.</li>
            </ul>
            
            <p>
                <span style="color:red">NB</span>: You can change the shortener page name from the default "msp" to anything else by going to the settings 
                page and modifying the shortener name field. If you do change the default name, deactivate 
                and reactivate the plugin for the changes to be reflected. Try to keep the new name as short as possible for more concise SMS messages.
            </p>
            
            <h2>Why This Is a Game Changer</h2>
            <ul>
                <li><i class="fas fa-database icon"></i> <strong>You only store engaged users</strong>—saving time and resources.</li>
                <li><i class="fas fa-user-tag icon"></i> <strong>Personalized experience</strong>—visitors see their data, making the message feel custom-tailored.</li>
                <li><i class="fas fa-sms icon"></i> <strong>No extra SMS costs</strong>—you keep messages short and professional.</li>
                <li><i class="fas fa-bolt icon"></i> <strong>Lightning-fast searches</strong>—thanks to the advanced indexing of the hashed storage, finding user data is incredibly quick.</li>
                <li><i class="fas fa-hdd icon"></i> <strong>Massive storage capacity</strong>—the new hashed storage can theoretically support 36 pow 8 = 2,821,109,907,456 <span style="color:yellow">~2.8 trillion unique entries</span>.</li>
                <li><i class="fas fa-trash-alt icon"></i> <strong>One-click removal</strong>—reset all stored data with a single click, keeping your system clean and efficient.</li>
            </ul>
            
            <div class="highlight">
                <strong><i class="fas fa-exclamation-circle icon"></i> Note: When using this option, the rotating index feature will be disabled.</strong>
            </div>
        </div>
    </div>
    
    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-file-alt icon"></i> Logging & Session Overview</h1>
            <p>The <strong>Pro Version</strong> includes an advanced logging feature, allowing you to track and analyze your sending sessions efficiently.</p>
            
            <h2><i class="fas fa-hdd icon"></i> Disk-Based Logging</h2>
            <p>Instead of cluttering your database, all logs are <strong>saved to a structured disk file</strong>. This ensures optimal performance while keeping a detailed record of your sending activity.</p>
            
            <h2><i class="fas fa-download icon"></i> Downloadable Session Overview</h2>
            <p>Once the sending session is complete, you'll have the option to <strong>download a detailed session report</strong> as a PDF.</p>
            <p>
                Our plugin offers a comprehensive, date-based logging system that provides a clear overview of 
                your message-sending history. You'll first see a list of available dates, with each entry 
                showing the total number of messages sent, along with a breakdown of successful and failed 
                messages. Clicking on a date reveals more detailed logs for each individual message sent 
                that day, summarized for quick reference. For in-depth insights, simply click on a specific 
                message to view its full details — including the request object, server response, and the 
                SMS provider's return status code and message.
            </p>
            <p>    
                Global logging overview<br>
                <img src="{{$plugin_url}}/assets/admin/img/help/logging.png" style="width:100%;" />
            </p>
            <p>    
                Details about an individual sms message<br>
                <img src="{{$plugin_url}}/assets/admin/img/help/logging-details.png" style="width:100%;"/>
            </p>

            <h2><i class="fas fa-redo-alt icon"></i> Easy Resending for Failed Messages</h2>
            <p>Simply copy the failed numbers from the report, paste them back into the leads input area, and <strong>attempt to resend</strong>—ensuring maximum message delivery.</p>
        </div>
    </div>

    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-chart-line icon"></i> Analytics & Click Tracking</h1>
            <p>The <strong>Pro Version</strong> comes with a powerful analytics feature, allowing you to track user interactions and gather meaningful insights from your SMS campaigns.</p>
    
            <h2><i class="fas fa-bullseye icon"></i> Click Tracking for Shortened Links</h2>
            <p>Every SMS message sent with the <strong>shortener option</strong> enabled is automatically marked for click tracking. When a recipient clicks the link, their interaction is logged along with a wealth of data about their device and session.</p>
    
            <h2><i class="fas fa-list icon"></i> Detailed Tracking Information</h2>
            <p>
                When a user clicks a tracked link, the following data is recorded:
            </p>
            <ul>
                <li><strong>Full Name:</strong> Jhon Doe</li>
                <li><strong>Email:</strong> jhondoe@gmail.com</li>
                <li><strong>Address:</strong> 17 Astrid Street, 1023 Schaarbeek, Brussels Belgium</li>
                <li><strong>Other Info:</strong> company: tmc international</li>
                <li><strong>Time:</strong> 08:06:01</li>
                <li><strong>Date:</strong> 26-02-2025</li>
                <li><strong>IP Address:</strong> 123.123.123.123</li>
                <li><strong>User Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit...</li>
                <li><strong>Referrer:</strong> Direct</li>
                <li><strong>Preferred Language:</strong> en-US,en;q=0.9,nl;q=0.8,fr;q=0.7,de;q=0.6</li>
                <li><strong>Device Type:</strong> browser</li>
                <li><strong>Device Name:</strong> Chrome</li>
                <li><strong>Device Version:</strong> 133.0.0.0</li>
                <li><strong>Operating System:</strong> Windows</li>
                <li><strong>OS Device Type:</strong> desktop</li>
            </ul>
    
            <p>All this data is seamlessly collected and stored in the hashed storage system — no database involved — ensuring fast access and high performance.</p>
    
            <h2><i class="fas fa-random icon"></i> Redirection & Data Access</h2>
            <p>Upon clicking a tracked link, users are automatically redirected to the destination page specified in the <strong>Redirect to</strong> field on your settings page. At the same time, all collected data is sent in a POST request, making it available for further processing.</p>
    
            <p>For developers needing to handle this data <strong style="color:yellow;">programmatically</strong>, visit the <strong style="color:yellow;">General Help</strong> tab for a ready-to-use PHP function that parses the incoming data and lets you use it however you want.</p>
    
            <p>Global analytics overview<br>
                <img src="{{$plugin_url}}/assets/admin/img/help/analytics.png" style="width:100%;" />
            </p>
    
            <p>Details about an individual click event<br>
                <img src="{{$plugin_url}}/assets/admin/img/help/analytics-details.png" style="width:100%;" />
            </p>
        </div>
    </div>

    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-database icon"></i> Sync Database Tables with Bulk SMS Sender</h1>
            <p>The <strong>Pro Version</strong> allows you to seamlessly sync all your database tables containing a phone number column with the Bulk SMS Sender. This feature ensures that your user data is always up-to-date and ready for SMS campaigns.</p>
            
            <h2><i class="fas fa-cogs icon"></i> Automatic Phone Column Detection</h2>
            <p>Bulk SMS Sender will automatically scan your database for potential phone number columns. It will present you with a list of detected columns and ask for your permission to sync them. You can choose to sync all or only specific columns.</p>
            
            <h2><i class="fas fa-user-plus icon"></i> User Registration & Profile Integration</h2>
            <p>A <strong>required phone number field</strong> will be added to the user registration form, user addition form in the admin dashboard, and the user profile editing page. This ensures that all new and existing users have a valid phone number associated with their account.</p>
            
            <div class="highlight" style="background: #1e8cbe40;">
                <strong><i class="fas fa-sync-alt icon"></i> Synced Data Availability:</strong>
                <ul style="padding-top: 15px;">
                    <li><i class="fas fa-check-circle icon"></i> Synced phone numbers will be available as input for SMS sending sessions.</li>
                    <li><i class="fas fa-check-circle icon"></i> You can manage and update synced data directly from the Bulk SMS Sender interface.</li>
                </ul>
            </div>
            
            <h2><i class="fas fa-shield-alt icon"></i> Data Security & Privacy</h2>
            <p>Your data is handled with the utmost care. Bulk SMS Sender ensures that all synced data is securely stored and only accessible to authorized users.</p>
        </div>
    </div>

    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-users icon"></i> Input Source - Select Users from WordPress Database</h1>
            <p>In addition to the traditional copy-paste method for adding leads, the <strong>Pro Version</strong> allows you to select users directly from your WordPress database. This feature simplifies the process of targeting specific user groups for your SMS campaigns.</p>
            
            <h2><i class="fas fa-filter icon"></i> Filter Users by Role</h2>
            <p>You can filter and select users based on their WordPress user roles. Whether you want to target administrators, editors, subscribers, or custom roles, Bulk SMS Sender makes it easy to narrow down your audience.</p>
            
            <h2><i class="fas fa-table icon"></i> Bulk Selection & Export</h2>
            <p>Easily select multiple users at once and export their phone numbers for use in your SMS campaigns. The selected users will be added to your sending session input list automatically.</p>
            
            <div class="highlight" style="background: #1e8cbe40;">
                <strong><i class="fas fa-check-circle icon"></i> Key Benefits:</strong>
                <ul style="padding-top: 15px;">
                    <li><i class="fas fa-check-circle icon"></i> Save time by selecting users directly from your WordPress database.</li>
                    <li><i class="fas fa-check-circle icon"></i> Target specific user roles for personalized campaigns.</li>
                    <li><i class="fas fa-check-circle icon"></i> Ensure accuracy by using verified phone numbers from your database.</li>
                </ul>
            </div>
            
            <h2><i class="fas fa-shield-alt icon"></i> Data Privacy Compliance</h2>
            <p>Bulk SMS Sender ensures that all user data is handled in compliance with data privacy regulations. Only authorized users can access and export user information.</p>
        </div>
    </div>            

    <div class="feature-section">
        <div class="container">
            <h1><i class="fas fa-star icon"></i> Pro Version Benefits</h1>
            <p>Upgrading to the <strong>Pro Version</strong> comes with exclusive benefits designed to enhance your experience and ensure long-term value.</p>
            
            <h2><i class="fas fa-sync-alt icon"></i> Lifetime Free Updates</h2>
            <p>Pro users receive <strong>lifetime access to all updates</strong>, ensuring that your plugin remains compatible with the latest WordPress versions, security patches, and feature improvements—<em>without any extra cost!</em></p>
            
            <h2><i class="fas fa-headset icon"></i> One Year of Free Support</h2>
            <p>To help you get the most out of your plugin, Pro users also receive <strong>one full year of priority support</strong>. Our dedicated team is available to assist you with any questions, troubleshooting, or technical guidance.</p>
            
            @if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_BASIC__ )
                @include('admin.components.get-the-pro-version-footer')
            @endif    
        </div>
    </div>
</div>