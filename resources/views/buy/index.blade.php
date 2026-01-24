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
            background: #ffffff;
            min-height: 100vh;
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container-fluid {
            padding: 0;
            max-width: 100%;
        }

        .product-image-section {
            width: 100%;
            position: relative;
            background: var(--gray-50);
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        .product-image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
            padding: 30px;
            color: white;
        }

        .product-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .product-price {
            font-size: 36px;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .product-price::before {
            content: '₹';
            font-size: 28px;
            font-weight: 600;
            margin-right: 4px;
        }

        .content-section {
            padding: 40px 24px;
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-200);
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
            padding: 14px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--gray-600);
            font-weight: 500;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-label i {
            color: var(--primary);
            font-size: 18px;
        }

        .info-value {
            color: var(--gray-900);
            font-weight: 600;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .row>[class*="col-"] {
            padding-left: 12px;
            padding-right: 12px;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 12px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label i {
            color: var(--primary);
            font-size: 20px;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid var(--gray-200);
            padding: 16px 20px;
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
            min-height: 140px;
        }

        .form-text {
            color: var(--gray-500);
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-text i {
            font-size: 12px;
        }

        .payment-section {
            background: var(--gray-50);
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
            padding: 22px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            display: flex;
            align-items: center;
            gap: 18px;
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
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .payment-method-label {
            flex: 1;
            cursor: pointer;
        }

        .payment-method-label strong {
            display: block;
            font-size: 17px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 5px;
        }

        .payment-method-label small {
            display: block;
            color: var(--gray-500);
            font-size: 14px;
        }

        .payment-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-100);
            color: var(--primary);
            font-size: 26px;
        }

        .payment-method.selected .payment-icon {
            background: var(--primary);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 14px;
            padding: 18px 32px;
            font-size: 17px;
            font-weight: 600;
            width: 100%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 12px;
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
            width: 20px;
            height: 20px;
            border-width: 2px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 18px 22px;
            margin-bottom: 24px;
            font-size: 15px;
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
            .product-image {
                height: 300px;
            }

            .product-image-overlay {
                padding: 20px;
            }

            .product-title {
                font-size: 26px;
            }

            .product-price {
                font-size: 30px;
            }

            .content-section {
                padding: 30px 20px;
            }

            .section-title {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .product-image {
                height: 250px;
            }

            .product-title {
                font-size: 22px;
            }

            .product-price {
                font-size: 26px;
            }

            .content-section {
                padding: 24px 16px;
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
    <div class="container-fluid">
        <!-- Full Width Product Image -->
        <div class="product-image-section">
            <img src="{{ $productImage }}" alt="{{ $productTitle }}" class="product-image" onerror="this.style.display='none';">
            <div class="product-image-overlay">
                <div class="product-title">{{ $productTitle }}</div>
                <div class="product-price">{{ number_format($price, 2) }}</div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <form id="buyForm">
                @csrf

                <!-- Product Info -->
                <div class="section-title">
                    <i class="bi bi-info-circle"></i>
                    Product Details
                </div>
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
                        <span class="info-value">{{ !empty($post->address) ? $post->address : 'Not specified' }}</span>
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
                <div class="section-title">
                    <i class="bi bi-truck"></i>
                    Delivery Information
                </div>

                <div class="form-group">
                    <label for="street_address" class="form-label">
                        <i class="bi bi-house-door"></i>
                        Street Address
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="street_address"
                        name="street_address"
                        placeholder="Enter your street address, building name, apartment number"
                        required>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="city" class="form-label">
                                <i class="bi bi-building"></i>
                                City
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="city"
                                name="city"
                                placeholder="Enter your city"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pin_code" class="form-label">
                                <i class="bi bi-mailbox"></i>
                                PIN Code
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="pin_code"
                                name="pin_code"
                                placeholder="PIN Code"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">
                        <i class="bi bi-globe"></i>
                        Country
                    </label>
                    <select
                        class="form-control form-select"
                        id="country"
                        name="country"
                        required>
                        <option value="India" selected>India</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Australia">Australia</option>
                        <option value="Germany">Germany</option>
                        <option value="France">France</option>
                        <option value="Japan">Japan</option>
                        <option value="China">China</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Payment Method -->
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
                environment: '{{ config("services.google_pay.environment", "TEST") }}' // TEST or PRODUCTION
            });
        }

        // Form submission
        document.getElementById('buyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const streetAddress = formData.get('street_address');
            const city = formData.get('city');
            const pinCode = formData.get('pin_code');
            const country = formData.get('country');
            const paymentMethod = formData.get('payment_method');

            if (!streetAddress || !city || !pinCode || !country || !paymentMethod) {
                showAlert('Please fill in all required fields', 'danger');
                return;
            }

            // Combine address fields
            const address = {
                street_address: streetAddress,
                city: city,
                pin_code: pinCode,
                country: country
            };

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
                    await processGooglePay(address);
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

        async function processGooglePay(address) {

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
                                merchantId: '{{ config("services.google_pay.merchant_id", "") }}',
                                merchantName: '{{ config("services.google_pay.merchant_name", "nearX") }}'
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
                                        gateway: '{{ config("services.google_pay.gateway", "razorpay") }}',
                                        gatewayMerchantId: '{{ config("services.google_pay.gateway_merchant_id", "") }}'
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
                    street_address: address.street_address,
                    city: address.city,
                    pin_code: address.pin_code,
                    country: address.country,
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
                    street_address: address.street_address,
                    city: address.city,
                    pin_code: address.pin_code,
                    country: address.country,
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

        // PIN code validation - only numbers
        document.getElementById('pin_code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>