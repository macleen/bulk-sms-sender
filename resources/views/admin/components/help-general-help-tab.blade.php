<div id="help" class="tab-content">
    <div class="feature-section">
      <div class="container">
        <!-- Previous sections up to "5. The Leads Panel" -->

        <h1>Sms-Bulk Sender Overview</h1>
        <p>Sms-Bulk Sender is specifically designed for large campaigns. It works for micro campaigns as well, but you can reap the true benefits when the campaigns are large. The plugin makes extensive use of the WordPress REST API, so it's essential to have WP REST API available and fully functional.</p>
        <p>If you encounter any issues, please do not hesitate to contact us; we will be glad to help.</p>
  
        <h2 style="color:aqua">Included Add-ons</h2>
        <p>Sms-Bulk Sender comes with a free SMS provider add-on from Amazon SNS and a Test child-plugin. The test plugin is specifically created so that you can see how your final SMS message will look on the receiver's end and to familiarize yourself with the different components of the plugin.</p>
  
        <h2 style="color:aqua">1. Sending Your First SMS</h2>
        <ol>
          <li>Select <strong>Start Sending</strong> from the admin menu. You will be shown all the available child-plugins, or what we call the SMS Providers add-ons. Choose <strong>Test</strong> to start exploring the possibilities.</li>
          <li>You will be presented with a dummy authentication screen. (If you select a live SMS provider, you will have to enter your API credentials in this panel.) In this case, just click <strong>Next</strong>.</li>
        </ol>
  


        <h2>2. The Account info</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/account-info.png" />
        </div>
        <p>If supported by the provider api, will have an overview of your account without the need to login into your profile area at the provider's website to see it</p>




        <h2 style="color:aqua">3. Local Settings Panel</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/local-settings.png" />
        </div>    
        <p>This panel includes the following options:</p>
        <ul>
          <li><strong>SenderID:</strong> This is the sender name that will appear on the receiver's phone (*) and it has a maximum length of 11 characters.</li>
          <li><strong>Delay Interval:</strong> This is the delay from one message to another. The larger this number is, the less likely your messages will be marked as spam.</li>
          <li><strong>Language:</strong> Use this field to differentiate users based on their language. The benefit of this field is noticeable later when the user clicks on the link in your message; you can redirect them to a page in their language, for example.</li>
          <li><strong>Country:</strong> Keep lists of users tied to each other by country. So, if you have 3 lists for 3 different countries, send them separately. The system parses the phone numbers based on this field, which is why it is mandatory.</li>
        </ul>
        <p>Now you can click <strong>Next</strong>. You will get the:</p>
  
        <h2 style="color:aqua">4. Rotational Messages Panel</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/rotational-messages-and-tags.png" />
        </div>    
        <p>Please check the tab "Pro Version" for a detailed explanation of this field.</p>
        <p>Predefined system tags are variables that you can use in your messages to get interpreted at runtime. There are 5 of them (and more are already in the making for the next release):</p>
        <ul>
          <li><span style="color: rgb(205, 215, 15);">__FULL_NAME__</span>: This will be replaced by the name from the input list. [More on this later on]</li>
          <li><span style="color: rgb(205, 215, 15);">__PHONE__</span>: This will be replaced by the phone number.</li>
          <li><span style="color: rgb(205, 215, 15);">__INDEX__</span>: This is where the link will be injected. [Read further for sub-options of this field]</li>
          <li><span style="color: rgb(205, 215, 15);">__ID__</span>: Each message will have a unique ID assigned to it which can also be injected in the message.</li>
          <li><span style="color: rgb(205, 215, 15);">__RANDOM_STR__</span>: A unique random string that can be injected to make messages look different from one another.</li>
          <li><span style="color: rgb(205, 215, 15);">__DATE__</span>: Inject today's date here</li>
          <li><span style="color: rgb(205, 215, 15);">__TIME__</span>: Current time</li>          
          <li><span style="color: rgb(205, 215, 15);">__DAY_OF_WEEK__</span>: Inject the day of the week from Monday to Sunday</li>
          <li><span style="color: rgb(205, 215, 15);">__DAY_OF_YEAR__</span>: Inject a digit from 1 to 365(366) representing the day of the year</li>          
          <li><span style="color: rgb(205, 215, 15);">__WEEK_OF_YEAR__</span>: Inject the week number from 1 to 52(53)</li>
          <li><span style="color: rgb(205, 215, 15);">__MONTH_NAME__</span>: Substitute this tag with the month name: January to December</li>          
          <li><span style="color: rgb(205, 215, 15);">__MONTH_NUMBER__</span>: Inject the month number from 1 to 12</li>
          <li><span style="color: rgb(205, 215, 15);">__CURRENT_YEAR__</span>: Current in the 4 digits format YYYY</li>          
        </ul>
        <p>For example, if your input list looks like this:</p>
        <pre><span style="color: white;background-color: #1d2023; padding: 10px 5px;">3367541245; Alina Petronella Mayer; 17th Boulevard 526A, 75412 Paris, France; alina@gmail.com; Alina Cosmetics Sarl</span></pre>
        <p>And your message template looks like this:</p>
        <pre><span style="color: white;background-color: #1d2023; padding: 10px 5px;">Dear __FULL_NAME__, We could not reach you at __PHONE__. Please log in and confirm your phone number at __INDEX__.</span></pre>
        <p>This would become at runtime:</p>
        <div style="color: white;background-color: #1d2023; padding: 10px 5px;">Dear Alina Petronella Mayer, We could not reach you at 3367541245. Please log in and confirm your phone number at https://yourwebsite.com/login</div>
  
        <h2 style="color:aqua">5. URL Linking Mode</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/url-mode.png" />
        </div>   
        <p>This offers the following options:</p>
        <ul>
          <li><strong>Indexation Option:</strong> There are 3 available values for this field:
            <ul>
              <li><strong>None:</strong> Selecting this means you are not using any web links. This is the only option where you can't use the <span style="color: rgb(87, 245, 14);">__INDEX__</span> tag in your messages.</li>
              <li><strong>Shortener:</strong> This is where the Msp (macShortenerPage) comes in. For more information on this, we have dedicated a full tab called Msp Page on this page.</li>
              <li><strong>As URL:</strong> Selecting this option will enable the URL links input area. Here you can put as many URLs as you want; they will be injected anywhere in your message where the <span style="color:rgb(104, 232, 25)">__INDEX__</span> tag is found, also on a round-robin basis. So, if you have 2 URLs, message 1 will use URL 1, message 2 will use URL 2, message 3 will use URL 1, and so on. This can tremendously lower the chance of your messages being marked as spam. Creating multiple URLs, all redirecting to the same page, will be a big boost for your campaign's success.</li>
            </ul>
          </li>
          <li><strong>Index Roll Frequency:</strong> This determines how many times a URL must be used before the system switches to the next URL in the list.</li>
          <li><strong>Shortener span style="color: white;background-color: #1d2023; padding: 10px 5px;" as a Route Argument or a Route Segment:</strong> As you might have read in the Msp tab, the shortener (when used) attaches a special span style="color: white;background-color: #1d2023; padding: 10px 5px;" to the URL. Here you can choose to have it as a query argument, like this: <span style="color:rgb(111, 188, 17);">./msp?r=xxxxx</span>, or as a route segment, like this: <span style="color: rgb(111, 188, 17);">./msp/xxxxx</span>.</li>
        </ul>
        <p>Click <strong>Next</strong>...</p>
          
        <h2>6. The Leads Panel</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/input-lists.png" />
        </div>
        <p>This is your leads input area and it has the following fields:</p>
        <ul>
          <li>
            <div style="padding-bottom: 15px;">
                <strong>Full Name:</strong> As mentioned earlier, the "Alina Petronella Mayer" will replace the <span style="color: rgb(205, 215, 15);">__FULL_NAME__</span> tag. If you prefer not to use the full name, especially when they are very long, you can select from the following options:
            </div>                    
            <ul>
              <li><strong>First Name:</strong> Only "Alina" will replace the <span style="color: rgb(205, 215, 15)">__FULL_NAME__</span> tag.</li>
              <li><strong>Second Name:</strong> Only "Petronella" will replace the <span style="color: rgb(205, 215, 15);">__FULL_NAME__</span> tag.</li>
              <li><strong>Third Name:</strong> Only "Mayer" will replace the <span style="color: rgb(205, 215, 15);">__FULL_NAME__</span> tag.</li>
              <li><strong>Skip First Part:</strong> "Petronella Mayer" will replace the <span style="color: rgb(205, 215, 15);">__FULL_NAME__</span> tag.</li>
              <li><strong>Full Name:</strong> The full "Alina Petronella Mayer" will be used.</li>
            </ul>
          </li>
          <li>
            <div style="padding-bottom: 15px;">
                <strong>Appending the List:</strong> In case you are importing users, admins, or others from your WordPress database, you can choose:
            </div>    
            <ul>
              <li><strong>Append:</strong> Keeps the existing entries in the input list and appends the newly imported lines to the end.</li>
              <li><strong>Replace:</strong> Replaces the existing entries in the input list with the newly imported lines from the database.</li>
            </ul>
          </li>
          <li><strong>Input Source:</strong> Select to import users from your WordPress database based on their role or paste an external list.</li>
        </ul>
  
        <h2>7. Lead Line Format</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/lead-list-format.png" />
        </div>
        <p>Input lists can vary in format. To accommodate different structures, the system supports multiple formats. For example:</p>
        <ul>
          <li><strong>Format A:</strong> <div style="display:inline-block;color: white;background-color: #1d2023; padding: 10px 5px;">3367541245; Alina Petronella Mayer; 17th Boulevard 526A, 75412 Paris, France; alina@gmail.com; Alina Cosmetics Sarl</div></li>
          <li><strong>Format B:</strong> <div style="display:inline-block;color: white;background-color: #1d2023; padding: 10px 5px;">Alina Petronella Mayer; 3367541245; 17th Boulevard 526A, 75412 Paris, France; alina@gmail.com; Alina Cosmetics Sarl</div></li>
          <li><strong>Format C:</strong> <div style="display:inline-block;color: white;background-color: #1d2023; padding: 10px 5px;">alina@gmail.com; Alina Petronella Mayer; 17th Boulevard 526A, 75412 Paris, France; 3367541245; Alina Cosmetics Sarl</div></li>
        </ul>
        <p>To match these formats, select the corresponding checkbox:</p>
        <ul>
          <li><strong>Format A:</strong> Select <div style="display:inline-block;color:white;background-color: #1d2023;padding: 10px 8px;">P-N-A-E</div> (Phone, Name, Address, Email).</li>
          <li><strong>Format B:</strong> Select <div style="display:inline-block;color: white;background-color: #1d2023; padding: 10px 5px;">N-P-A-E</div> (Name, Phone, Address, Email).</li>
          <li><strong>Format C:</strong> Select <div style="display:inline-block;color: white;background-color: #1d2023; padding: 10px 5px;">E-N-A-P</div> (Email, Name, Address, Phone).</li>
        </ul>
  
        <h2 style="color:aqua">8. Preview Summary</h2>
        <div style="text-align:center;margin: 15px 0;">
            <img src="{{$plugin_url}}/assets/admin/img/help/review.png" />
        </div>
        <p>This screen recapitulates all your choices for a final review. If you need to make changes, click the "Previous" button or use the sidebar menu. If everything looks correct, click "Next".</p>
  
        <h2 style="color:aqua">9. Sending Session</h2>
        <p>In this section, you will see two tabs:</p>
        <ul>
          <li><strong>Successful Messages:</strong> Displays messages that were sent successfully.</li>
          <li><strong>Failed Messages:</strong> Displays messages that failed to send.</li>
        </ul>
        <p>The sending session is pausable and resumable at any time.</p>
  
        <h2 style="color:aqua">10. Daily Log</h2>
        <p>All sent messages are available in the daily log. Log files are organized by the date the sending was started. For example, if you send a list in the morning and another at noon, they will be available in the same log. The log provides details such as the total number of messages sent that day, how many were successful, and how many failed. Clicking on the log date will give a detailed overview of each message sent that day.</p>
  
        <h2 style="color:aqua">11. Click Analytics</h2>
        <p>Available when the send mode is set to "Shortener" (i.e., when the Msp page is used). For more information, refer to the "Msp Help" tab on this page.</p>
      </div>
    </div>
  </div>
  