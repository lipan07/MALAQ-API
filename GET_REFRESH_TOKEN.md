# How to Get YouTube Refresh Token

## Quick Method: OAuth Playground

### Step 1: Get Your Client Secret

1. Go to: https://console.cloud.google.com/apis/credentials
2. Find your OAuth 2.0 Client ID (Web application)
3. Click to view details
4. Copy the **Client Secret** (not the Client ID)

### Step 2: Use OAuth Playground

1. Go to: https://developers.google.com/oauthplayground/
2. Click the **gear icon (⚙️)** in the top right corner
3. Check **"Use your own OAuth credentials"**
4. Enter:
   - **OAuth Client ID:** `965583527872-ko5s12ge7nj8d06rl748v3r80pkom4u9.apps.googleusercontent.com` (or your Web Client ID)
   - **OAuth Client secret:** Your Client Secret (from Step 1)
5. Click **"Close"**

### Step 3: Authorize

1. In the left panel, scroll to **"YouTube Data API v3"**
2. Expand it
3. Check these scopes:
   - ✅ `https://www.googleapis.com/auth/youtube.upload`
   - ✅ `https://www.googleapis.com/auth/youtube`
4. Click **"Authorize APIs"** button
5. Sign in with **YOUR Google account** (the one with your YouTube channel)
6. Click **"Allow"** to grant permissions

### Step 4: Get Refresh Token

1. After authorization, you'll see an authorization code
2. Click **"Exchange authorization code for tokens"** button
3. You'll see tokens in the right panel
4. **Copy the "Refresh token"** value (this is what you need!)

### Step 5: Add to .env

Add to your Laravel `.env` file:

```env
YOUTUBE_CLIENT_ID=965583527872-ko5s12ge7nj8d06rl748v3r80pkom4u9.apps.googleusercontent.com
YOUTUBE_CLIENT_SECRET=your-client-secret-here
YOUTUBE_REFRESH_TOKEN=your-refresh-token-here
```

## ✅ That's It!

After adding these to `.env`, all video uploads from users will go to YOUR YouTube channel!

## 🔍 Verify It Works

1. Upload a test video from the app
2. Check your YouTube channel (videos will be unlisted)
3. All future uploads will go to your channel

