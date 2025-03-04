<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collision Probability Analysis for SMS References</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1 {
            color: #2c3e50;
        }
        p {
            margin-bottom: 20px;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Collision Probability Analysis for SMS References</h1>
    <p>Given the context that the plugin will be sending SMS messages, we have analyzed the probability of reference collisions based on the daily SMS sending activity of users or companies with large user bases. Here's a breakdown of the analysis:</p>

    <h2>Key Assumptions</h2>
    <ul>
        <li><strong>Number of SMS messages per user per day:</strong> A user or company sends between 1 to 10 SMS messages per day. We'll use <strong>10 SMS messages per user per day</strong> as a maximum for a large user base.</li>
        <li><strong>Number of users:</strong> We assume there are <strong>1 million users</strong>.</li>
        <li><strong>Duration:</strong> We calculate the collision risk over the course of <strong>1 year</strong> of SMS message sending.</li>
    </ul>

    <h2>Number of References (SMS Messages)</h2>
    <p>If each user sends 10 SMS messages per day, for <strong>1 million users</strong>, the number of references generated in a <strong>single day</strong> would be:</p>
    <pre>1,000,000 users × 10 messages per user per day = 10,000,000 references per day</pre>
    <p>For a <strong>year (365 days)</strong>, this would result in:</p>
    <pre>10,000,000 references per day × 365 days = 3,650,000,000 references per year</pre>
    <p>So, for 1 million users, we generate <strong>3.65 billion references</strong> in one year.</p>

    <h2>Collision Probability</h2>
    <p>The reference space is determined by the size of the base36 reference. After converting the CRC32 hash to base36, the possible reference space is:</p>
    <pre>36^8 = 2,821,109,907,456 (approximately 2.8 trillion unique references)</pre>

    <p>With <strong>3.65 billion references per year</strong> being generated, the chance of a collision is extremely low. To visualize this, we can use the <strong>Birthday Paradox</strong> to estimate the collision probability.</p>

    <h2>Using the Birthday Paradox Approximation</h2>
    <p>The approximation formula for the probability <span class="highlight">P</span> of at least one collision is:</p>
    <pre>P ≈ 1 - exp(-n² / 2N)</pre>
    <p>Where:
        <ul>
            <li><strong>n</strong> is the number of items (SMS references),</li>
            <li><strong>N</strong> is the size of the reference space.</li>
        </ul>
    </p>

    <p>Given the following values:
        <ul>
            <li><strong>n = 3,650,000,000</strong> references per year,</li>
            <li><strong>N = 2,821,109,907,456</strong> possible references.</li>
        </ul>
    </p>

    <p>The probability of a collision over the course of a year for 1 million users is <strong>negligibly low</strong>, almost approaching zero for practical purposes. Even with a large user base sending SMS messages daily, the collision risk remains insignificant.</p>

    <h2>Conclusion</h2>
    <p>For your plugin’s expected usage (sending 1–10 SMS messages per user per day), the collision risk is effectively negligible. However, if you anticipate massive growth in the future (e.g., billions of users or hundreds of millions of SMS messages per day), additional measures could be considered to handle potential collisions. For now, this reference system is well-suited for your needs and should perform effectively at scale.</p>
</body>
</html>
