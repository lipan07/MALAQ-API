# YouTube Server-Side Upload Setup

This guide explains how to set up server-side YouTube uploads so all videos go to YOUR YouTube channel, not the user's channel.

## ✅ What's Been Created

1. **YouTubeService** (`app/Services/YouTubeService.php`)
   - Handles YouTube uploads using your credentials
   - All videos upload to your channel

2. **YouTubeController** (`app/Http/Controllers/YouTubeController.php`)
   - API endpoint: `POST /api/youtube/upload`
   - Handles video upload requests from the app

3. **Updated React Native App**
   - Now uploads videos to your server
   - Server uploads to your YouTube channel

## 📋 Setup Steps

### Step 1: Get Your YouTube OAuth Credentials

You need to get a **refresh token** for your YouTube channel. This is a one-time setup.

#### Option A: Using OAuth Playground (Easiest)

1. Go to: https://developers.google.com/oauthplayground/
2. Click the gear icon (⚙️) in top right
3. Check **"Use your own OAuth credentials"**
4. Enter:
   - **OAuth Client ID:** Your Web Client ID
   - **OAuth Client secret:** Your Client Secret (get from Google Cloud Console)
5. In the left panel, find **"YouTube Data API v3"**
6. Select these scopes:
   - `https://www.googleapis.com/auth/youtube.upload`
   - `https://www.googleapis.com/auth/youtube`
7. Click **"Authorize APIs"**
8. Sign in with **YOUR Google account** (the one with your YouTube channel)
9. Click **"Allow"**
10. Click **"Exchange authorization code for tokens"**
11. Copy the **"Refresh token"** value

#### Option B: Using Your App's OAuth Flow

1. Add to your `.env`:
   ```env
   YOUTUBE_REDIRECT_URI=https://yourdomain.com/api/youtube/callback
   ```

2. Visit: `https://yourdomain.com/api/youtube/auth-url`
3. Authorize with your Google account
4. Copy the refresh token from the response

### Step 2: Add Credentials to .env

Add these to your Laravel `.env` file:

```env
# YouTube API Credentials (for server-side uploads)
YOUTUBE_CLIENT_ID=your-web-client-id.apps.googleusercontent.com
YOUTUBE_CLIENT_SECRET=your-client-secret
YOUTUBE_REFRESH_TOKEN=your-refresh-token-here
YOUTUBE_REDIRECT_URI=https://yourdomain.com/api/youtube/callback
```

**Important:**
- Use the **Web application** Client ID and Client Secret
- The refresh token is the one you got from OAuth Playground
- Make sure the Google account has a YouTube channel

### Step 3: Verify Setup

1. **Check YouTube Data API v3 is enabled:**
   - Go to: https://console.cloud.google.com/apis/library
   - Search "YouTube Data API v3"
   - Should show "API enabled"

2. **Test the endpoint:**
   ```bash
   # Using curl (replace with your token)
   curl -X POST https://yourdomain.com/api/youtube/upload \
     -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
     -F "video=@/path/to/video.mp4" \
     -F "title=Test Video" \
     -F "privacy=unlisted"
   ```

## 🔧 How It Works

1. **User selects video** in the app
2. **App uploads video** to your Laravel server (`/api/youtube/upload`)
3. **Server receives video** and temporarily stores it
4. **Server uploads to YouTube** using YOUR credentials
5. **Server returns YouTube URL** to the app
6. **App saves URL** in the property form

## 📱 React Native App Changes

The app now:
- ✅ Uploads videos to your server (not directly to YouTube)
- ✅ No user sign-in required
- ✅ All videos go to your YouTube channel
- ✅ Returns YouTube URL for the property

## 🔒 Security Notes

1. **Keep credentials secure:**
   - Never commit `.env` file
   - Use environment variables
   - Rotate refresh token if compromised

2. **Rate Limits:**
   - YouTube API has daily quotas
   - Monitor usage in Google Cloud Console
   - Default: 10,000 units/day (1,600 per upload)

3. **File Size:**
   - Current limit: 100MB per video
   - Adjust in `YouTubeController.php` if needed

## 🐛 Troubleshooting

### Error: "Invalid refresh token"
- Make sure refresh token is correct in `.env`
- Token might have expired (get a new one)
- Check that Client ID and Secret are correct

### Error: "YouTube channel required"
- The Google account needs a YouTube channel
- Create one at youtube.com

### Error: "API not enabled"
- Enable YouTube Data API v3 in Google Cloud Console

### Error: "Unauthorized"
- Check OAuth consent screen is configured
- Verify scopes are added
- Check refresh token is valid

## ✅ Testing

1. **Upload a test video** from the app
2. **Check your YouTube channel** (videos should be unlisted)
3. **Verify the URL** is returned to the app
4. **Check the property form** has the video URL

## 📊 Monitoring

- Check Laravel logs: `storage/logs/laravel.log`
- Monitor YouTube API usage in Google Cloud Console
- Check video uploads in your YouTube Studio

## 🎯 Next Steps

1. Add refresh token to `.env`
2. Test video upload from app
3. Verify video appears on your YouTube channel
4. All user videos will now go to your channel!

