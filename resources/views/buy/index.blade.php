<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy {{ $productTitle }} - nearX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            padding: 20px;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .container {
            max-width: 680px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .product-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .product-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.3;
            }
        }

        .product-image-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            z-index: 1;
        }

        .product-image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 20px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow-xl);
            background: white;
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-title {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .product-price {
            font-size: 42px;
            font-weight: 800;
            color: white;
            margin-top: 12px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-price::before {
            content: '₹';
            font-size: 32px;
            font-weight: 600;
            margin-right: 4px;
        }

        .form-section {
            padding: 40px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
            font-size: 24px;
        }

        .product-info-card {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
            transition: background 0.2s;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item:hover {
            background: rgba(99, 102, 241, 0.05);
            margin: 0 -12px;
            padding: 12px;
            border-radius: 8px;
        }

        .info-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            color: var(--primary);
            font-size: 16px;
        }

        .info-value {
            color: var(--gray-900);
            font-weight: 600;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 10px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary);
            font-size: 18px;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid var(--gray-200);
            padding: 14px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
            color: var(--gray-900);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-text {
            color: var(--gray-500);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-text i {
            font-size: 12px;
        }

        .payment-section {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 28px;
            margin-top: 8px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .payment-method {
            border: 2px solid var(--gray-200);
            border-radius: 14px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .payment-method:hover {
            border-color: var(--primary-light);
            background: rgba(99, 102, 241, 0.03);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .payment-method.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(99, 102, 241, 0.03) 100%);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .payment-method input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .payment-method-label {
            flex: 1;
            cursor: pointer;
        }

        .payment-method-label strong {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .payment-method-label small {
            display: block;
            color: var(--gray-500);
            font-size: 13px;
        }

        .payment-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-100);
            color: var(--primary);
            font-size: 24px;
        }

        .payment-method.selected .payment-icon {
            background: var(--primary);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 14px;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        .spinner-border-sm {
            width: 18px;
            height: 18px;
            border-width: 2px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 20px;
            font-size: 14px;
            box-shadow: var(--shadow);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid var(--warning);
        }

        .btn-close {
            opacity: 0.7;
        }

        .btn-close:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .form-section {
                padding: 24px;
            }

            .product-header {
                padding: 30px 20px;
            }

            .product-title {
                font-size: 24px;
            }

            .product-price {
                font-size: 36px;
            }

            .product-image {
                width: 140px;
                height: 140px;
            }
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Product Header -->
            <div class="product-header">
                <div class="product-image-wrapper">
                    <img src="{{ $productImage }}" alt="{{ $productTitle }}" class="product-image" onerror="this.style.display='none';">
                </div>
                <div class="product-title">{{ $productTitle }}</div>
                <div class="product-price">{{ number_format($price, 2) }}</div>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <form id="buyForm">
                    @csrf

                    <!-- Product Info -->
                    <div class="product-info-card">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-tag"></i>
                                Category
                            </span>
                            <span class="info-value">{{ $post->category->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-geo-alt"></i>
                                Location
                            </span>
                            <span class="info-value">{{ $post->address ?? 'Not specified' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-person"></i>
                                Seller
                            </span>
                            <span class="info-value">{{ $post->user->name ?? 'Unknown' }}</span>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-group">
                        <label for="address" class="form-label">
                            <i class="bi bi-geo-alt-fill"></i>
                            Delivery Address
                        </label>
                        <textarea
                            class="form-control"
                            id="address"
                            name="address"
                            rows="4"
                            placeholder="Enter your complete delivery address including street, city, state, and PIN code"
                            required></textarea>
                        <small class="form-text">
                            <i class="bi bi-info-circle"></i>
                            Please provide your complete address for accurate delivery
                        </small>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group">
                        <div class="section-title">
                            <i class="bi bi-credit-card-2-front"></i>
                            Payment Method
                        </div>
                        <div class="payment-section">
                            <div class="payment-methods">
                                <div class="payment-method" onclick="selectPaymentMethod('google_pay')">
                                    <input type="radio" name="payment_method" id="google_pay" value="google_pay" required>
                                    <div class="payment-icon">
                                        <i class="bi bi-google"></i>
                                    </div>
                                    <div class="payment-method-label">
                                        <label for="google_pay" style="cursor: pointer; margin: 0;">
                                            <strong>Google Pay</strong>
                                            <small>Fast and secure payment with Google Pay</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="payment-method" onclick="selectPaymentMethod('other')">
                                    <input type="radio" name="payment_method" id="other" value="other">
                                    <div class="payment-icon">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div class="payment-method-label">
                                        <label for="other" style="cursor: pointer; margin: 0;">
                                            <strong>Other Payment Methods</strong>
                                            <small>UPI, Credit/Debit Card, Net Banking</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Messages -->
                    <div id="alertContainer"></div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span id="submitText">Proceed to Payment</span>
                        <i class="bi bi-arrow-right"></i>
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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