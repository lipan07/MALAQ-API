<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy {{ $productTitle }} - nearX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0984e3;
            --success-color: #00b894;
            --danger-color: #d63031;
            --warning-color: #fdcb6e;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background: white;
        }

        .product-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .product-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
            margin: 0 auto 20px;
            display: block;
            border: 4px solid white;
        }

        .product-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }

        .form-section {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(9, 132, 227, 0.25);
        }

        .payment-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-top: 20px;
        }

        .payment-method {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: #e3f2fd;
        }

        .payment-method input[type="radio"] {
            margin-right: 10px;
        }

        .google-pay-button {
            background: #000;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .google-pay-button:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .google-pay-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background: #0770c4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(9, 132, 227, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        .product-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Product Header -->
            <div class="product-header">
                <img src="{{ $productImage }}" alt="{{ $productTitle }}" class="product-image" onerror="this.style.display='none';">
                <div class="product-title">{{ $productTitle }}</div>
                <div class="product-price">₹{{ number_format($price, 2) }}</div>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <form id="buyForm">
                    @csrf

                    <!-- Product Info -->
                    <div class="product-info">
                        <div class="info-item">
                            <span class="info-label">Category:</span>
                            <span class="info-value">{{ $post->category->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Location:</span>
                            <span class="info-value">{{ $post->address ?? 'Not specified' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Seller:</span>
                            <span class="info-value">{{ $post->user->name ?? 'Unknown' }}</span>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="mb-4">
                        <label for="address" class="form-label">
                            <i class="bi bi-geo-alt"></i> Delivery Address *
                        </label>
                        <textarea
                            class="form-control"
                            id="address"
                            name="address"
                            rows="4"
                            placeholder="Enter your complete delivery address"
                            required></textarea>
                        <small class="text-muted">Please provide your complete address including street, city, state, and PIN code</small>
                    </div>

                    <!-- Payment Method -->
                    <div class="payment-section">
                        <label class="form-label mb-3">
                            <i class="bi bi-credit-card"></i> Payment Method *
                        </label>

                        <div class="payment-method" onclick="selectPaymentMethod('google_pay')">
                            <input type="radio" name="payment_method" id="google_pay" value="google_pay" required>
                            <label for="google_pay" style="cursor: pointer; margin: 0;">
                                <strong>Google Pay</strong>
                                <br>
                                <small class="text-muted">Pay securely with Google Pay</small>
                            </label>
                        </div>

                        <div class="payment-method" onclick="selectPaymentMethod('other')">
                            <input type="radio" name="payment_method" id="other" value="other">
                            <label for="other" style="cursor: pointer; margin: 0;">
                                <strong>Other Payment Methods</strong>
                                <br>
                                <small class="text-muted">UPI, Credit/Debit Card, Net Banking</small>
                            </label>
                        </div>
                    </div>

                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span id="submitText">Proceed to Payment</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function selectPaymentMethod(method) {
            document.getElementById(method).checked = true;
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        // Initialize Google Pay button
        let paymentsClient = null;

        function initGooglePay() {
            if (typeof google === 'undefined' || !google.payments) {
                console.warn('Google Pay API not loaded');
                return;
            }

            paymentsClient = new google.payments.api.PaymentsClient({
                environment: 'PRODUCTION' // or 'TEST' for testing
            });
        }

        // Form submission
        document.getElementById('buyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const address = formData.get('address');
            const paymentMethod = formData.get('payment_method');

            if (!address || !paymentMethod) {
                showAlert('Please fill in all required fields', 'danger');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loading = submitBtn.querySelector('.loading');

            // Show loading
            submitBtn.disabled = true;
            loading.classList.add('show');
            submitText.textContent = 'Processing...';

            try {
                if (paymentMethod === 'google_pay') {
                    // Process Google Pay payment
                    await processGooglePay();
                } else {
                    // Process other payment methods
                    await processPayment(address, paymentMethod);
                }
            } catch (error) {
                console.error('Payment error:', error);
                showAlert('Payment processing failed. Please try again.', 'danger');
                submitBtn.disabled = false;
                loading.classList.remove('show');
                submitText.textContent = 'Proceed to Payment';
            }
        });

        async function processGooglePay() {
            const address = document.getElementById('address').value;

            try {
                // Check if Google Pay is available
                if (paymentsClient && paymentsClient.isReadyToPay) {
                    const isReady = await paymentsClient.isReadyToPay({
                        apiVersion: 2,
                        apiVersionMinor: 0,
                        allowedPaymentMethods: [{
                            type: 'CARD',
                            parameters: {
                                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                                allowedCardNetworks: ['MASTERCARD', 'VISA']
                            }
                        }]
                    });

                    if (isReady.result) {
                        // Create payment request
                        const paymentRequest = {
                            apiVersion: 2,
                            apiVersionMinor: 0,
                            merchantInfo: {
                                merchantId: 'YOUR_MERCHANT_ID', // Replace with your Google Pay merchant ID
                                merchantName: 'nearX'
                            },
                            allowedPaymentMethods: [{
                                type: 'CARD',
                                parameters: {
                                    allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                                    allowedCardNetworks: ['MASTERCARD', 'VISA']
                                },
                                tokenizationSpecification: {
                                    type: 'PAYMENT_GATEWAY',
                                    parameters: {
                                        gateway: 'example',
                                        gatewayMerchantId: 'exampleGatewayMerchantId'
                                    }
                                }
                            }],
                            transactionInfo: {
                                totalPriceStatus: 'FINAL',
                                totalPriceLabel: 'Total',
                                totalPrice: '{{ number_format($price, 2, ".", "") }}',
                                currencyCode: 'INR',
                                countryCode: 'IN'
                            }
                        };

                        // Load payment data
                        const paymentData = await paymentsClient.loadPaymentData(paymentRequest);

                        // Process payment with backend
                        await processPaymentWithToken(address, 'google_pay', paymentData);
                    } else {
                        // Fallback to regular payment processing
                        await processPayment(address, 'google_pay');
                    }
                } else {
                    // Google Pay not available, use regular payment
                    await processPayment(address, 'google_pay');
                }
            } catch (error) {
                console.error('Google Pay error:', error);
                // Fallback to regular payment processing
                await processPayment(address, 'google_pay');
            }
        }

        async function processPaymentWithToken(address, paymentMethod, paymentData) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch(`/buy/{{ $post->id }}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    address: address,
                    payment_method: paymentMethod,
                    payment_data: paymentData
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Payment successful! Your order has been placed. Order ID: ' + data.order_id, 'success');

                // Reset form
                document.getElementById('buyForm').reset();
                document.querySelectorAll('.payment-method').forEach(el => {
                    el.classList.remove('selected');
                });

                // Reset button
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const loading = submitBtn.querySelector('.loading');
                submitBtn.disabled = false;
                loading.classList.remove('show');
                submitText.textContent = 'Proceed to Payment';

                setTimeout(() => {
                    // Try to redirect to app, fallback to success message
                    try {
                        window.location.href = 'reuseapp://order/' + data.order_id;
                    } catch (e) {
                        // If app not installed, show success message
                        showAlert('Order placed successfully! You can view it in the app.', 'success');
                    }
                }, 2000);
            } else {
                throw new Error(data.message || 'Payment failed');
            }
        }

        async function processPayment(address, paymentMethod) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch(`/buy/{{ $post->id }}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    address: address,
                    payment_method: paymentMethod
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Payment successful! Your order has been placed. Order ID: ' + data.order_id, 'success');

                // Reset form
                document.getElementById('buyForm').reset();
                document.querySelectorAll('.payment-method').forEach(el => {
                    el.classList.remove('selected');
                });

                // Reset button
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const loading = submitBtn.querySelector('.loading');
                submitBtn.disabled = false;
                loading.classList.remove('show');
                submitText.textContent = 'Proceed to Payment';

                setTimeout(() => {
                    // Try to redirect to app, fallback to success message
                    try {
                        window.location.href = 'reuseapp://order/' + data.order_id;
                    } catch (e) {
                        // If app not installed, show success message
                        showAlert('Order placed successfully! You can view it in the app.', 'success');
                    }
                }, 2000);
            } else {
                throw new Error(data.message || 'Payment failed');
            }
        }

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Load Google Pay API
        function loadGooglePay() {
            if (typeof google === 'undefined' || !google.payments) {
                const script = document.createElement('script');
                script.src = 'https://pay.google.com/gp/p/pay.js';
                script.async = true;
                script.onload = function() {
                    if (typeof google !== 'undefined' && google.payments) {
                        initGooglePay();
                    }
                };
                script.onerror = function() {
                    console.warn('Google Pay API failed to load. Using fallback payment method.');
                };
                document.head.appendChild(script);
            } else {
                initGooglePay();
            }
        }

        // Load Google Pay on page load
        loadGooglePay();
    </script>
</body>

</html>