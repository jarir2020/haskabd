<?php
/**
 * Update CreatePage content for Organic Shop ecommerce
 * Pages: Privacy Policy, Terms & Conditions, Return Policy, Delivery Rules, Order Procedure
 * Created: 2026-04-15
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CreatePage;

// ──────────────────────────────────────────────────────────────────────────────
$privacyPolicy = <<<'HTML'
<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 900px; margin: auto;">

  <p>At <strong>Organic Shop</strong>, your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information when you visit or make a purchase from our website.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">1. Information We Collect</h3>
  <ul>
    <li><strong>Personal Details:</strong> Name, phone number, email address, and delivery address provided during registration or checkout.</li>
    <li><strong>Order Information:</strong> Products ordered, transaction history, and payment method (we do not store card details).</li>
    <li><strong>Device &amp; Usage Data:</strong> IP address, browser type, pages visited, and cookies to improve your experience.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">2. How We Use Your Information</h3>
  <ul>
    <li>To process and deliver your orders accurately.</li>
    <li>To send order confirmation, delivery updates, and customer support messages via SMS or phone.</li>
    <li>To improve our website, product offerings, and customer experience.</li>
    <li>To prevent fraud and ensure the security of our platform.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">3. Information Sharing</h3>
  <p>We do <strong>not</strong> sell, rent, or trade your personal information to third parties. We only share your details with:</p>
  <ul>
    <li>Delivery partners (courier services) solely for the purpose of delivering your order.</li>
    <li>Payment gateways (e.g., bKash, Nagad) to process transactions securely.</li>
    <li>Law enforcement authorities if required by law.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">4. Cookies</h3>
  <p>We use cookies to remember your cart items, login session, and browsing preferences. You can disable cookies in your browser settings, though this may affect website functionality.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">5. Data Security</h3>
  <p>We implement appropriate technical measures to protect your personal data from unauthorized access, alteration, or disclosure. However, no internet transmission is 100% secure, so we encourage you to use a strong password and keep your account credentials safe.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">6. Your Rights</h3>
  <ul>
    <li>You may request access to or correction of your personal data at any time.</li>
    <li>You may request deletion of your account and associated data by contacting us.</li>
    <li>You may opt out of promotional messages by informing our support team.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">7. Contact Us</h3>
  <p>If you have any questions about this Privacy Policy, please contact us:</p>
  <p>&#128222; <strong>Hotline:</strong> +8801615406040<br>
  &#127760; <strong>Website:</strong> Organic Shop</p>

  <p style="margin-top:20px; font-size:13px; color:#888;">This policy was last updated on April 2026. We reserve the right to update it at any time.</p>
</div>
HTML;

// ──────────────────────────────────────────────────────────────────────────────
$termsConditions = <<<'HTML'
<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 900px; margin: auto;">

  <p>Welcome to <strong>Organic Shop</strong>. By accessing or purchasing from our website, you agree to comply with and be bound by the following Terms &amp; Conditions. Please read them carefully before placing an order.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">1. General</h3>
  <ul>
    <li>These terms apply to all users and customers of Organic Shop.</li>
    <li>We reserve the right to update these terms at any time without prior notice.</li>
    <li>Continued use of our website constitutes acceptance of the updated terms.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">2. Products</h3>
  <ul>
    <li>All products listed on our website are 100% natural and organic, sourced from trusted farms and suppliers.</li>
    <li>Product images and descriptions are for reference purposes. Actual product appearance may vary slightly due to natural variation.</li>
    <li>We reserve the right to discontinue or modify products at any time.</li>
    <li>Prices are subject to change without prior notice.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">3. Ordering</h3>
  <ul>
    <li>By placing an order, you confirm that you are at least 18 years of age and legally capable of entering into a purchase agreement.</li>
    <li>Orders are confirmed only after receiving an SMS or phone confirmation from our team.</li>
    <li>We reserve the right to cancel any order at our discretion (e.g., due to stock unavailability or payment issues).</li>
    <li>Please place orders only when you are 100% sure about the product and quantity.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">4. Payment</h3>
  <ul>
    <li>We accept Cash on Delivery (COD), bKash, Nagad, and other available payment methods.</li>
    <li>Advance bKash/Nagad payment is eligible for a <strong>5% discount</strong> on your order total.</li>
    <li>No advance payment is required for Cash on Delivery orders.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">5. Intellectual Property</h3>
  <p>All content on this website — including text, images, logos, and design — is the property of Organic Shop and may not be copied, reproduced, or distributed without written permission.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">6. Limitation of Liability</h3>
  <p>Organic Shop is not liable for any indirect, incidental, or consequential damages arising from the use of our products or website. Our total liability shall not exceed the amount paid for the specific order in question.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">7. Governing Law</h3>
  <p>These Terms &amp; Conditions are governed by the laws of the People&#39;s Republic of Bangladesh.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">8. Contact</h3>
  <p>&#128222; <strong>Hotline:</strong> +8801615406040<br>
  For any questions or disputes, please contact us before taking any other action.</p>

  <p style="margin-top:20px; font-size:13px; color:#888;">Last updated: April 2026</p>
</div>
HTML;

// ──────────────────────────────────────────────────────────────────────────────
$returnPolicy = <<<'HTML'
<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 900px; margin: auto;">

  <p>At <strong>Organic Shop</strong>, we are committed to delivering fresh, high-quality organic products. Please read our return policy carefully before making a purchase.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">General Policy</h3>
  <p>Sold products are generally <strong>not returnable</strong> once delivered and accepted. However, we do accept returns or offer replacements in the following specific situations:</p>

  <h3 style="color:#3c7d17; margin-top:24px;">Eligible Return Cases</h3>
  <ul>
    <li><strong>Wrong Product Delivered:</strong> If you receive a product different from what you ordered, please contact us within 24 hours of delivery with a photo/video as proof.</li>
    <li><strong>Damaged During Delivery:</strong> If the product arrives visibly damaged due to courier mishandling, report it immediately at the time of delivery (before accepting the package).</li>
    <li><strong>Expired or Spoiled Product:</strong> If you receive a product that is expired or clearly spoiled upon delivery, notify us within 24 hours with photo evidence.</li>
    <li><strong>Quantity Mismatch:</strong> If the delivered quantity differs from what was confirmed in your order.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">Non-Returnable Situations</h3>
  <ul>
    <li>Change of mind after accepting the delivery.</li>
    <li>Products that match the description and photos but do not meet personal taste preferences.</li>
    <li>Partially used or opened products.</li>
    <li>Products reported after 24 hours of delivery without prior notice.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">How to Return</h3>
  <ol>
    <li>Call our hotline at <strong>+8801615406040</strong> within 24 hours of receiving your order.</li>
    <li>Describe the issue and provide photo or video evidence.</li>
    <li>Our team will review and confirm the return/replacement within 24&#8211;48 hours.</li>
    <li>Approved returns: you may exchange for a product of equal or greater value (paying the difference for higher-priced items). <strong>Exchanges for lower-priced items are not available.</strong></li>
    <li>Return courier charges are borne by the customer.</li>
  </ol>

  <h3 style="color:#3c7d17; margin-top:24px;">Refunds</h3>
  <p>Refunds are issued only when a replacement product is unavailable. Refunds are processed within 3&#8211;5 business days via the original payment method (bKash/Nagad/Bank Transfer).</p>

  <h3 style="color:#3c7d17; margin-top:24px;">Warranty Products</h3>
  <p>For products that come with a manufacturer warranty, we will facilitate the warranty claim process. In some cases, the brand may handle the service directly at their nearest service center.</p>

  <p>&#128222; <strong>Contact for Returns:</strong> +8801615406040</p>
  <p style="margin-top:20px; font-size:13px; color:#888;">Last updated: April 2026</p>
</div>
HTML;

// ──────────────────────────────────────────────────────────────────────────────
$deliveryRules = <<<'HTML'
<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 900px; margin: auto;">

  <p><strong>Organic Shop</strong> delivers fresh organic products to your doorstep across Bangladesh. Please review our delivery rules before placing your order.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">Delivery Coverage</h3>
  <ul>
    <li>We deliver <strong>nationwide across Bangladesh</strong> through trusted courier partners.</li>
    <li>Dhaka city deliveries may be faster than outside Dhaka (see timeframes below).</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">Delivery Timeframe</h3>
  <table style="width:100%; border-collapse:collapse; margin-top:10px;">
    <thead>
      <tr style="background-color:#3c7d17; color:white;">
        <th style="padding:10px; border:1px solid #ddd; text-align:left;">Location</th>
        <th style="padding:10px; border:1px solid #ddd; text-align:left;">Estimated Delivery</th>
      </tr>
    </thead>
    <tbody>
      <tr style="background:#f9f9f9;">
        <td style="padding:10px; border:1px solid #ddd;">Dhaka City</td>
        <td style="padding:10px; border:1px solid #ddd;">1&#8211;2 Business Days</td>
      </tr>
      <tr>
        <td style="padding:10px; border:1px solid #ddd;">Outside Dhaka (District Towns)</td>
        <td style="padding:10px; border:1px solid #ddd;">2&#8211;4 Business Days</td>
      </tr>
      <tr style="background:#f9f9f9;">
        <td style="padding:10px; border:1px solid #ddd;">Remote / Upazila Areas</td>
        <td style="padding:10px; border:1px solid #ddd;">3&#8211;5 Business Days</td>
      </tr>
    </tbody>
  </table>

  <h3 style="color:#3c7d17; margin-top:24px;">Delivery Charges</h3>
  <ul>
    <li>Delivery charge is fixed at <strong>&#2547;120</strong> per order (paid to the delivery person on receipt).</li>
    <li>For Cash on Delivery (COD) orders, payment is made in full (product price + delivery charge) to the courier.</li>
    <li>Orders with advance bKash/Nagad payment still pay &#2547;120 courier charge on delivery.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">Delivery Process</h3>
  <ol>
    <li>Once your order is confirmed, it will be packed and handed to our courier partner within 24 hours (on business days).</li>
    <li>You will receive an SMS with a tracking number once your parcel is dispatched.</li>
    <li>The delivery person will contact you before arriving.</li>
    <li>Please ensure someone is available at the delivery address to receive the parcel.</li>
  </ol>

  <h3 style="color:#3c7d17; margin-top:24px;">Important Rules</h3>
  <ul>
    <li><strong>Inspect before accepting:</strong> Please check your parcel in front of the delivery person. Damaged parcels should be refused and reported to us immediately.</li>
    <li><strong>If you are unavailable:</strong> The courier may attempt delivery up to 2 times. After that, the parcel may be returned and a re-delivery charge will apply.</li>
    <li><strong>Refused deliveries:</strong> If you refuse a confirmed order without a valid reason, you will be liable for the two-way courier charge (&#2547;240).</li>
    <li>We are not responsible for delays caused by natural disasters, strikes, or other force majeure events.</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:24px;">Track Your Order</h3>
  <p>You can track your order from the <a href="/customer/order-track" style="color:#3c7d17;"><strong>Track Order</strong></a> page using your order number and phone number.</p>

  <p>&#128222; <strong>For delivery inquiries:</strong> +8801615406040</p>
  <p style="margin-top:20px; font-size:13px; color:#888;">Last updated: April 2026</p>
</div>
HTML;

// ──────────────────────────────────────────────────────────────────────────────
$orderProcedure = <<<'HTML'
<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 900px; margin: auto;">

  <p>Ordering from <strong>Organic Shop</strong> is quick and easy. Follow the simple steps below to get fresh organic products delivered to your door.</p>

  <h3 style="color:#3c7d17; margin-top:24px;">Step-by-Step Order Process</h3>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">1</div>
    <div>
      <strong>Browse &amp; Select Products</strong><br>
      Visit our <a href="/shop" style="color:#3c7d17;">Shop</a> or browse by category. Click on any product to view details, ingredients, weight, and price. Add your desired items to the cart.
    </div>
  </div>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">2</div>
    <div>
      <strong>Review Your Cart</strong><br>
      Open your cart and review the selected items, quantities, and total price. You can add more products or remove items before proceeding.
    </div>
  </div>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">3</div>
    <div>
      <strong>Proceed to Checkout</strong><br>
      Click <strong>Checkout</strong>. Fill in your:
      <ul style="margin-top:6px;">
        <li>Full Name</li>
        <li>Phone Number (must be reachable)</li>
        <li>Full Delivery Address (Division, District, Upazila, Street)</li>
        <li>Any special note for the delivery</li>
      </ul>
    </div>
  </div>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">4</div>
    <div>
      <strong>Choose Payment Method</strong><br>
      Select your preferred payment option:
      <ul style="margin-top:6px;">
        <li>&#128181; <strong>Cash on Delivery (COD)</strong> &#8212; Pay when you receive your order. No advance needed.</li>
        <li>&#128241; <strong>bKash / Nagad</strong> &#8212; Pay in advance and enjoy a <strong>5% discount</strong> on your order total.</li>
      </ul>
    </div>
  </div>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">5</div>
    <div>
      <strong>Confirm Your Order</strong><br>
      Click <strong>Place Order</strong>. You will receive an on-screen confirmation. Our team will call or SMS you within a few hours to confirm your order details.
    </div>
  </div>

  <div style="display:flex; align-items:flex-start; margin-bottom:20px;">
    <div style="background:#3c7d17; color:white; border-radius:50%; min-width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; margin-right:16px; margin-top:4px;">6</div>
    <div>
      <strong>Wait for Delivery</strong><br>
      Your order will be packed and dispatched within 24 hours. Delivery takes <strong>3&#8211;5 business days</strong> across Bangladesh. You can track your order from the <a href="/customer/order-track" style="color:#3c7d17;"><strong>Track Order</strong></a> page.
    </div>
  </div>

  <h3 style="color:#3c7d17; margin-top:28px;">Tips for a Smooth Experience</h3>
  <ul>
    <li>Make sure your phone number is correct and reachable &#8212; we will call to confirm.</li>
    <li>Provide a complete delivery address including a nearby landmark for faster delivery.</li>
    <li>Place orders only when you are 100% ready to receive and pay.</li>
    <li>Take advantage of the <strong>5% advance payment discount</strong> to save money.</li>
    <li>Order multiple products in one go &#8212; same delivery charge applies!</li>
  </ul>

  <h3 style="color:#3c7d17; margin-top:28px;">Need Help?</h3>
  <p>Our customer support team is ready to assist you.<br>
  &#128222; <strong>Call / WhatsApp:</strong> +8801615406040</p>

  <p style="margin-top:20px; font-size:13px; color:#888;">Last updated: April 2026</p>
</div>
HTML;

// ──────────────────────────────────────────────────────────────────────────────
$updates = [
    7 => ['name' => 'Privacy Policy',    'title' => 'Privacy Policy',    'description' => $privacyPolicy],
    6 => ['name' => 'Terms & Conditions','title' => 'Terms & Conditions','description' => $termsConditions],
    5 => ['name' => 'Return Policy',     'title' => 'Return Policy',     'description' => $returnPolicy],
    3 => ['name' => 'Delivery Rules',    'title' => 'Delivery Rules',    'description' => $deliveryRules],
    2 => ['name' => 'Order procedure',   'title' => 'How to Order',      'description' => $orderProcedure],
];

$count = 0;
foreach ($updates as $id => $data) {
    $page = CreatePage::find($id);
    if (!$page) {
        echo "SKIP: No page with id=$id\n";
        continue;
    }
    $page->update([
        'name'        => $data['name'],
        'title'       => $data['title'],
        'description' => trim($data['description']),
    ]);
    echo "UPDATED [{$id}]: {$data['name']}\n";
    $count++;
}

echo "\nDone. $count page(s) updated.\n";
