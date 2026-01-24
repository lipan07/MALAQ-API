# Google Pay Integration Setup Guide

This guide will help you set up Google Pay for your nearX application.

## Prerequisites

1. A Google Pay Business Account
2. A Payment Gateway account (Razorpay, PayU, Stripe, etc.) - Recommended for India: **Razorpay** or **PayU**
3. Your business registration documents

---

## Step 1: Get Google Pay Merchant ID

### Option A: Direct Google Pay (For UPI in India)

1. Go to [Google Pay Business Console](https://pay.google.com/business/console)
2. Sign in with your Google account
3. Complete the business verification process:
   - Provide business details
   - Upload business registration documents
   - Verify your business address
4. Once approved, you'll receive your **Merchant ID** (format: `12345678901234567890`)

### Option B: Through Payment Gateway (Recommended)

If you're using a payment gateway like Razorpay or PayU, they will provide you with:
- **Gateway Merchant ID** (for tokenization)
- **Google Pay Merchant ID** (if they handle it)

---

## Step 2: Choose Your Payment Gateway

### For India - Recommended Options:

#### A. Razorpay (Recommended)
- **Website**: https://razorpay.com
- **Google Pay Support**: ✅ Yes
- **Setup Steps**:
  1. Sign up at https://razorpay.com
  2. Complete KYC verification
  3. Get your `Key ID` and `Key Secret`
  4. Enable Google Pay in Razorpay Dashboard
  5. Get your Gateway Merchant ID from Razorpay

#### B. PayU
- **Website**: https://payu.in
- **Google Pay Support**: ✅ Yes
- **Setup Steps**:
  1. Sign up at https://payu.in
  2. Complete merchant registration
  3. Get your `Merchant Key` and `Salt`
  4. Enable Google Pay in PayU Dashboard

#### C. Stripe
- **Website**: https://stripe.com
- **Google Pay Support**: ✅ Yes (International)
- **Setup Steps**:
  1. Sign up at https://stripe.com
  2. Get your `Publishable Key` and `Secret Key`
  3. Enable Google Pay in Stripe Dashboard

---

## Step 3: Configure Environment Variables

Add these variables to your `.env` file:

```env
# Google Pay Configuration
GOOGLE_PAY_MERCHANT_ID=your_merchant_id_here
GOOGLE_PAY_MERCHANT_NAME=nearX
GOOGLE_PAY_ENVIRONMENT=TEST
GOOGLE_PAY_GATEWAY=razorpay
GOOGLE_PAY_GATEWAY_MERCHANT_ID=your_gateway_merchant_id_here
```

### Environment Values:

- **GOOGLE_PAY_MERCHANT_ID**: Your Google Pay Merchant ID (from Step 1)
- **GOOGLE_PAY_MERCHANT_NAME**: Your business name (default: "nearX")
- **GOOGLE_PAY_ENVIRONMENT**: 
  - `TEST` - For testing (use during development)
  - `PRODUCTION` - For live payments (use after testing)
- **GOOGLE_PAY_GATEWAY**: Your payment gateway name
  - `razorpay` - For Razorpay
  - `payu` - For PayU
  - `stripe` - For Stripe
  - `example` - For testing without gateway
- **GOOGLE_PAY_GATEWAY_MERCHANT_ID**: Your gateway's merchant ID

---

## Step 4: Example Configuration

### Example 1: Razorpay Setup

```env
GOOGLE_PAY_MERCHANT_ID=12345678901234567890
GOOGLE_PAY_MERCHANT_NAME=nearX
GOOGLE_PAY_ENVIRONMENT=TEST
GOOGLE_PAY_GATEWAY=razorpay
GOOGLE_PAY_GATEWAY_MERCHANT_ID=rzp_test_xxxxxxxxxxxx
```

### Example 2: PayU Setup

```env
GOOGLE_PAY_MERCHANT_ID=12345678901234567890
GOOGLE_PAY_MERCHANT_NAME=nearX
GOOGLE_PAY_ENVIRONMENT=TEST
GOOGLE_PAY_GATEWAY=payu
GOOGLE_PAY_GATEWAY_MERCHANT_ID=your_payu_merchant_key
```

---

## Step 5: Testing

### Test Mode Setup:

1. Set `GOOGLE_PAY_ENVIRONMENT=TEST` in your `.env`
2. Use test credentials from your payment gateway
3. Test with Google Pay test cards (provided by your gateway)

### Test Cards (Razorpay Example):

- **Card Number**: 4111 1111 1111 1111
- **CVV**: Any 3 digits
- **Expiry**: Any future date

---

## Step 6: Production Setup

### Before Going Live:

1. ✅ Complete all KYC/verification with Google Pay
2. ✅ Complete verification with your payment gateway
3. ✅ Test thoroughly in TEST mode
4. ✅ Set `GOOGLE_PAY_ENVIRONMENT=PRODUCTION`
5. ✅ Update with production credentials
6. ✅ Enable Google Pay in your payment gateway dashboard

---

## Step 7: Backend Integration

The payment data from Google Pay will be sent to your backend in the `processPayment` method in `BuyController.php`.

You need to:

1. **Decrypt the payment token** (using your gateway's SDK)
2. **Verify the payment** with your gateway
3. **Create an order record** in your database
4. **Update post status** if needed
5. **Send confirmation notifications**

### Example Backend Integration (Razorpay):

```php
use Razorpay\Api\Api;

public function processPayment(Request $request, $id)
{
    // ... validation ...
    
    if ($request->payment_method === 'google_pay' && isset($request->payment_data)) {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        
        // Verify payment with Razorpay
        $payment = $api->payment->fetch($request->payment_data['id']);
        
        if ($payment->status === 'captured') {
            // Payment successful
            // Create order, update post, send notifications
        }
    }
}
```

---

## Step 8: Required Keys Summary

### For Google Pay Direct:
- ✅ Google Pay Merchant ID

### For Payment Gateway Integration:
- ✅ Google Pay Merchant ID (optional if gateway handles it)
- ✅ Gateway Merchant ID
- ✅ Gateway API Keys (Key ID, Secret Key, etc.)

---

## Troubleshooting

### Common Issues:

1. **"Merchant ID not found"**
   - Verify your `GOOGLE_PAY_MERCHANT_ID` is correct
   - Ensure it's activated in Google Pay Business Console

2. **"Payment gateway error"**
   - Check your gateway credentials
   - Verify Google Pay is enabled in gateway dashboard
   - Ensure you're using correct environment (TEST vs PRODUCTION)

3. **"Google Pay not available"**
   - Check if user has Google Pay installed
   - Verify browser/device compatibility
   - Check if you're using HTTPS (required for production)

---

## Additional Resources

- [Google Pay API Documentation](https://developers.google.com/pay/api/web/overview)
- [Razorpay Google Pay Integration](https://razorpay.com/docs/payments/payment-gateways/google-pay/)
- [PayU Google Pay Integration](https://devguide.payu.in/google-pay/)
- [Stripe Google Pay Integration](https://stripe.com/docs/payments/google-pay)

---

## Support

If you need help:
1. Check your payment gateway's support documentation
2. Contact your payment gateway's support team
3. Review Google Pay Business Console for merchant-specific issues

---

**Note**: Always test thoroughly in TEST mode before going to PRODUCTION!
