# Twilio SMS Integration for OTP

## Overview

The OTP system now integrates with Twilio for sending SMS messages. This provides real SMS delivery to users' phone numbers.

## Setup

### 1. Environment Variables

Add these variables to your `.env` file:

```env
TWILIO_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_NUMBER=your_twilio_phone_number
```

### 2. Twilio Configuration

- Sign up for a Twilio account at https://www.twilio.com
- Get your Account SID and Auth Token from the Twilio Console
- Purchase a phone number for sending SMS
- Add the credentials to your `.env` file

## API Endpoints

### Send OTP

```
POST /api/send-otp
Content-Type: application/json

{
    "phoneNumber": "9876543210",
    "countryCode": "+91"
}
```

### Resend OTP

```
POST /api/resend-otp
Content-Type: application/json

{
    "phoneNumber": "9876543210",
    "countryCode": "+91"
}
```

### Test SMS (Debug Only)

```
POST /api/test-sms
Content-Type: application/json

{
    "phoneNumber": "9876543210",
    "countryCode": "+91"
}
```

## Features

### Progressive Resend Timer

- 1st resend: 2 minutes wait
- 2nd resend: 5 minutes wait
- 3rd resend: 10 minutes wait
- 4th resend: 15 minutes wait
- 5th resend: 20 minutes wait
- After 5 attempts: User must wait or contact support

### Country Code Support

- Supports different country codes (default: +91 for India)
- Frontend sends country code with phone number
- Backend formats phone number correctly for Twilio

### Error Handling

- Fallback to logging OTP in debug mode if SMS fails
- Proper error messages for failed SMS delivery
- Rate limiting and attempt tracking

### SMS Message Format

```
Your Reuse app OTP is: 1234. This OTP is valid for 10 minutes. Do not share this code with anyone.
```

## Testing

### Development Mode

- In debug mode, if SMS fails, OTP is logged and returned in response
- Use `/api/test-sms` endpoint for testing SMS functionality
- Check Laravel logs for SMS delivery status

### Production Mode

- SMS failures return error responses
- OTP is not returned in API responses
- All SMS attempts are logged

## Troubleshooting

### Common Issues

1. **Invalid Twilio credentials**: Check your `.env` file
2. **Phone number format**: Ensure phone numbers are 10 digits without country code
3. **Twilio account limits**: Check your Twilio account balance and limits
4. **SMS delivery failures**: Check Twilio logs in their console

### Debug Steps

1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Test with `/api/test-sms` endpoint
3. Verify Twilio credentials in Twilio Console
4. Check phone number format and country code

## Security Notes

- OTP is only returned in API responses in debug mode
- Rate limiting prevents SMS spam
- Progressive timer increases wait time with each resend
- Maximum 5 resend attempts per session
